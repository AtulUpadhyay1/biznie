<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnquiryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'unique_id'       => $this->unique_id,
            'status'          => $this->status,
            'origin_city'     => $this->origin_city,
            'price'           => $this->price,
            'payment_mode'    => $this->payment_mode,
            'credit_day'      => $this->credit_day,
            'brand'           => $this->getBrand ? [
                'id'   => $this->getBrand->id,
                'name' => $this->getBrand->name,
            ] : null,
            'product'         => $this->getCommodityProduct ? [
                'id'        => $this->getCommodityProduct->id,
                'name'      => $this->getCommodityProduct->name,
                'slug'      => $this->getCommodityProduct->slug ?? null,
                'thumbnail' => $this->getCommodityProduct->thumbnail
                    ? imageUrl($this->getCommodityProduct->thumbnail)
                    : null,
                'category'  => $this->getCommodityProduct->getCategory ? [
                    'id'   => $this->getCommodityProduct->getCategory->id,
                    'name' => $this->getCommodityProduct->getCategory->name,
                ] : null,
            ] : null,
            'is_marked_by_seller' => (bool) $this->getMarkedSellerProductEnquiry,
            'order_id'        => optional($this->getCommodityProductOrder)->order_id,
            'created_at'      => optional($this->created_at)->toIso8601String(),
        ];
    }
}
