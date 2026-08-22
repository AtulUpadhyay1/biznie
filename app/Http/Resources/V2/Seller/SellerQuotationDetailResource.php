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
            'quantity'        => $this->quantity !== null ? (float) $this->quantity : null,
            'unit_label'      => $this->unit_label,
            'size_label'      => $this->size_label,
            /* The city is all a bidding seller gets. Full delivery details are
               released only after the buyer confirms the order - the screen
               says as much, and the payload has to make that true. */
            'delivery_city'   => $this->delivery_city,
            'required_by'     => $this->required_by,
            'bidding' => [
                'status'       => $this->bidding_status,
                'is_live'      => $this->bidding_status === 'live'
                                  && $this->bidding_ends_at
                                  && $this->bidding_ends_at->isFuture(),
                'started_at'   => optional($this->bidding_started_at)->toIso8601String(),
                'ends_at'      => optional($this->bidding_ends_at)->toIso8601String(),
                'seconds_left' => $this->bidding_ends_at
                    ? max(0, (int) now()->diffInSeconds($this->bidding_ends_at, false))
                    : 0,
            ],
            'description'     => $this->description,
            'purpose'         => $this->purpose,
            'variation'       => $this->variation ?? null,
            'quality'         => $this->quality ?? null,
            'packaging_charge'=> $this->packaging_charge ?? null,
            'price'           => $this->price,
            'payment_mode'    => $this->payment_mode,
            'credit_day'      => $this->credit_day,
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
            /* The seller's own bid. `freight_charges` and `other_charges` are
               read-only here: the seller types ex-works, the platform supplies
               the rest, and `for_price` is what the ranking uses. */
            'my_reply' => $myReply && $myReply->resource ? [
                'id'              => $myReply->id,
                'status'          => $myReply->status,
                'base_price'      => (float) ($myReply->base_price ?? 0),
                'transport_price' => (float) ($myReply->transport_price ?? 0),
                'ex_works_price'  => $myReply->ex_works_price !== null ? (float) $myReply->ex_works_price : null,
                'freight_charges' => (float) ($myReply->freight_charges ?? 0),
                'other_charges'   => (float) ($myReply->other_charges ?? 0),
                'for_price'       => $myReply->for_price !== null ? (float) $myReply->for_price : null,
                'ex_works_city'   => $myReply->ex_works_city,
                'freight_type'    => $myReply->freight_type,
                'price_source'    => $myReply->price_source ?: 'app',
                'remarks'         => $myReply->remarks,
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
