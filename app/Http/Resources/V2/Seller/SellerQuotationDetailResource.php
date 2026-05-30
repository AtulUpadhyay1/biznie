<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerQuotationDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $myReply = $this->whenLoaded('getMySellerProductEnquiry');
        return [
            'id'              => $this->id,
            'unique_id'       => $this->unique_id,
            'status'          => $this->status,
            'origin_city'     => $this->origin_city,
            'description'     => $this->description,
            'purpose'         => $this->purpose,
            'variation'       => $this->variation ?? null,
            'quality'         => $this->quality ?? null,
            'packaging_charge'=> $this->packaging_charge ?? null,
            'price'           => $this->price,
            'payment_mode'    => $this->payment_mode,
            'credit_day'      => $this->credit_day,
            'billing_address' => $this->billing_address ?? null,
            'delivery_address'=> $this->delivery_address ?? null,
            'consignee_detail'=> $this->consignee_detail ?? null,
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
                'price'           => $myReply->price,
                'commission'      => $myReply->commission ?? null,
                'commission_type' => $myReply->commission_type ?? null,
                'is_mark'         => (bool) $myReply->is_mark,
                'created_at'      => optional($myReply->created_at)->toIso8601String(),
                'updated_at'      => optional($myReply->updated_at)->toIso8601String(),
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
