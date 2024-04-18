<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'order_id'          => $this->order_id,
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
            'base_price'        => $this->base_price,
            'token_amount'      => $this->token_amount,
            'transport_price'   => $this->transport_price,
            'commission'        => $this->commission,
            'loading_address'   => $this->loading_address,
            'status'            => $this->status,
            'updated_at'        => dateTimeFormat($this->updated_at),
            'quality_check_image'           => $this->quality_check_image ?? [],
            'quality_check_image_status'    => $this->quality_check_image_status,
            'quality_check_image_status_updated_by' => $this->quality_check_image_status_updated_by,
            'invoice'           => $this->invoice,
            'final_invoice'     => $this->final_invoice,
            'e_bill'            => $this->e_bill,
            'quality_check_certificate' => $this->quality_check_certificate,
            'insurance_certificate'     => $this->insurance_certificate,
            'other'             => $this->other,
            'final_quantity_by_seller'  => $this->final_quantity_by_seller,
            'final_quantity_by_customer'=> $this->final_quantity_by_customer,
            'invoice'                   => $this->invoice ? imageUrl($this->invoice) : null,
            'final_invoice'             => $this->final_invoice ? imageUrl($this->final_invoice) : null,
            'e_bill'                    => $this->e_bill ? imageUrl($this->e_bill) : null,
            'quality_check_certificate' => $this->quality_check_certificate ? imageUrl($this->quality_check_certificate) : null,
            'insurance_certificate'     => $this->insurance_certificate ? imageUrl($this->insurance_certificate) : null,
            'other'                     => $this->other ? imageUrl($this->other) : null,
        ];

        $quality_check_image_arr = [];
        if($this->quality_check_image){
            foreach ($this->quality_check_image as $image_id) {
                $image_data['id']       = $image_id;
                $image_data['image']    = imageUrl($image_id);
                $quality_check_image_arr[] = $image_data;
            }

            $data['quality_check_image']=$quality_check_image_arr;
        }

        return $data;
    }
}
