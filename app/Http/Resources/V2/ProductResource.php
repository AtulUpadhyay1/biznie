<?php

namespace App\Http\Resources\V2;

use App\Services\ProductPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = [];
        if (is_array($this->images)) {
            foreach ($this->images as $img) {
                $images[] = imageUrl($img);
            }
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'thumbnail'   => $this->thumbnail ? imageUrl($this->thumbnail) : null,
            'images'      => $images,
            'base_price'  => (float) ($this->base_price ?? 0),
            'gst'         => (float) ($this->gst ?? 0),
            // Lets list-driven forms (Rate Finder) enforce the MOQ (B14).
            'min_order_qty' => (float) ($this->min_order_qty ?? 0),
            'category'    => $this->whenLoaded('getCategory', fn () => $this->getCategory ? [
                'id'   => $this->getCategory->id,
                'name' => $this->getCategory->name,
            ] : null),
            'sub_category' => $this->whenLoaded('getSubCategory', fn () => $this->getSubCategory ? [
                'id'   => $this->getSubCategory->id,
                'name' => $this->getSubCategory->name,
            ] : null),
            'unit'        => $this->whenLoaded('getUnit', fn () => $this->getUnit ? [
                'id'   => $this->getUnit->id,
                'name' => $this->getUnit->name,
            ] : null),
            'status'      => $this->status,
            // "from ₹X/MT" on product cards (4.10), opt-in via ?with_price=1:
            // the cheapest live ex-works offer whose seller publishes that
            // price — the same figure the product page starts from.
            'from_price'  => $this->when($request->boolean('with_price'), fn () => $this->fromPrice()),
        ];
    }

    private function fromPrice(): ?array
    {
        $pricing = app(ProductPricingService::class);
        $offers = $pricing->sellerOffers($this->resource)->filter(fn ($offer) => (bool) $offer->show_ex_price);
        $cheapest = $offers->first();

        return $cheapest ? [
            'ex_works'      => (float) $pricing->exWorksBreakup($cheapest)['ex_price'],
            'sellers_count' => $offers->count(),
        ] : null;
    }
}
