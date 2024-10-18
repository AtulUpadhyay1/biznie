<?php

namespace App\Http\Resources\Transporter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEnquiryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $data = [
            'id'                => $this->id,
            'unique_id'         => $this->unique_id,
            'brand'             => $this->getBrand ? [
                'id'            => $this->getBrand->id,
                'name'          => $this->getBrand->name
            ] : [],

            'commodity_product' => $this->getCommodityProduct ? [
                'id'            => $this->getCommodityProduct->id,
                'name'          => $this->getCommodityProduct->name,
                'thumbnail'     => $this->getCommodityProduct->thumbnail ? imageUrl($this->getCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),

                'category'      => $this->getCommodityProduct->getCategory ? [
                    'id'        => $this->getCommodityProduct->getCategory->id,
                    'name'      => $this->getCommodityProduct->getCategory->name,

                ] : [],

            ] : [],

            'origin_city'       => $this->origin_city,
            'billing_address'   => $this->billing_address,
            'delivery_address'  => $this->delivery_address,
            'consignee_detail'  => $this->consignee_detail,
            'purpose'           => $this->purpose,
            'description'       => $this->description,
            'message'           => $this->message,
            'min_price'         => $this->min_price,
            'max_price'         => $this->max_price,
            'price'             => $this->price,
            'status'            => $this->status,
            'history'           => $this->history
        ];

        return $data;
    }
}
