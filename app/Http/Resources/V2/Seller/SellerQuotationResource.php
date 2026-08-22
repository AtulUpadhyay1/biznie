<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerQuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $myReply = $this->relationLoaded('getMySellerProductEnquiry')
            ? $this->getRelation('getMySellerProductEnquiry')
            : null;

        return [
            'id'           => $this->id,
            'unique_id'    => $this->unique_id,
            'status'       => $this->status,
            'origin_city'  => $this->origin_city,
            'quantity'     => $this->quantity !== null ? (float) $this->quantity : null,
            'unit_label'   => $this->unit_label,
            'size_label'   => $this->size_label,
            'delivery_city' => $this->delivery_city,
            'required_by'  => $this->required_by,
            'price'        => $this->price,
            /* Drives the LIVE pill and the countdown on the inbox row. Derived
               from `bidding_ends_at` so the list and the detail can never
               disagree about how long is left. */
            'bidding' => [
                'status'       => $this->bidding_status,
                'is_live'      => $this->bidding_status === 'live'
                                  && $this->bidding_ends_at
                                  && $this->bidding_ends_at->isFuture(),
                'ends_at'      => optional($this->bidding_ends_at)->toIso8601String(),
                'seconds_left' => $this->bidding_ends_at
                    ? max(0, (int) now()->diffInSeconds($this->bidding_ends_at, false))
                    : 0,
            ],
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
            'my_reply' => $myReply ? [
                'id'              => $myReply->id,
                'status'          => $myReply->status,
                'base_price'      => (float) ($myReply->base_price ?? 0),
                'transport_price' => (float) ($myReply->transport_price ?? 0),
                'ex_works_price'  => $myReply->ex_works_price !== null ? (float) $myReply->ex_works_price : null,
                'for_price'       => $myReply->for_price !== null ? (float) $myReply->for_price : null,
                'is_mark'         => (bool) $myReply->is_mark,
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
