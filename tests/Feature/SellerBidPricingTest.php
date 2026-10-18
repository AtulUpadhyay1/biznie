<?php

namespace Tests\Feature;

use App\Http\Resources\Seller\ProductEnquiryDetailResource as LegacyQuotation;
use App\Models\CommodityProduct;
use App\Models\CommodityProductVariation;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;
use App\Models\SellerProductEnquiry;
use App\Models\User;
use App\Services\Rfq\EnquiryCreator;
use App\Services\Rfq\LiveBiddingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * A seller bids a base price, as in the legacy app, and the server works out
 * the Ex price from it: base + gauge difference + charges, then GST, size by
 * size. F.O.R (what the board ranks on) is that Ex price plus freight.
 */
class SellerBidPricingTest extends TestCase
{
    use DatabaseTransactions;

    private CommodityProduct $product;

    private User $seller;

    private SellerCommodityProduct $listing;

    private int $houseRowId;

    protected function setUp(): void
    {
        parent::setUp();

        $variation = CommodityProductVariation::whereHas('getCommodityProduct')->first();
        $product = $variation?->getCommodityProduct;
        if (! $product) {
            $this->markTestSkipped('Needs a commodity product with a variation to price sizes against.');
        }
        $this->product = $product;

        $this->seller = $this->makeUser('seller');

        $listing = new SellerCommodityProduct();
        $listing->user_id              = $this->seller->id;
        $listing->commodity_product_id = $product->id;
        $listing->name                 = $product->name;
        $listing->status               = 'active';
        $listing->request_status       = 'approved';
        $listing->base_price           = 46000;
        $listing->loading_charge       = 200;
        $listing->insurance_charge     = 50;
        $listing->quality_charge       = 0;
        $listing->tax_rate             = 18;
        $listing->gst                  = 18;
        $listing->load_within          = 7;
        // "GST 18%" duplicates the tax column and must not be added as ₹18.
        $listing->charge_name          = ['Cutting', 'GST 18%'];
        $listing->charge_price         = [100, 18];
        $listing->operator             = ['+', '+'];
        $listing->loading_address      = [['city' => 'Raipur', 'state' => 'Chhattisgarh']];
        $listing->save();
        $this->listing = $listing;

        // The buyer's row points at the price row it was picked from, now ₹1200
        // (the RFQ froze ₹1000). This seller's own row for the variation, ₹700,
        // is not what the legacy quotation reads.
        $houseRow = new SellerCommodityProductStatePrice();
        $houseRow->user_id                        = 1;
        $houseRow->commodity_product_id           = $product->id;
        $houseRow->commodity_product_variation_id = $variation->id;
        $houseRow->seller_commodity_product_id    = 0;
        $houseRow->value                          = [['name' => 'Size', 'value' => '8']];
        $houseRow->price                          = 1200;
        $houseRow->save();
        $this->houseRowId = $houseRow->id;

        $ownRow = $houseRow->replicate();
        $ownRow->user_id                     = $this->seller->id;
        $ownRow->seller_commodity_product_id = $listing->id;
        $ownRow->price                       = 700;
        $ownRow->save();
    }

    private function makeUser(string $type): User
    {
        $user = new User();
        $user->name     = 'Bid ' . $type . ' ' . uniqid();
        $user->type     = $type;
        $user->phone    = (string) random_int(7000000000, 9999999999);
        $user->email    = 'bid-' . uniqid() . '@example.test';
        $user->password = bcrypt('secret123');
        $user->status   = 'active';
        $user->save();

        return $user;
    }

    /** A live RFQ for 10 MT of the 8 mm (priced row) and 20 MT of a size whose row is gone. */
    private function liveRfq(): ProductEnquiry
    {
        $rfq = app(EnquiryCreator::class)->create($this->makeUser('customer'), [
            'commodity_product_id' => $this->product->id,
            'variation' => [
                ['id' => $this->houseRowId, 'value' => [['name' => 'Size', 'value' => '8']], 'price' => 1000, 'quantity' => '10'],
                ['id' => 0, 'value' => [['name' => 'Size', 'value' => '12']], 'price' => 500, 'quantity' => '20'],
            ],
            'quality'          => ['name' => 'Fe 500D', 'price' => 500],
            'packaging_charge' => ['id' => 1, 'name' => 'Bundle', 'charge' => 300],
            'delivery_city'    => 'Varanasi',
            'delivery_state'   => 'Uttar Pradesh',
        ], false);

        $bidding = app(LiveBiddingService::class);
        $bidding->attachSeller($rfq, $this->seller->id, $this->listing);
        $bidding->openBidding($rfq);

        return $rfq;
    }

    public function test_the_bid_screen_gets_everything_added_to_the_base_price(): void
    {
        $rfq = $this->liveRfq();

        Sanctum::actingAs($this->seller);
        $data = $this->getJson("/api/v2/seller/rfqs/{$rfq->id}")->assertOk()->json('data');

        $this->assertSame(46000.0, (float) $data['pricing']['listed_base_price']);
        $this->assertSame(18.0, (float) $data['pricing']['gst']);
        // Loading 200 + insurance 50 + cutting 100 + quality 500 + packaging 300.
        $this->assertEquals(1150, $data['pricing']['charges_total']);
        $this->assertNotContains('GST 18%', array_column($data['pricing']['charges'], 'label'));
        // The picked row's current price, then the frozen figure for the row that is gone.
        $this->assertEquals([1200, 500], array_column($data['pricing']['sizes'], 'gauge_diff'));
        // The seller's own invitation comes back (it was always null).
        $this->assertNotNull($data['my_reply']);
        $this->assertNull($data['my_reply']['quoted_base_price']);
        // The legacy "Order Details": dispatch from the listing.
        $this->assertSame(7, $data['order_terms']['dispatch_within_days']);
    }

    public function test_a_base_price_bid_is_priced_to_ex_and_for(): void
    {
        $rfq = $this->liveRfq();

        Sanctum::actingAs($this->seller);
        $response = $this->postJson("/api/v2/seller/rfqs/{$rfq->id}/quote", ['base_price' => 40000])->assertCreated();

        // 8 mm:  40000 + 1200 + 1150 = 42350, GST 7623 -> 49973 x 10 = 4,99,730
        // 12 mm: 40000 +  500 + 1150 = 41650, GST 7497 -> 49147 x 20 = 9,82,940
        // Total Ex 14,82,670; per MT = 14,82,670 / 30
        $this->assertEquals(40000, $response->json('meta.base_price'));
        $this->assertEquals(49422.33, $response->json('meta.ex_price'));

        $bid = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->where('user_id', $this->seller->id)->first();
        $this->assertEquals(40000, (float) $bid->base_price);
        $this->assertEquals(49422.33, $bid->ex_works_price);
        $this->assertEquals(round(49422.33 + $bid->freight_charges + $bid->other_charges, 2), $bid->for_price);

        // Each size keeps its own GST-inclusive rate for the order summary.
        $this->assertEquals(49973, $bid->value[0]['per_unit_price']);
        $this->assertEquals(7623, $bid->value[0]['tax']);
        $this->assertEquals(499730, $bid->value[0]['final_price']);
        $this->assertEquals(49147, $bid->value[1]['per_unit_price']);
        $this->assertSame('12', $bid->value[1]['value'][0]['value']);

        $this->assertEquals(40000, $response->json('data.my_reply.quoted_base_price'));
        $this->assertEquals(49422.33, $response->json('data.my_reply.ex_works_price'));

        // The board shows the basic price behind each rate, as the legacy one did.
        $row = collect($this->getJson("/api/v2/seller/rfqs/{$rfq->id}/leaderboard")->assertOk()->json('data'))->firstWhere('is_me', true);
        $this->assertEquals(40000, $row['base_price']);
    }

    public function test_it_prices_a_bid_exactly_as_the_legacy_seller_quotation(): void
    {
        // Without the duplicate "GST 18%" charge, which the legacy screen added
        // as ₹18 and the product page has stopped adding (B41).
        $this->listing->charge_name  = ['Cutting'];
        $this->listing->charge_price = [100];
        $this->listing->operator     = ['+'];
        $this->listing->save();

        $rfq = $this->liveRfq();
        Sanctum::actingAs($this->seller);
        $this->postJson("/api/v2/seller/rfqs/{$rfq->id}/quote", ['base_price' => 43210])->assertCreated();

        $bid = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->where('user_id', $this->seller->id)->first();
        $this->actingAs($this->seller);
        $legacy = (new LegacyQuotation($bid))->resolve();

        $priced = array_values(array_filter($legacy['variation'], fn ($v) => isset($v['per_unit_price'])));
        $this->assertEquals(array_column($priced, 'per_unit_price'), array_column(array_slice($bid->value, 0, count($priced)), 'per_unit_price'));
        $this->assertEquals(array_column($priced, 'final_price'), array_column(array_slice($bid->value, 0, count($priced)), 'final_price'));
    }

    public function test_a_base_price_is_required_and_above_zero(): void
    {
        $rfq = $this->liveRfq();

        Sanctum::actingAs($this->seller);
        $this->postJson("/api/v2/seller/rfqs/{$rfq->id}/quote", [])
            ->assertUnprocessable()->assertJsonValidationErrors(['base_price' => 'Enter your base price.']);
        $this->postJson("/api/v2/seller/rfqs/{$rfq->id}/quote", ['base_price' => 0])
            ->assertUnprocessable()->assertJsonValidationErrors('base_price');
    }

    public function test_an_older_client_sending_an_ex_price_is_still_accepted(): void
    {
        $rfq = $this->liveRfq();

        Sanctum::actingAs($this->seller);
        $this->postJson("/api/v2/seller/rfqs/{$rfq->id}/quote", ['ex_works_price' => 47800])->assertCreated();

        $bid = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->where('user_id', $this->seller->id)->first();
        $this->assertEquals(47800, $bid->ex_works_price);
    }
}
