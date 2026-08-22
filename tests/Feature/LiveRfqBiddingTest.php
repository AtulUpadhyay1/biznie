<?php

namespace Tests\Feature;

use App\Models\CommodityProduct;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductEnquiry;
use App\Models\User;
use App\Services\Rfq\LiveBiddingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The rules the live RFQ auction has to hold to, end to end.
 *
 * `DatabaseTransactions`, not `RefreshDatabase`: this project has no separate
 * test database configured, so the suite runs against the working one. Rolling
 * back is safe; migrating fresh would wipe it.
 */
class LiveRfqBiddingTest extends TestCase
{
    use DatabaseTransactions;

    private CommodityProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $product = CommodityProduct::first();
        if (! $product) {
            $this->markTestSkipped('No commodity product in the database to build an RFQ against.');
        }

        $this->product = $product;
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function makeUser(string $type): User
    {
        $user = new User();
        $user->name     = 'Test ' . $type . ' ' . uniqid();
        $user->type     = $type;
        $user->phone    = (string) random_int(7000000000, 9999999999);
        $user->email    = 'test-' . uniqid() . '@example.test';
        $user->password = bcrypt('secret123');
        $user->status   = 'active';
        $user->save();

        return $user;
    }

    /** A seller with an active listing for the product, loading out of `$city`. */
    private function makeSeller(string $city): User
    {
        $seller = $this->makeUser('seller');

        $listing = new SellerCommodityProduct();
        $listing->user_id              = $seller->id;
        $listing->commodity_product_id = $this->product->id;
        $listing->name                 = $this->product->name;
        $listing->status               = 'active';
        $listing->loading_address      = [['city' => $city, 'state' => 'Test State']];
        $listing->save();

        return $seller;
    }

    /** Raises an RFQ through the API as `$buyer` and returns its id. */
    private function createRfq(User $buyer): int
    {
        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v2/me/enquiries', [
            'commodity_product_id' => $this->product->id,
            'quantity'             => 30,
            'unit_label'           => 'Tons',
            'size_label'           => '2.5 mm',
            'delivery_city'        => 'Varanasi',
            'delivery_state'       => 'Uttar Pradesh',
            'required_by'          => 'As soon as possible',
        ]);

        $response->assertCreated();

        return $response->json('data.id');
    }

    /* ------------------------------------------------------------------ */
    /* Tests                                                               */
    /* ------------------------------------------------------------------ */

    public function test_creating_an_rfq_opens_bidding_and_invites_matching_sellers(): void
    {
        $buyer = $this->makeUser('customer');
        $this->makeSeller('Raipur');
        $this->makeSeller('Ranchi');

        $rfqId = $this->createRfq($buyer);
        $rfq   = ProductEnquiry::find($rfqId);

        $this->assertSame('live', $rfq->bidding_status);
        $this->assertTrue($rfq->bidding_ends_at->isFuture());

        // Quantity used to be appended to `description` as free text; it has to
        // be a real column for freight and totals to be computable.
        $this->assertEquals(30.0, (float) $rfq->quantity);
        $this->assertSame('Varanasi', $rfq->delivery_city);

        $this->assertGreaterThanOrEqual(
            2,
            SellerProductEnquiry::where('product_enquiries_id', $rfqId)->count(),
            'both listed sellers should have been invited without an admin touching it'
        );
    }

    public function test_rfq_references_are_unique_across_a_day(): void
    {
        $buyer = $this->makeUser('customer');
        $this->makeSeller('Raipur');

        $first  = ProductEnquiry::find($this->createRfq($buyer))->unique_id;
        $second = ProductEnquiry::find($this->createRfq($buyer))->unique_id;

        $this->assertNotSame($first, $second);
    }

    public function test_bids_are_ranked_by_for_price_not_ex_works(): void
    {
        $buyer   = $this->makeUser('customer');
        $cheap   = $this->makeSeller('Raipur');
        $dearer  = $this->makeSeller('Ranchi');

        $rfqId = $this->createRfq($buyer);
        $rfq   = ProductEnquiry::find($rfqId);
        $svc   = app(LiveBiddingService::class);

        $bidA = SellerProductEnquiry::where('product_enquiries_id', $rfqId)->where('user_id', $cheap->id)->first();
        $bidB = SellerProductEnquiry::where('product_enquiries_id', $rfqId)->where('user_id', $dearer->id)->first();

        // A wins on ex-works but loses once freight is added — the whole reason
        // the auction ranks on F.O.R.
        $svc->recordPrice(bid: $bidA, exWorks: 47000, freight: 6000, other: 0);
        $svc->recordPrice(bid: $bidB, exWorks: 48000, freight: 1000, other: 0);

        $board = $svc->leaderboard($rfq->refresh())->values();

        $this->assertSame(49000.0, $board[0]['for_price']);
        $this->assertSame(53000.0, $board[1]['for_price']);
        $this->assertEquals(49000.0, (float) $rfq->best_for_price);
    }

    public function test_a_seller_sees_rivals_anonymised_and_itself_marked(): void
    {
        $buyer  = $this->makeUser('customer');
        $viewer = $this->makeSeller('Raipur');
        $rival  = $this->makeSeller('Ranchi');

        $rfqId = $this->createRfq($buyer);
        $svc   = app(LiveBiddingService::class);

        foreach ([[$viewer, 48250], [$rival, 47800]] as [$seller, $price]) {
            Sanctum::actingAs($seller);
            $this->postJson("/api/v2/seller/rfqs/{$rfqId}/quote", ['ex_works_price' => $price])
                ->assertCreated();
        }

        Sanctum::actingAs($viewer);
        $response = $this->getJson("/api/v2/seller/rfqs/{$rfqId}/leaderboard")->assertOk();

        $rows = $response->json('data');
        $this->assertCount(2, $rows);

        // Cheapest first, rival anonymised, viewer's own row identified.
        $this->assertSame('Seller A', $rows[0]['label']);
        $this->assertFalse($rows[0]['is_me']);
        $this->assertSame('You (Current)', $rows[1]['label']);
        $this->assertTrue($rows[1]['is_me']);
        $this->assertSame(2, $response->json('meta.my_rank'));

        // Nothing in the payload should identify a competitor.
        $body = $response->getContent();
        $this->assertStringNotContainsString($rival->name, $body);
        $this->assertStringNotContainsString('Ranchi', $body);
    }

    public function test_the_buyer_is_shown_the_best_price_but_never_the_seller(): void
    {
        $buyer  = $this->makeUser('customer');
        $winner = $this->makeSeller('Ranchi');
        $this->makeSeller('Raipur');

        $rfqId = $this->createRfq($buyer);

        Sanctum::actingAs($winner);
        $this->postJson("/api/v2/seller/rfqs/{$rfqId}/quote", ['ex_works_price' => 47800])->assertCreated();

        Sanctum::actingAs($buyer);
        $response = $this->getJson("/api/v2/me/enquiries/{$rfqId}/live")->assertOk();

        $this->assertSame('best_found', $response->json('data.stage'));
        $this->assertNotNull($response->json('data.best_offer.for_price'));
        // assertEquals, not assertSame: a whole-rupee price serialises to JSON as
        // an int, so the decoded value is 47800 rather than 47800.0.
        $this->assertEquals(47800.0, $response->json('data.best_offer.ex_works_price'));

        // The breakup is public; who quoted it is not.
        $this->assertStringNotContainsString($winner->name, $response->getContent());
    }

    public function test_the_buyers_quotation_list_is_anonymous_and_ranked(): void
    {
        $buyer  = $this->makeUser('customer');
        $first  = $this->makeSeller('Ranchi');
        $second = $this->makeSeller('Raipur');

        $rfqId = $this->createRfq($buyer);

        foreach ([[$first, 47800], [$second, 48250]] as [$seller, $price]) {
            Sanctum::actingAs($seller);
            $this->postJson("/api/v2/seller/rfqs/{$rfqId}/quote", ['ex_works_price' => $price])
                ->assertCreated();
        }

        Sanctum::actingAs($buyer);
        $response = $this->getJson("/api/v2/me/enquiries/{$rfqId}/bidding")->assertOk();

        $rows = $response->json('data');
        $this->assertCount(2, $rows);

        // Ranked cheapest-first and labelled, never named.
        $this->assertSame('Offer A', $rows[0]['seller_label']);
        $this->assertSame(1, $rows[0]['rank']);
        $this->assertSame('Offer B', $rows[1]['seller_label']);
        $this->assertNull($rows[0]['seller'] ?? null);

        $body = $response->getContent();
        $this->assertStringNotContainsString($first->name, $body);
        $this->assertStringNotContainsString($second->name, $body);

        // Accepting still has to work, so the bid id must survive anonymisation.
        $this->assertArrayHasKey('id', $rows[0]);
    }

    public function test_an_uninvited_seller_cannot_read_or_bid_on_an_rfq(): void
    {
        $buyer = $this->makeUser('customer');
        $this->makeSeller('Raipur');

        $rfqId = $this->createRfq($buyer);

        // A seller with no listing for this product is never invited.
        $stranger = $this->makeUser('seller');
        Sanctum::actingAs($stranger);

        $this->getJson("/api/v2/seller/rfqs/{$rfqId}")->assertForbidden();
        $this->getJson("/api/v2/seller/rfqs/{$rfqId}/leaderboard")->assertForbidden();
        $this->postJson("/api/v2/seller/rfqs/{$rfqId}/quote", ['ex_works_price' => 1])->assertForbidden();
    }

    public function test_bidding_is_rejected_once_the_window_closes(): void
    {
        $buyer  = $this->makeUser('customer');
        $seller = $this->makeSeller('Raipur');

        $rfqId = $this->createRfq($buyer);
        ProductEnquiry::whereKey($rfqId)->update(['bidding_ends_at' => now()->subMinute()]);

        Sanctum::actingAs($seller);
        $this->postJson("/api/v2/seller/rfqs/{$rfqId}/quote", ['ex_works_price' => 47800])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Bidding has closed for this RFQ.');
    }

    public function test_extending_a_lapsed_auction_pushes_the_deadline_into_the_future(): void
    {
        $buyer = $this->makeUser('customer');
        $this->makeSeller('Raipur');

        $rfq = ProductEnquiry::find($this->createRfq($buyer));
        $rfq->bidding_ends_at = now()->subHour();
        $rfq->save();

        app(LiveBiddingService::class)->extend($rfq, 5);

        // Adding 5 minutes to a deadline an hour old would still be in the past.
        $this->assertTrue($rfq->bidding_ends_at->isFuture());
        $this->assertSame('live', $rfq->bidding_status);
    }

    public function test_a_manual_admin_price_lands_on_the_board_flagged_as_manual(): void
    {
        $buyer  = $this->makeUser('customer');
        $seller = $this->makeSeller('Ranchi');

        $rfqId = $this->createRfq($buyer);
        $rfq   = ProductEnquiry::find($rfqId);
        $svc   = app(LiveBiddingService::class);

        $bid = SellerProductEnquiry::where('product_enquiries_id', $rfqId)->where('user_id', $seller->id)->first();

        $svc->recordPrice(
            bid: $bid,
            exWorks: 47800,
            freight: 4400,
            other: 0,
            source: 'manual',
            adminId: 1,
            remarks: 'Quoted over the phone'
        );

        $row = $svc->leaderboard($rfq->refresh())->first();

        $this->assertSame('manual', $row['source']);
        $this->assertSame(52200.0, $row['for_price']);

        // The legacy order pipeline reads these two, so they must stay in step.
        $this->assertEquals(47800.0, (float) $bid->refresh()->base_price);
        $this->assertEquals(4400.0, (float) $bid->transport_price);
    }
}
