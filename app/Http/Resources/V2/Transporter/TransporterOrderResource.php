<?php

namespace App\Http\Resources\V2\Transporter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransporterOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'unique_id'    => $this->unique_id,
            'order_id'     => $this->order_id,
            'enquiry_id'   => optional($this->getProductEnquiry)->unique_id,
            'status'       => $this->status,
            'origin_city'  => $this->origin_city,
            'transport_price' => (float) ($this->transport_price ?? 0),
            'total_amount' => (float) ($this->total_amount ?? 0),
            'final_amount' => (float) ($this->final_amount ?? 0),
            'customer' => $this->getCustomer ? [
                'id'    => $this->getCustomer->id,
                'name'  => $this->getCustomer->name,
                'phone' => $this->getCustomer->phone,
            ] : null,
            'seller' => $this->getSeller ? [
                'id'    => $this->getSeller->id,
                'name'  => $this->getSeller->name,
                'phone' => $this->getSeller->phone,
            ] : null,
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
                'unit' => $this->getCommodityProduct->getUnit ? [
                    'id'   => $this->getCommodityProduct->getUnit->id,
                    'name' => $this->getCommodityProduct->getUnit->name,
                ] : null,
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
