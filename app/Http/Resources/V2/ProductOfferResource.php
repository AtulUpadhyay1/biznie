<?php

namespace App\Http\Resources\V2;

use App\Services\ProductPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One seller's live offer for a commodity product.
 *
 * Replaces the legacy SellerListByCommodityProductResource. The price is
 * produced by ProductPricingService — the same rule the detail page uses — so
 * the amount on this card is the amount the buyer sees after clicking through.
 *
 * Freight comes from `?city=` / `?state=`, matching the detail endpoint.
 */
class ProductOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ProductPricingService $pricing */
        $pricing = app(ProductPricingService::class);

        $breakup = $pricing->exWorksBreakup($this->resource);

        $city = $request->string('city')->toString() ?: $request->user()?->getUserDetail?->city;
        $state = $request->string('state')->toString() ?: $request->user()?->getUserDetail?->state;

        // No delivery city = Ex-Works (B08). The card used to price to the
        // listing's first quoted city and add its freight, so "Ex-Works only"
        // still showed a delivered price for somewhere the buyer never chose.
        $exWorksMode = ! $city;
        $priced = $exWorksMode ? ['state' => null, 'city' => null] : ['state' => $state, 'city' => $city];
        $freight = $exWorksMode ? 0 : $pricing->freight((int) $this->commodity_product_id, $priced['state'], $priced['city']);

        $statePrice = $this->getStatePrice->first();

        // A freight rate the seller quoted for this city replaces the
        // transporter's, so this card matches the detail page it links to.
        $doorstep = $exWorksMode
            ? ['for_price' => null, 'freight' => 0.0, 'source' => 'ex_works']
            : $pricing->destinationForPrice(
                $this->resource,
                (float) $breakup['ex_price'],
                (float) $freight,
                $priced['state'],
                $priced['city']
            );

        return [
            'id'                   => $this->id,
            'commodity_product_id' => $this->commodity_product_id,
            'name'                 => $this->name,
            'thumbnail'            => $this->thumbnail ? imageUrl($this->thumbnail) : null,
            'brand'                => $this->getBrand?->name,
            'seller'               => $this->getUser ? [
                'id'   => $this->getUser->id,
                'name' => $this->getUser->name,
            ] : null,
            'city'                 => $this->city ?: $statePrice?->city,
            'state'                => $this->state ?: $statePrice?->state,
            'quality'              => is_array($this->quality) ? array_values($this->quality) : [],
            'moq'                  => $this->moq,
            'moq_unit'             => $this->moq_unit,
            'price_validity'       => $this->price_validity ? dateTimeFormat($this->price_validity) : null,
            'last_updated'         => dateTimeFormat($this->updated_at),
            // ISO timestamps for "Valid till" / "Updated 3 hours ago".
            'valid_till'           => $this->validTill()?->toIso8601String(),
            'price_updated_at'     => optional($this->updated_at)->toIso8601String(),

            'base_price'      => (float) $breakup['base_price'],
            'total_charges'   => (float) $breakup['total_charges'],
            'gst'             => (float) $breakup['gst'],
            'tax_amount'      => (float) $breakup['tax_amount'],
            'ex_works_price'  => (float) $breakup['ex_price'],
            'freight_charges'  => (float) $doorstep['freight'],
            'for_price'        => $doorstep['for_price'],
            'for_price_source' => $doorstep['source'],
            // 'ex_works' until the buyer names a delivery city, then 'for'.
            'price_mode'       => $exWorksMode ? 'ex_works' : 'for',

            // Same shape the detail page reads, so a card and the page it links
            // to lead with the same price under the same name.
            'display' => [
                'show_ex_price'  => (bool) $this->show_ex_price,
                'show_for_price' => (bool) $this->show_for_price,
                'show_basic_price' => (bool) $this->show_basic_price,
                'show_fob_price' => (bool) $this->show_fob_price,
                'for_price_city' => $priced['city'],
            ],
        ];
    }
}
