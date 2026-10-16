<?php

namespace Tests\Feature;

use App\Models\CommodityProduct;
use App\Models\ProductCategory;
use App\Models\SellerCommodityProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * One source of truth for where a listing is filed (B07): the catalog
 * product. A seller's pick cannot override it, and re-filing the catalog
 * product re-files its listings.
 */
class ListingCatalogSyncTest extends TestCase
{
    use DatabaseTransactions;

    private function category(string $name): ProductCategory
    {
        $c = new ProductCategory();
        $c->name = $name;
        $c->slug = Str::slug($name) . '-' . uniqid();
        $c->status = 1;
        $c->save();

        return $c;
    }

    public function test_listing_is_filed_where_its_catalog_product_is(): void
    {
        $semi = $this->category('QA SEMI ' . uniqid());
        $finished = $this->category('QA FINISHED ' . uniqid());

        $catalog = new CommodityProduct();
        $catalog->name = 'QA Sponge ' . uniqid();
        $catalog->slug = Str::slug($catalog->name);
        $catalog->category_id = $semi->id;
        $catalog->status = 'active';
        $catalog->save();

        $seller = User::query()->first();
        if (! $seller) {
            $this->markTestSkipped('No user to own the listing.');
        }

        // The seller picks FINISHED; the catalog says semi-finished.
        $listing = new SellerCommodityProduct();
        $listing->user_id = $seller->id;
        $listing->commodity_product_id = $catalog->id;
        $listing->name = $catalog->name;
        $listing->category_id = $finished->id;
        $listing->status = 'inactive';
        $listing->save();

        $this->assertSame($semi->id, (int) $listing->fresh()->category_id);

        // Re-filing the catalog product carries its listings along.
        $catalog->category_id = $finished->id;
        $catalog->save();

        $this->assertSame($finished->id, (int) $listing->fresh()->category_id);
    }

    public function test_a_listing_without_a_catalog_link_keeps_the_sellers_category(): void
    {
        $own = $this->category('QA OWN ' . uniqid());
        $seller = User::query()->first();
        if (! $seller) {
            $this->markTestSkipped('No user to own the listing.');
        }

        $listing = new SellerCommodityProduct();
        $listing->user_id = $seller->id;
        $listing->name = 'QA own product ' . uniqid();
        $listing->category_id = $own->id;
        $listing->status = 'inactive';
        $listing->save();

        $this->assertSame($own->id, (int) $listing->fresh()->category_id);
    }
}
