<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'            => $this->id,
            'commodity_product_id'  => $this->commodity_product_id,
            'seller_commodity_product_id'  => $this->seller_commodity_product_id,
            'name'          => $this->getCommodityProduct->name,
            'brand'         => $this->getBrand->name,
            'address'       => $this->city,
            'base_price'    => $this->base_price,
            'transport_price'   => $this->transport_price,
            'commission'        => $this->commission,
            'thumbnail'     => $this->getCommodityProduct->thumbnail ? imageUrl($this->getCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),
            'updated_at'    => dateTimeFormat($this->updated_at),
            'is_mark'       => $this->is_mark ? true : false,
        ];

        return $data;
    }
}
