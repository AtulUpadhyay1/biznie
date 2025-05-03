<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use App\Models\SellerCommodityProduct;
use App\Models\TransporterProductEnquiry;
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
            'order_id'          => $this->getCommodityProductOrder ? $this->getCommodityProductOrder->id : NULL,
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
            'selected_quality'           => $this->quality,
            'selected_packaging_charge'  => $this->packaging_charge,
            'unit_price'        => $this->unit_price,
            'billing_address'   => $this->billing_address,
            'delivery_address'  => $this->delivery_address,
            'consignee_detail'  => $this->consignee_detail,
            'purpose'           => $this->purpose,
            'description'       => $this->description,
            'message'           => $this->message,
            // 'price'             => $this->price,
            'packaging_charge'  => [],
            'other_charge'      => [],
            'other_quantity_charge'    => [],
            'transporter_detail' => [],
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
            'commission_type'   => '',
            'commission'        => 0,
            'final_variation_price' => 0,
            'ex_price'          => 0,
            'transport_price'   => 0,
            'for_price'         => 0,
            'required_booking_amount' => 0,
            'is_mark'           => false,
            'status'            => $this->status,
            'created_at'        => dateTimeFormat($this->created_at),
            'credit_days'       => $this->customer_credit_days ? $this->customer_credit_days : auth()->user()->credit_days,
        ];
        $markedSeller = $this->getMarkedSellerProductEnquiry;
        if($markedSeller){

            // $data['variation']  = $markedSeller->value;
            $data['base_price'] = $markedSeller->base_price;
            $data['transport_price'] = $markedSeller->transport_price;
            $data['commission_type'] = $markedSeller->commission_type;
            $data['commission'] = (int)$markedSeller->commission;
            $data['is_mark']    = $markedSeller->is_mark ? true : false;
            if($data['commission_type'] == 'exclude'){
                $data['base_price'] += $data['commission'];
            }

            $seller_commodity_product   = SellerCommodityProduct::where('user_id', $markedSeller->user_id)->where('commodity_product_id', $markedSeller->commodity_product_id)->where('brand_id', $markedSeller->brand_id)->first();

            $packaging_arr = [];
            foreach ($seller_commodity_product->packaging_type ?? [] as $packaging_charge) {
                $packaging_arr['name'] = getPackagingType($packaging_charge)->name;
                $packaging_arr['price'] = isset($seller_commodity_product->packaging_type_price[$packaging_charge]) ? $seller_commodity_product->packaging_type_price[$packaging_charge] : 0;
                $data['total_charges'] += $packaging_arr['price'];
                $data['packaging_charge'][] = $packaging_arr;
            }

            $other_charges_arr = [];
            $extra_charges = 0;

            foreach ($seller_commodity_product->charge_name ?? [] as $charge_key => $charge_name) {
                $other_charges_arr['name'] = $charge_name;
                $other_charges_arr['price'] = isset($seller_commodity_product->charge_price[$charge_key]) ? $seller_commodity_product->charge_price[$charge_key] : 0;
                $other_charges_arr['operator'] = isset($seller_commodity_product->operator[$charge_key]) ? $seller_commodity_product->operator[$charge_key] : "";

                if($other_charges_arr['operator']){
                    if($other_charges_arr['operator'] == "+"){
                        $extra_charges += $other_charges_arr['price'];
                    }elseif($other_charges_arr['operator'] == "-"){
                        $extra_charges -= $other_charges_arr['price'];
                    }elseif($other_charges_arr['operator'] == "*"){
                        $extra_charges += 0;
                    }elseif($other_charges_arr['operator'] == "/"){
                        $extra_charges += 0;
                    }elseif($other_charges_arr['operator'] == "%"){
                        $extra_charges += 0;
                    }
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

            foreach($markedSeller->value as $variation){

                $variation['total_price']       = ($variation['price'] + $data['base_price']) + $extra_charges + $seller_commodity_product->loading_charge + $seller_commodity_product->insurance_charge;
                $variation['tax']               = round(($variation['total_price']) * $seller_commodity_product->gst / 100);
                $variation['per_unit_price']    = $variation['total_price'] + $variation['tax'];
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

            $data['for_price']          = $data['ex_price'] + $data['transport_price'] * $data['total_quantity'];
            $data['required_booking_amount'] = $data['for_price'] * 30 / 100;
            // $data['status']     = $this->getMarkedSellerProductEnquiry->status;
        }else{
            $data['variation']   = $this->variation;
        }

        $transporter = TransporterProductEnquiry::where('product_enquiries_id', $this->id)->where('is_mark', '1')->with('getUser')->first();
        if($transporter){
            $data['transporter_detail'] = [
                'name'          => $transporter->getUser->name,
                'phone'         => $transporter->getUser->phone,
                'min_price'     => $transporter->min_price,
                'max_price'     => $transporter->max_price,
                'price'         => $transporter->price,
            ];
        }
        return $data;
    }
}
