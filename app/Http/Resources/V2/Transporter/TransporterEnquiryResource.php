<?php

namespace App\Http\Resources\V2\Transporter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransporterEnquiryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pe = $this->whenLoaded('getProductEnquiry');
        return [
            'id'           => $this->id,
            'unique_id'    => $this->unique_id,
            'product_enquiry_id' => $this->product_enquiries_id,
            'status'       => $this->status,
            'is_marked'    => (bool) $this->is_mark,
            'price'        => $this->price,
            'min_price'    => $this->min_price,
            'max_price'    => $this->max_price,
            'origin_city'  => $this->origin_city,
            'brand' => $this->getBrand ? [
                'id'   => $this->getBrand->id,
                'name' => $this->getBrand->name,
            ] : null,
            'product' => $this->getCommodityProduct ? [
                'id'        => $this->getCommodityProduct->id,
                'name'      => $this->getCommodityProduct->name,
                'slug'      => $this->getCommodityProduct->slug ?? null,
                'thumbnail' => $this->getCommodityProduct->thumbnail
                    ? imageUrl($this->getCommodityProduct->thumbnail)
                    : null,
                'category' => $this->getCommodityProduct->getCategory ? [
                    'id'   => $this->getCommodityProduct->getCategory->id,
                    'name' => $this->getCommodityProduct->getCategory->name,
                ] : null,
            ] : null,
            'parent_enquiry' => $pe && $pe->resource ? [
                'id'        => $pe->id,
                'unique_id' => $pe->unique_id,
                'status'    => $pe->status,
            ] : null,
            'created_at'   => optional($this->created_at)->toIso8601String(),
        ];
    }
}
