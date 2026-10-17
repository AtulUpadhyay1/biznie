<?php

namespace App\Http\Resources\V2;

use App\Models\CommodityProduct;
use App\Models\SellerCommodityProduct;
use App\Services\ProductPricingService;
use App\Support\AddressFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cp = $this->getCommodityProduct;

        /** @var ProductPricingService $pricing */
        $pricing = app(ProductPricingService::class);

        // What the buyer can actually buy today: the saved listing while it is
        // still on offer, otherwise Biznie's current offer for the product, the
        // same pick the product page makes for `?offer=`. A stale quote or a
        // listing that is not offered to buyers is never shown here.
        $offer = $cp ? $pricing->bestSellerOffer($cp, (int) $this->seller_commodity_product_id) : null;

        return [
            'id'                          => $this->id,
            'commodity_product_id'        => $this->commodity_product_id,
            'seller_commodity_product_id' => $this->seller_commodity_product_id,
            'product' => $cp ? [
                'id'        => $cp->id,
                'name'      => $cp->name,
                'slug'      => $cp->slug ?? null,
                'thumbnail' => $cp->thumbnail ? imageUrl($cp->thumbnail) : null,
                'category'  => $cp->getCategory ? [
                    'id'   => $cp->getCategory->id,
                    'name' => $cp->getCategory->name,
                ] : null,
                'unit' => $cp->getUnit ? [
                    'id'   => $cp->getUnit->id,
                    'name' => $cp->getUnit->name,
                ] : null,
            ] : null,
            'seller_product' => $offer ? $this->listing($offer['seller_product'], $offer['breakup'], $cp, $pricing) : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }

    /** The values a seller keeps up to date, priced the way the product page prices them. */
    private function listing(SellerCommodityProduct $listing, array $breakup, CommodityProduct $cp, ProductPricingService $pricing): array
    {
        $loadingCity = $listing->city ?: $listing->getStatePrice->first()?->city;

        return [
            'id'               => $listing->id,
            // Ex-Works per unit. `price` is kept for readers of the old shape.
            'price'            => (float) $breakup['ex_price'],
            'ex_works_price'   => (float) $breakup['ex_price'],
            'base_price'       => (float) $breakup['base_price'],
            'unit'             => $listing->getUnit?->name ?? $cp->getUnit?->name,
            'quantity'         => filled($listing->quantity) ? (string) $listing->quantity : null,
            'load_within'      => filled($listing->load_within) ? (int) $listing->load_within : null,
            'moq'              => $listing->moq,
            'moq_unit'         => $listing->moq_unit,
            'city'             => $loadingCity ? AddressFormatter::titleCase($loadingCity) : null,
            'state'            => $listing->state ?: $listing->getStatePrice->first()?->state,
            'valid_till'       => $listing->validTill()?->toIso8601String(),
            'price_updated_at' => optional($listing->updated_at)->toIso8601String(),
            // False when the saved listing is gone and Biznie's current offer stands in.
            'is_saved_listing' => $listing->id === (int) $this->seller_commodity_product_id,
            'sold_by_biznie'   => $pricing->isHouseSeller((int) $listing->user_id),
            'brand' => $listing->getBrand ? [
                'id'   => $listing->getBrand->id,
                'name' => $listing->getBrand->name,
            ] : null,
            'seller' => $listing->getUser ? [
                'id'   => $listing->getUser->id,
                'name' => $listing->getUser->name,
            ] : null,
        ];
    }
}
