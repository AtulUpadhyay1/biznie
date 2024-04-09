<?php

namespace App\Http\Resources\Customer;

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
                    'id'        => $this->getBrand->id,
                    'name'      => $this->getBrand->name
                ] : [],

            'commodity_product' => $this->getCommodityProduct ? [
                    'id'        => $this->getCommodityProduct->id,
                    'name'      => $this->getCommodityProduct->name,
                    'thumbnail' => $this->getCommodityProduct->thumbnail ? imageUrl($this->getCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),

                    'category'  => $this->getCommodityProduct->getCategory ? [
                        'id'    => $this->getCommodityProduct->getCategory->id,
                        'name'  => $this->getCommodityProduct->getCategory->name,

                    ] : [],

                ] : [],

            'origin_city'       => $this->origin_city,
            'variation'         => $this->variation,
            'billing_address'   => $this->billing_address,
            'delivery_address'  => $this->delivery_address,
            'consignee_detail'  => $this->consignee_detail,
            'purpose'           => $this->purpose,
            'description'       => $this->description,
            'message'           => $this->message,
            'price'             => $this->price,
            'base_price'        => 0,
            'transport_price'   => 0,
            'commission'        => 0,
            'is_mark'           => false,
            'status'            => $this->status,
        ];

        if($this->getMarkedSellerProductEnquiry){

            $data['variation']  = $this->getMarkedSellerProductEnquiry->value;
            $data['base_price'] = $this->getMarkedSellerProductEnquiry->base_price;
            $data['transport_price'] = $this->getMarkedSellerProductEnquiry->transport_price;
            $data['commission'] = $this->getMarkedSellerProductEnquiry->commission;
            $data['is_mark']    = $this->getMarkedSellerProductEnquiry->is_mark ? true : false;
            $data['status']     = $this->getMarkedSellerProductEnquiry->status;
        }

        return $data;
    }
}
