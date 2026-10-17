<?php

namespace Tests\Feature;

use App\Models\BookmarkProduct;
use App\Models\CommodityProduct;
use App\Models\SellerCommodityProduct;
use App\Models\User;
use App\Services\ProductPricingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The buyer's watchlist shows what they can buy today: the saved listing's
 * price, quantity, dispatch window and validity while it is on offer, and
 * Biznie's current offer when it is not.
 */
class WatchlistApiTest extends TestCase
{
    use DatabaseTransactions;

    private const HOUSE_SELLER_ID = 1;

    private function user(string $type): User
    {
        $u = new User();
        $u->name     = 'QA ' . $type . ' ' . uniqid();
        $u->type     = $type;
        $u->phone    = (string) random_int(7000000000, 9999999999);
        $u->email    = 'qa-' . uniqid() . '@example.test';
        $u->password = bcrypt('secret123');
        $u->status   = 'active';
        $u->save();

        return $u;
    }

    private function catalog(): CommodityProduct
    {
        $c = new CommodityProduct();
        $c->name = 'QA Watch TMT ' . uniqid();
        $c->slug = Str::slug($c->name);
        $c->status = 'active';
        $c->save();

        return $c;
    }

    private function listing(CommodityProduct $catalog, int $sellerId, array $overrides = []): SellerCommodityProduct
    {
        $l = new SellerCommodityProduct();
        $l->user_id = $sellerId;
        $l->commodity_product_id = $catalog->id;
        $l->name = $catalog->name;
        $l->status = 'active';
        $l->request_status = 'approved';
        $l->base_price = 25000;
        $l->quantity = '100';
        $l->load_within = 3;
        $l->moq = '10';
        $l->moq_unit = 'MT';
        $l->city = 'raigarh';
        $l->state = 'Chhattisgarh';
        $l->price_validity = now()->addDays(2)->format(SellerCommodityProduct::PRICE_VALIDITY_FORMAT);
        foreach ($overrides as $k => $v) {
            $l->{$k} = $v;
        }
        $l->save();

        return $l;
    }

    private function watch(User $buyer, SellerCommodityProduct $listing): void
    {
        BookmarkProduct::create([
            'user_id'                     => $buyer->id,
            'commodity_product_id'        => $listing->commodity_product_id,
            'seller_commodity_product_id' => $listing->id,
        ]);
    }

    private function item(User $buyer, CommodityProduct $catalog): array
    {
        Sanctum::actingAs($buyer);
        $rows = $this->getJson('/api/v2/me/watchlist')->assertOk()->json('data');

        return collect($rows)->firstWhere('commodity_product_id', $catalog->id);
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (! User::find(self::HOUSE_SELLER_ID)) {
            $this->markTestSkipped('No house seller account to list from.');
        }
    }

    public function test_a_saved_listing_shows_every_value_the_seller_keeps_up_to_date(): void
    {
        $catalog = $this->catalog();
        $listing = $this->listing($catalog, self::HOUSE_SELLER_ID);
        $buyer = $this->user('customer');
        $this->watch($buyer, $listing);

        $sp = $this->item($buyer, $catalog)['seller_product'];
        $exPrice = app(ProductPricingService::class)->exWorksBreakup($listing->fresh())['ex_price'];

        $this->assertSame($listing->id, $sp['id']);
        $this->assertTrue($sp['is_saved_listing']);
        $this->assertTrue($sp['sold_by_biznie']);
        $this->assertEquals(25000, $sp['base_price']);
        $this->assertEquals($exPrice, $sp['ex_works_price']);
        // The old field now carries the same price instead of null.
        $this->assertEquals($exPrice, $sp['price']);
        $this->assertSame('100', $sp['quantity']);
        $this->assertSame(3, $sp['load_within']);
        $this->assertSame('10', $sp['moq']);
        $this->assertSame('Raigarh', $sp['city']);
        $this->assertNotNull($sp['valid_till']);
        $this->assertNotNull($sp['price_updated_at']);
    }

    public function test_a_listing_not_offered_to_buyers_is_replaced_by_biznies_offer(): void
    {
        $catalog = $this->catalog();
        $house = $this->listing($catalog, self::HOUSE_SELLER_ID);
        $other = $this->listing($catalog, $this->user('seller')->id, ['base_price' => 20000]);
        $buyer = $this->user('customer');
        $this->watch($buyer, $other);

        $sp = $this->item($buyer, $catalog)['seller_product'];

        $this->assertSame($house->id, $sp['id']);
        $this->assertFalse($sp['is_saved_listing']);
        $this->assertTrue($sp['sold_by_biznie']);
    }

    public function test_no_live_offer_means_no_listing(): void
    {
        $catalog = $this->catalog();
        $expired = $this->listing($catalog, self::HOUSE_SELLER_ID, [
            'price_validity' => now()->subDay()->format(SellerCommodityProduct::PRICE_VALIDITY_FORMAT),
        ]);
        $buyer = $this->user('customer');
        $this->watch($buyer, $expired);

        $this->assertNull($this->item($buyer, $catalog)['seller_product']);
    }
}
