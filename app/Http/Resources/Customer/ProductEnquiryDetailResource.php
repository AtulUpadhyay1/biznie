<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEnquiryDetailResource extends JsonResource
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
            'unit'              => $this->getCommodityProduct->getUnit ? [
                    'id'        => $this->getCommodityProduct->getUnit->id,
                    'name'      => $this->getCommodityProduct->getUnit->name
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
            'variation'         => [],
            'billing_address'   => $this->billing_address,
            'delivery_address'  => $this->delivery_address,
            'consignee_detail'  => $this->consignee_detail,
            'purpose'           => $this->purpose,
            'description'       => $this->description,
            'message'           => $this->message,
            // 'price'             => $this->price,
            'total_quantity'    => 0,
            'base_price'        => 0,
            'loading_charge'    => 0,
            'insurance_charge'  => 0,
            'quality_charge'    => 0,
            'gst'               => 0,
            'tcs'               => 0,
            'gst_amount'        => 0,
            'tcs_amount'        => 0,
            'total_charges'     => 0,
            'commission'        => 0,
            'final_variation_price' => 0,
            'ex_price'          => 0,
            'transport_price'   => 0,
            'for_price'         => 0,
            'required_booking_amount' => 0,
            'is_mark'           => false,
            'status'            => $this->status,
        ];
        $markedSeller = $this->getMarkedSellerProductEnquiry;
        if($markedSeller){

            // $data['variation']  = $markedSeller->value;
            $data['base_price'] = $markedSeller->base_price;
            $data['transport_price'] = $markedSeller->transport_price;
            $data['commission'] = $markedSeller->commission;
            $data['is_mark']    = $markedSeller->is_mark ? true : false;

            foreach($markedSeller->value as $variation){
                $variation['final_price'] = ($variation['price'] + $data['base_price']) * $variation['quantity'];
                $data['variation'][]  = $variation;
                $data['total_quantity'] += $variation['quantity'];
                $data['final_variation_price'] += $variation['final_price'];
            }

            $seller_commodity_product   = SellerCommodityProduct::where('user_id', $markedSeller->user_id)->where('commodity_product_id', $markedSeller->commodity_product_id)->where('brand_id', $markedSeller->brand_id)->first();
            $data['loading_charge']     = $seller_commodity_product->loading_charge;
            $data['insurance_charge']   = $seller_commodity_product->insurance_charge;
            $data['quality_charge']     = $seller_commodity_product->quality_charge ?? 0;
            $data['gst']                = $seller_commodity_product->gst;
            $data['tcs']                = $seller_commodity_product->tcs;
            $data['gst_amount']         = $data['final_variation_price']*$data['gst']/100;
            $data['tcs_amount']         = ($data['final_variation_price'] + $data['gst_amount'])*$data['tcs_amount']/100;

            $data['ex_price']           = $data['final_variation_price'] + $data['gst_amount'] + $data['tcs_amount'] + $data['total_charges'];
            $data['for_price']          = $data['ex_price'] + $data['transport_price'];
            $data['required_booking_amount'] = $data['for_price'] * 30 / 100;
            // $data['status']     = $this->getMarkedSellerProductEnquiry->status;
        }else{
            $data['variation']   = $this->variation;
        }

        return $data;
    }
}
