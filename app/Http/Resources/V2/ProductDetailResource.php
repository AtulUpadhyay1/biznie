<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\Seller\MyCommodityProductVariationResource;
use App\Services\ProductPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public product detail, including the best live offer and its price breakup.
 *
 * The pricing rules live in ProductPricingService, which ports them from the v1
 * ProductDetailResource so every client shows the same numbers.
 *
 * Destination for the freight leg is taken from `?city=` / `?state=`, falling
 * back to the authenticated buyer's saved city/state.
 */
class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ProductPricingService $pricing */
        $pricing = app(ProductPricingService::class);

        $images = [];
        foreach ((is_array($this->images) ? $this->images : []) as $image) {
            $images[] = imageUrl($image);
        }

        [$city, $state] = $this->resolveDestination($request);

        // `?offer=` pins the page to the seller offer the buyer picked from the
        // offers list; without it the cheapest live offer is shown.
        $offerId = $request->integer('offer') ?: null;
        $best = $pricing->bestSellerOffer($this->resource, $offerId);
        $sellerProduct = $best['seller_product'] ?? null;
        $breakup = $best['breakup'] ?? null;

        $freight = $sellerProduct
            ? $pricing->freight((int) $this->id, $state, $city)
            : 0;

        $exPrice = $breakup ? (float) $breakup['ex_price'] : 0.0;
        // A freight rate the seller quoted for this city wins over the
        // transporter's, so the breakup below reports whichever applied.
        $doorstep = $sellerProduct
            ? $pricing->destinationForPrice($sellerProduct, $exPrice, (float) $freight, $state, $city)
            : ['for_price' => $exPrice, 'freight' => 0.0, 'source' => 'calculated'];
        $forPrice = $doorstep['for_price'];
        $appliedFreight = (float) $doorstep['freight'];

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'thumbnail'    => $this->thumbnail ? imageUrl($this->thumbnail) : null,
            'images'       => $images,
            'base_price'   => (float) ($this->base_price ?? 0),
            'gst'          => (float) ($this->gst ?? 0),
            'min_order_qty' => (float) ($this->min_order_qty ?? 0),
            'hsn_code'     => $this->hsn_code,
            'category'     => $this->getCategory ? ['id' => $this->getCategory->id, 'name' => $this->getCategory->name] : null,
            'sub_category' => $this->getSubCategory ? ['id' => $this->getSubCategory->id, 'name' => $this->getSubCategory->name] : null,
            'unit'         => $this->getUnit ? ['id' => $this->getUnit->id, 'name' => $this->getUnit->name] : null,
            'status'       => $this->status,

            'pricing' => $breakup ? [
                'base_price'       => (float) $breakup['base_price'],
                'gauge_diff'       => (float) $breakup['gauge_diff'],
                'loading_charge'   => (float) $breakup['loading_charge'],
                'insurance_charge' => (float) $breakup['insurance_charge'],
                'quality_charge'   => (float) $breakup['quality_charge'],
                'extra_charges'    => (float) $breakup['extra_charges'],
                'total_charges'    => (float) $breakup['total_charges'],
                'total_amount'     => (float) $breakup['total_amount'],
                'gst'              => (float) $breakup['gst'],
                'tcs'              => (float) $breakup['tcs'],
                'tax_amount'       => (float) $breakup['tax_amount'],
                'ex_works_price'   => $exPrice,
                'freight_charges'  => $appliedFreight,
                'other_charges'    => 0.0,
                'for_price'        => $forPrice,
                // 'seller' when the seller quoted this city, 'calculated' when
                // it is ex-works plus the cheapest transporter rate.
                'for_price_source' => $doorstep['source'],
                'other_charges_breakup' => $pricing->otherCharges($sellerProduct),
                'price_validity'   => $sellerProduct->price_validity ? dateTimeFormat($sellerProduct->price_validity) : null,
                'last_updated'     => dateTimeFormat($sellerProduct->updated_at),
            ] : null,

            'offer' => $sellerProduct ? [
                'seller_product_id' => $sellerProduct->id,
                'seller' => $sellerProduct->getUser ? [
                    'id'   => $sellerProduct->getUser->id,
                    'name' => $sellerProduct->getUser->name,
                ] : null,
                'brand' => $sellerProduct->getBrand ? [
                    'id'   => $sellerProduct->getBrand->id,
                    'name' => $sellerProduct->getBrand->name,
                ] : null,
                'loading_city'  => $sellerProduct->city,
                'loading_state' => $sellerProduct->state,
                'sellers_count' => (int) ($best['sellers_count'] ?? 0),
            ] : null,

            'delivery' => [
                'city'  => $city,
                'state' => $state,
                'freight_available' => $appliedFreight > 0,
                'destinations' => $pricing->deliveryDestinations((int) $this->id),
            ],

            'variations' => $sellerProduct
                ? MyCommodityProductVariationResource::collection($sellerProduct->getStatePrice)->resolve()
                : [],

            'quality'   => $pricing->qualityOptions($this->resource),
            'packaging' => $sellerProduct ? $pricing->packagingOptions($sellerProduct) : [],

            'price_history' => $sellerProduct ? $pricing->priceHistory($sellerProduct) : [],
        ];
    }

    /**
     * @return array{0: ?string, 1: ?string} [city, state]
     */
    private function resolveDestination(Request $request): array
    {
        $city = $request->string('city')->toString() ?: null;
        $state = $request->string('state')->toString() ?: null;

        if ($city && $state) {
            return [$city, $state];
        }

        $userDetail = $request->user()?->getUserDetail;

        return [
            $city ?: $userDetail?->city,
            $state ?: $userDetail?->state,
        ];
    }
}
