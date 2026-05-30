<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerQuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $myReply = $this->whenLoaded('getMySellerProductEnquiry');
        return [
            'id'           => $this->id,
            'unique_id'    => $this->unique_id,
            'status'       => $this->status,
            'origin_city'  => $this->origin_city,
            'price'        => $this->price,
            'payment_mode' => $this->payment_mode,
            'credit_day'   => $this->credit_day,
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
            'customer' => $this->getUser ? [
                'id'   => $this->getUser->id,
                'name' => $this->getUser->name,
            ] : null,
            'my_reply' => $myReply && $myReply->resource ? [
                'id'              => $myReply->id,
                'status'          => $myReply->status,
                'base_price'      => (float) ($myReply->base_price ?? 0),
                'transport_price' => (float) ($myReply->transport_price ?? 0),
                'is_mark'         => (bool) $myReply->is_mark,
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
