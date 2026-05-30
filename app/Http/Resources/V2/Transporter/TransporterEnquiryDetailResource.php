<?php

namespace App\Http\Resources\V2\Transporter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransporterEnquiryDetailResource extends JsonResource
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
            'description'  => $this->description ?? null,
            'billing_address'  => $this->billing_address ?? null,
            'delivery_address' => $this->delivery_address ?? null,
            'consignee_detail' => $this->consignee_detail ?? null,
            'history'      => $this->history ?? [],
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
                'id'           => $pe->id,
                'unique_id'    => $pe->unique_id,
                'status'       => $pe->status,
                'origin_city'  => $pe->origin_city,
                'purpose'      => $pe->purpose,
                'description'  => $pe->description,
                'payment_mode' => $pe->payment_mode,
                'credit_day'   => $pe->credit_day,
            ] : null,
            'created_at'   => optional($this->created_at)->toIso8601String(),
            'updated_at'   => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
