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
            'quantity'        => $this->quantity !== null ? (float) $this->quantity : null,
            'unit_label'      => $this->unit_label,
            'size_label'      => $this->size_label,
            'delivery_city'   => $this->delivery_city,
            'delivery_state'  => $this->delivery_state,
            'required_by'     => $this->required_by,
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
            // Enough for a list row to render a LIVE badge and a countdown
            // without a per-row query; the full board lives on /live.
            'bidding' => [
                'status'         => $this->bidding_status,
                'is_live'        => $this->bidding_status === 'live'
                                    && $this->bidding_ends_at
                                    && $this->bidding_ends_at->isFuture(),
                'started_at'     => optional($this->bidding_started_at)->toIso8601String(),
                'ends_at'        => optional($this->bidding_ends_at)->toIso8601String(),
                'seconds_left'   => $this->bidding_ends_at
                    ? max(0, (int) now()->diffInSeconds($this->bidding_ends_at, false))
                    : 0,
                'best_for_price' => $this->best_for_price !== null ? (float) $this->best_for_price : null,
            ],
            'is_marked_by_seller' => (bool) $this->getMarkedSellerProductEnquiry,
            'order_id'        => optional($this->getCommodityProductOrder)->order_id,
            'created_at'      => optional($this->created_at)->toIso8601String(),
        ];
    }
}
