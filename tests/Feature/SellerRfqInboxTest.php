<?php

namespace Tests\Feature;

use App\Livewire\Admin\CommodityProductEnquiry\ManualPriceEntry;
use App\Livewire\Admin\CommodityProductEnquiry\Show;
use App\Livewire\Admin\WebsiteSetup\Setting;
use App\Models\CommodityProduct;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductEnquiry;
use App\Models\User;
use App\Models\WebsiteSetup;
use App\Services\Rfq\PushNotifier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A new RFQ reaches the seller everywhere it should: the New RFQs tile, the
 * dashboard list and the New RFQs tab agree, the buyer's sizes arrive by
 * name, and the bidding window is the admin's reply time.
 */
class SellerRfqInboxTest extends TestCase
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

        // Fan-out reaches real sellers in the dev database: no push may leave.
        $this->app->instance(PushNotifier::class, new class extends PushNotifier {
            public function toUser(User $user, string $title, string $body, string $type = 'notification', array $payload = [], bool $save = false): void
            {
            }

            public function toAdmins(string $title, string $body, string $type = 'notification', array $payload = []): void
            {
            }
        });
    }

    private function makeUser(string $type): User
    {
        $user = new User();
        $user->name     = 'Inbox ' . $type . ' ' . uniqid();
        $user->type     = $type;
        $user->phone    = (string) random_int(7000000000, 9999999999);
        $user->email    = 'inbox-' . uniqid() . '@example.test';
        $user->password = bcrypt('secret123');
        $user->status   = 'active';
        $user->save();

        return $user;
    }

    private function makeSeller(): User
    {
        $seller = $this->makeUser('seller');

        $listing = new SellerCommodityProduct();
        $listing->user_id              = $seller->id;
        $listing->commodity_product_id = $this->product->id;
        $listing->name                 = $this->product->name;
        $listing->status               = 'active';
        $listing->loading_address      = [['city' => 'Raipur', 'state' => 'Chhattisgarh']];
        $listing->save();

        return $seller;
    }

    private function createRfq(User $buyer, array $extra = []): int
    {
        Sanctum::actingAs($buyer);

        return $this->postJson('/api/v2/me/enquiries', $extra + [
            'commodity_product_id' => $this->product->id,
            'quantity'             => 30,
            'unit_label'           => 'Tons',
            'delivery_city'        => 'Varanasi',
            'delivery_state'       => 'Uttar Pradesh',
        ])->assertCreated()->json('data.id');
    }

    private function newRfqIds(User $seller): array
    {
        Sanctum::actingAs($seller);
        $tab = collect($this->getJson('/api/v2/seller/rfqs?tab=received')->assertOk()->json('data'))->pluck('id')->all();
        $dashboard = $this->getJson('/api/v2/seller/dashboard')->assertOk();

        return [
            'tab'       => $tab,
            'dashboard' => collect($dashboard->json('data.recent_rfqs'))->pluck('id')->all(),
            'tile'      => $dashboard->json('data.tiles.rfqs.new'),
        ];
    }

    public function test_a_fresh_invitation_shows_on_the_tile_the_dashboard_and_the_new_rfqs_tab(): void
    {
        $seller = $this->makeSeller();
        $rfqId = $this->createRfq($this->makeUser('customer'));

        // The case that went missing: an invitation with no base price at all.
        $this->assertNull(
            SellerProductEnquiry::where('product_enquiries_id', $rfqId)->where('user_id', $seller->id)->value('base_price')
        );

        $seen = $this->newRfqIds($seller);
        $this->assertContains($rfqId, $seen['tab']);
        $this->assertContains($rfqId, $seen['dashboard']);
        $this->assertGreaterThanOrEqual(1, $seen['tile']);
    }

    public function test_a_rivals_bid_does_not_take_the_rfq_off_my_new_list(): void
    {
        $me = $this->makeSeller();
        $rival = $this->makeSeller();
        $rfqId = $this->createRfq($this->makeUser('customer'));

        Sanctum::actingAs($rival);
        $this->postJson("/api/v2/seller/rfqs/{$rfqId}/quote", ['ex_works_price' => 47800])->assertCreated();

        $seen = $this->newRfqIds($me);
        $this->assertContains($rfqId, $seen['tab']);
        $this->assertContains($rfqId, $seen['dashboard']);

        // And for the rival it has moved to My Bids.
        $this->assertNotContains($rfqId, $this->newRfqIds($rival)['tab']);
    }

    public function test_the_buyers_sizes_reach_the_seller_by_name(): void
    {
        $seller = $this->makeSeller();
        $size = fn (string $mm, int $id) => [
            'id'       => $id,
            'value'    => [['name' => 'Size', 'value' => $mm, 'unit' => ['name' => 'Millimetre', 'short_name' => 'mm']]],
            'price'    => 0,
            'quantity' => '10',
            'ex_price' => 48000,
            'line_total' => 480000,
        ];
        $rfqId = $this->createRfq($this->makeUser('customer'), [
            'quantity'  => null,
            'variation' => [$size('8', 1), $size('10', 2), $size('12', 3)],
        ]);

        $rfq = ProductEnquiry::find($rfqId);
        $this->assertSame('8', $rfq->variation[0]['value'][0]['value']);
        // The rows' total is the RFQ's quantity.
        $this->assertEquals(30, (float) $rfq->quantity);

        Sanctum::actingAs($seller);
        $rows = $this->getJson("/api/v2/seller/rfqs/{$rfqId}")->assertOk()->json('data.variation');
        $this->assertSame(['8', '10', '12'], array_map(fn ($r) => $r['value'][0]['value'], $rows));
        $this->assertSame('10', $rows[1]['quantity']);
    }

    public function test_the_admin_reply_time_is_the_bidding_window(): void
    {
        WebsiteSetup::updateOrCreate(['key' => 'seller_enquiry_reply_time'], ['key' => 'seller_enquiry_reply_time', 'value' => '60']);
        $this->makeSeller();

        $rfq = ProductEnquiry::find($this->createRfq($this->makeUser('customer')));

        $this->assertSame('live', $rfq->bidding_status);
        $this->assertEqualsWithDelta(60, $rfq->bidding_started_at->diffInMinutes($rfq->bidding_ends_at), 1);
    }

    public function test_the_price_option_reaches_the_buyer_the_seller_and_the_admin(): void
    {
        $seller = $this->makeSeller();
        $buyer = $this->makeUser('customer');
        $rfqId = $this->createRfq($buyer, ['price_option' => 'for']);

        $this->assertSame('for', ProductEnquiry::find($rfqId)->price_option);

        Sanctum::actingAs($buyer);
        $this->getJson("/api/v2/me/enquiries/{$rfqId}")->assertOk()
            ->assertJsonPath('data.price_option', 'for')
            ->assertJsonPath('data.price_option_label', 'F.O.R');

        Sanctum::actingAs($seller);
        $this->getJson("/api/v2/seller/rfqs/{$rfqId}")->assertOk()
            ->assertJsonPath('data.price_option_label', 'F.O.R');
        $row = collect($this->getJson('/api/v2/seller/rfqs?tab=received')->json('data'))->firstWhere('id', $rfqId);
        $this->assertSame('F.O.R', $row['price_option_label']);

        Livewire::test(ManualPriceEntry::class, ['id' => $rfqId])
            ->assertSeeHtml('<dt>Price Option</dt>')
            ->assertSee('F.O.R')
            ->assertSee("Delivered to the buyer's city, freight included");
        Livewire::test(Show::class, ['id' => $rfqId])
            ->assertSeeHtml('<dt>Price Option</dt>')
            ->assertSee('F.O.R');
    }

    public function test_an_rfq_without_a_unit_takes_the_products(): void
    {
        $this->product->loadMissing('getUnit');
        if (! $this->product->getUnit) {
            $this->markTestSkipped('The product has no unit to fall back to.');
        }
        $this->makeSeller();

        $rfqId = $this->createRfq($this->makeUser('customer'), ['unit_label' => null]);

        $short = $this->product->getUnit->short_name;
        $expected = in_array(strtolower(trim($short)), ['mt', 'metric ton', 'metric tonne'], true) ? 'MT' : $short;
        $this->assertSame($expected, ProductEnquiry::find($rfqId)->unit_label);
    }

    public function test_an_unknown_price_option_is_refused(): void
    {
        Sanctum::actingAs($this->makeUser('customer'));

        $this->postJson('/api/v2/me/enquiries', [
            'commodity_product_id' => $this->product->id,
            'quantity'             => 30,
            'price_option'         => 'cif',
        ])->assertUnprocessable()->assertJsonValidationErrors('price_option');
    }

    public function test_the_admin_reply_time_must_be_whole_minutes(): void
    {
        Livewire::test(Setting::class)
            ->set('value.seller_enquiry_reply_time', '0')
            ->call('updateSetting')
            ->assertHasErrors(['value.seller_enquiry_reply_time' => 'min']);
    }
}
