<?php

namespace App\Http\Resources\V2\Seller;

use App\Services\ProductPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SellerProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = is_array($this->images ?? null) ? $this->images : [];
        $imageUrls = [];
        foreach ($images as $imgId) {
            $imageUrls[] = imageUrl($imgId);
        }

        // Priced with the same service the public offer cards use, so the ex
        // price a seller sees on their own product is the one buyers compare.
        $breakup = app(ProductPricingService::class)->exWorksBreakup($this->resource);

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'status'      => $this->status,
            'request_status'    => $this->request_status,
            'request_reference' => $this->request_reference,
            'review_note'       => $this->review_note,
            // Editable unless a reviewer is holding it. An approved product stays
            // editable: step saves keep it approved and live.
            'can_edit'          => $this->request_status !== 'pending_review',
            'thumbnail'   => $imageUrls[0] ?? null,
            'images'      => $imageUrls,
            'commodity_product' => $this->getCommodityProduct ? [
                'id'   => $this->getCommodityProduct->id,
                'name' => $this->getCommodityProduct->name,
            ] : null,
            'brand' => $this->getBrand ? [
                'id'   => $this->getBrand->id,
                'name' => $this->getBrand->name,
            ] : null,
            'category' => $this->getCategory ? [
                'id'   => $this->getCategory->id,
                'name' => $this->getCategory->name,
            ] : null,
            'unit' => $this->getUnit ? [
                'id'   => $this->getUnit->id,
                'name' => $this->getUnit->name,
            ] : null,
            'variation'      => $this->variation ?? [],
            'size'           => $this->size ?? [],
            'quality'        => $this->quality ?? [],
            'packaging_type' => $this->packaging_type ?? [],

            // Seller-editable operating values (the price panel).
            'base_price'     => (float) $breakup['base_price'],
            'load_within'    => (string) ($this->load_within ?? ''),
            'quantity'       => (string) ($this->quantity ?? ''),
            // Stored as `Y-m-d h:i A` for the admin reports; handed to the client
            // in the shape a `datetime-local` input expects.
            'price_validity' => $this->price_validity
                ? Carbon::parse($this->price_validity)->format('Y-m-d\TH:i')
                : null,

            // Ex-Works: base + gauge difference + charges + GST. The breakup
            // travels with it so the figure can be explained where it is shown,
            // instead of the client re-deriving a number the server already has.
            'ex_price'       => (float) $breakup['ex_price'],
            'price_breakup'  => [
                'base_price'    => (float) $breakup['base_price'],
                'gauge_diff'    => (float) $breakup['gauge_diff'],
                'total_charges' => (float) $breakup['total_charges'],
                'gst'           => (float) $breakup['gst'],
                'tax_amount'    => (float) $breakup['tax_amount'],
            ],

            'created_at'     => optional($this->created_at)->toIso8601String(),
            'updated_at'     => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
