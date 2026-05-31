<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerCommodityProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'base_price'     => (float) ($this->base_price ?? 0),
            'gst'            => (float) ($this->gst ?? 0),
            'tcs'            => (float) ($this->tcs ?? 0),
            'loading_charge' => (float) ($this->loading_charge ?? 0),
            'insurance_charge' => (float) ($this->insurance_charge ?? 0),
            'quality_charge' => (float) ($this->quality_charge ?? 0),
            'is_quality'     => (bool) ($this->is_quality ?? 0),
            'state'          => $this->state,
            'city'           => $this->city,
            'status'         => $this->status,
            'thumbnail'      => $this->thumbnail ? imageUrl($this->thumbnail) : null,
            'category'       => $this->getCategory ? [
                'id'   => $this->getCategory->id,
                'name' => $this->getCategory->name,
            ] : null,
            'brand'          => $this->getBrand ? [
                'id'   => $this->getBrand->id,
                'name' => $this->getBrand->name,
            ] : null,
            'product'        => $this->getCommodityProduct ? [
                'id'        => $this->getCommodityProduct->id,
                'name'      => $this->getCommodityProduct->name,
                'slug'      => $this->getCommodityProduct->slug ?? null,
                'thumbnail' => $this->getCommodityProduct->thumbnail
                    ? imageUrl($this->getCommodityProduct->thumbnail)
                    : null,
            ] : null,
            'unit'           => $this->getUnit ? [
                'id'   => $this->getUnit->id,
                'name' => $this->getUnit->name,
            ] : null,
            'packaging_type' => $this->packaging_type ?? [],
            'packaging_type_price' => $this->packaging_type_price ?? [],
            'quality'        => $this->quality ?? [],
            'quality_price'  => $this->quality_price ?? [],
            'charge_name'    => $this->charge_name ?? [],
            'charge_price'   => $this->charge_price ?? [],
            'operator'       => $this->operator ?? [],
            'created_at'     => optional($this->created_at)->toIso8601String(),
            'updated_at'     => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
