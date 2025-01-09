<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
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
            'unit'              => $this->getSellerCommodityProduct->getUnit ? [
                    'id'        => $this->getSellerCommodityProduct->getUnit->id,
                    'name'      => $this->getSellerCommodityProduct->getUnit->name
                ] : [],
            'commodity_product' => $this->getSellerCommodityProduct ? [
                    'id'        => $this->getSellerCommodityProduct->id,
                    'name'      => $this->getSellerCommodityProduct->name,
                    'thumbnail' => $this->getSellerCommodityProduct->thumbnail ? imageUrl($this->getSellerCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),

                    'category'  => $this->getSellerCommodityProduct->getCategory ? [
                        'id'    => $this->getSellerCommodityProduct->getCategory->id,
                        'name'  => $this->getSellerCommodityProduct->getCategory->name,

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
            'delivery_by'       => $this->delivery_by,
            'loading_address'   => $this->loading_address,
            // 'price'             => $this->price,
            'packaging_charge'  => [],
            'other_charge'      => [],
            'other_quantity_charge'    => [],
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

        $seller_commodity_product = $this->getSellerCommodityProduct;
        if($seller_commodity_product){

            // $data['variation']  = $seller_commodity_product->value;
            $data['base_price'] = $this->base_price;
            $data['transport_price'] = $this->transport_price;
            $data['commission'] = $seller_commodity_product->commission;
            $data['is_mark']    = $seller_commodity_product->is_mark ? true : false;

            // $seller_commodity_product   = SellerCommodityProduct::where('user_id', $seller_commodity_product->user_id)->where('commodity_product_id', $seller_commodity_product->commodity_product_id)->where('brand_id', $seller_commodity_product->brand_id)->first();

            $packaging_arr = [];
            foreach ($seller_commodity_product->packaging_type ?? [] as $packaging_charge) {
                $packaging_arr['name'] = getPackagingType($packaging_charge)->name;
                $packaging_arr['price'] = $seller_commodity_product->packaging_type_price[$packaging_charge];
                $data['total_charges'] += $packaging_arr['price'];
                $data['packaging_charge'][] = $packaging_arr;
            }

            $other_charges_arr = [];
            foreach ($seller_commodity_product->charge_name ?? [] as $charge_key => $charge_name) {
                $other_charges_arr['name'] = $charge_name;
                $other_charges_arr['price'] = $seller_commodity_product->charge_price[$charge_key];
                $other_charges_arr['operator'] = $seller_commodity_product->operator[$charge_key];

                if($other_charges_arr['operator'] == "+"){
                    $data['total_charges'] += $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "-"){
                    $data['total_charges'] -= $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "*"){
                    $data['total_charges'] += $data['ex_price'] * $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "/"){
                    $data['total_charges'] += $data['ex_price'] / $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "%"){
                    $data['total_charges'] += $data['ex_price'] * ($other_charges_arr['price'] / 100);
                }
                $data['other_charge'][] = $other_charges_arr;
            }

            if($seller_commodity_product && $seller_commodity_product->is_quality){
                $other_quantity_charge_arr = [];
                foreach ($seller_commodity_product->quality ?? [] as $quality_key => $quality) {
                    $other_quantity_charge_arr['name']          = $quality;
                    $other_quantity_charge_arr['quality_price'] = $seller_commodity_product->quality_price[$quality_key];
                    $data['total_charges'] += $other_quantity_charge_arr['quality_price'];
                    $data['other_quantity_charge'][] = $other_quantity_charge_arr;
                }
            }

            foreach($this->value as $variation){
                $variation['tax']               = ($variation['price'] + $data['base_price']) * $seller_commodity_product->gst / 100;
                $variation['per_unit_price']    = ($variation['price'] + $data['base_price']) + $variation['tax'] + $data['total_charges'];
                $variation['final_price']       = $variation['per_unit_price'] * $variation['quantity'];
                $data['variation'][]            = $variation;
                $data['total_quantity']         += $variation['quantity'];
                $data['final_variation_price']  += $variation['final_price'];
                $data['gst_amount']             += $variation['tax'];
            }

            $data['loading_charge']     = $seller_commodity_product->loading_charge;
            $data['insurance_charge']   = $seller_commodity_product->insurance_charge;
            $data['quality_charge']     = $seller_commodity_product->quality_charge ?? 0;
            $data['gst']                = $seller_commodity_product->gst;
            $data['tcs']                = $seller_commodity_product->tcs;

            $data['tcs_amount']         = $data['final_variation_price']*$data['tcs_amount']/100;

            $data['ex_price']           = $data['final_variation_price'] + $data['tcs_amount'];

            $data['for_price']          = $data['ex_price'] + $data['transport_price'];
            $data['required_booking_amount'] = $data['for_price'] * 30 / 100;
            // $data['status']     = $this->getMarkedSellerProductEnquiry->status;
        }else{
            $data['variation']   = $this->variation;
        }

        return $data;
    }
}
