<?php

namespace Tests\Feature;

use App\Models\CommodityProduct;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;
use App\Models\SellerProductEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Guards the fix for the "Undefined array key 0" crash on the admin Seller
 * Reply screen.
 *
 * `SellerProductEnquiry::getSellerCommodityProduct` joins on `user_id` alone,
 * so once a seller owns more than one listing it is ambiguous - and on an eager
 * load the *last* matching row wins. The product-request wizard leaves behind
 * draft rows with no `commodity_product_id` and no state prices, and the day
 * one of those became a seller's newest row, `getStatePrice[0]` started
 * fatalling on pages that had worked for months.
 */
class SellerListingResolutionTest extends TestCase
{
    use DatabaseTransactions;

    private CommodityProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $product = CommodityProduct::first();
        if (! $product) {
            $this->markTestSkipped('No commodity product in the database to build a listing against.');
        }

        $this->product = $product;
    }

    private function makeSeller(): User
    {
        $user = new User();
        $user->name     = 'Listing Test Seller ' . uniqid();
        $user->type     = 'seller';
        $user->phone    = (string) random_int(7000000000, 9999999999);
        $user->email    = 'listing-' . uniqid() . '@example.test';
        $user->password = bcrypt('secret123');
        $user->status   = 'active';
        $user->save();

        return $user;
    }

    private function makeListing(User $seller, array $attributes = []): SellerCommodityProduct
    {
        $listing = new SellerCommodityProduct();
        $listing->user_id              = $seller->id;
        $listing->commodity_product_id = $this->product->id;
        $listing->brand_id             = 1;
        $listing->name                 = $this->product->name;
        $listing->status               = 'active';
        $listing->request_status       = 'approved';

        foreach ($attributes as $key => $value) {
            $listing->{$key} = $value;
        }

        $listing->save();

        return $listing;
    }

    private function makeBid(User $seller, array $attributes = []): SellerProductEnquiry
    {
        $bid = new SellerProductEnquiry();
        $bid->user_id              = $seller->id;
        $bid->commodity_product_id = $this->product->id;
        $bid->brand_id             = 1;
        $bid->status               = 'pending';

        foreach ($attributes as $key => $value) {
            $bid->{$key} = $value;
        }

        $bid->save();

        return $bid;
    }

    public function test_a_draft_listing_never_wins_the_relation(): void
    {
        $seller = $this->makeSeller();
        $real   = $this->makeListing($seller);

        // The wizard leftover: created last, so it used to win both the lazy
        // `first()` and the eager-load dictionary.
        $this->makeListing($seller, [
            'commodity_product_id' => null,
            'brand_id'             => null,
            'status'               => 'inactive',
            'request_status'       => 'draft',
        ]);

        $bid = $this->makeBid($seller);

        // Lazy read.
        $this->assertSame($real->id, $bid->getSellerCommodityProduct?->id);

        // Eager read — this is the path the admin screen actually uses, and the
        // one that regressed.
        $eager = SellerProductEnquiry::with('getSellerCommodityProduct')->find($bid->id);
        $this->assertSame($real->id, $eager->getSellerCommodityProduct?->id);
    }

    public function test_seller_listing_matches_the_enquiry_product_and_brand(): void
    {
        $seller = $this->makeSeller();

        $wanted = $this->makeListing($seller, ['brand_id' => 1]);
        // A newer listing for a different brand must not shadow the right one.
        $this->makeListing($seller, ['brand_id' => 2]);

        $bid = $this->makeBid($seller, ['brand_id' => 1]);

        $this->assertSame($wanted->id, $bid->sellerListing()?->id);
    }

    public function test_seller_listing_falls_back_when_the_brand_does_not_match(): void
    {
        $seller = $this->makeSeller();
        $only   = $this->makeListing($seller, ['brand_id' => 2]);

        // The seller stocks the product but not this brand: a listing is still
        // better than the null the old three-column lookup returned.
        $bid = $this->makeBid($seller, ['brand_id' => 1]);

        $this->assertSame($only->id, $bid->sellerListing()?->id);
    }

    public function test_origin_place_prefers_the_bids_own_loading_address(): void
    {
        $seller = $this->makeSeller();
        $this->makeListing($seller);

        $bid = $this->makeBid($seller, [
            'loading_address' => [['city' => 'Ranchi', 'state' => 'Jharkhand']],
        ]);

        $this->assertSame(['state' => 'Jharkhand', 'city' => 'Ranchi'], $bid->originPlace());
    }

    public function test_origin_place_falls_back_to_state_prices(): void
    {
        $seller  = $this->makeSeller();
        $listing = $this->makeListing($seller);

        $price = new SellerCommodityProductStatePrice();
        $price->user_id                     = $seller->id;
        $price->commodity_product_id        = $this->product->id;
        $price->brand_id                    = 1;
        $price->seller_commodity_product_id = $listing->id;
        $price->state                       = 'Chattisgarh';
        $price->city                        = 'Raipur';
        $price->price                       = '100';
        $price->save();

        // The shape that broke the screen: a loading address carrying an
        // address but neither city nor state.
        $bid = $this->makeBid($seller, [
            'loading_address' => [['address_line_one' => 'Plot 1', 'pin_code' => '493111']],
        ]);

        $this->assertSame(['state' => 'Chattisgarh', 'city' => 'Raipur'], $bid->originPlace());
    }

    public function test_origin_place_degrades_to_nulls_instead_of_throwing(): void
    {
        $seller = $this->makeSeller();

        // No listing, no loading address — previously an "Undefined array key 0".
        $bid = $this->makeBid($seller, ['loading_address' => null]);

        $this->assertSame(['state' => null, 'city' => null], $bid->originPlace());
        $this->assertNull($bid->sellerListing());
    }

    public function test_format_indian_number_tolerates_a_null_amount(): void
    {
        // An un-priced bid renders its base price through this helper.
        $this->assertSame('0', formatIndianNumber(null));
        $this->assertSame('47,800', formatIndianNumber(47800));
    }
}
