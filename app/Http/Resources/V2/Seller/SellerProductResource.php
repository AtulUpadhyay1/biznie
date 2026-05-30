<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = is_array($this->images ?? null) ? $this->images : [];
        $imageUrls = [];
        foreach ($images as $imgId) {
            $imageUrls[] = imageUrl($imgId);
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'status'      => $this->status,
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
            'created_at'     => optional($this->created_at)->toIso8601String(),
            'updated_at'     => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
