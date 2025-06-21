<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CommodityProductOrderDriverResource;

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
            'enquiry_id'        => $this->getProductEnquiry->unique_id,
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
            'packaging_charge'  => [],
            'other_charge'      => [],
            'other_quantity_charge'    => [],
            'total_quantity'    => 0,
            'base_price'        => $this->base_price,
            'loading_charge'    => 0,
            'insurance_charge'  => 0,
            'quality_charge'    => 0,
            'gst'               => 0,
            'tcs'               => 0,
            'gst_amount'        => 0,
            'tcs_amount'        => 0,
            'total_charges'     => 0,
            'commission'        => $this->commission,
            'commission_type'   => '',
            'total_amount'      => $this->total_amount,
            'paid_amount'       => $this->paid_amount,
            'due_amount'        => $this->due_amount,
            'final_amount'      => $this->final_amount,
            'final_variation_price' => 0,
            'ex_price'          => 0,
            'transport_price'   => $this->transport_price,
            'for_price'         => 0,
            'required_booking_amount' => 0,
            'token_amount'      => $this->token_amount,
            'loading_address'   => $this->loading_address,
            'status'            => $this->status,
            'updated_at'        => dateTimeFormat($this->updated_at),
            'quality_check_image'           => $this->quality_check_image ?? [],
            'quality_check_image_status'    => $this->quality_check_image_status,
            'quality_check_image_status_updated_by' => $this->quality_check_image_status_updated_by,
            'quality_check_message'         => $this->customer_quality_check_message,
            'quality_check_visibility'      => $this->customer_quality_check_visibility ? ($this->customer_quality_check_visibility ? true : false) : false,
            'final_quantity_by_seller'      => $this->final_quantity_by_seller,
            'final_quantity_by_customer'    => $this->final_quantity_by_customer,
            'invoice'                       => $this->invoice ? imageUrl($this->invoice) : null,
            'final_invoice'                 => $this->final_invoice ? imageUrl($this->final_invoice) : null,
            'e_bill'                        => $this->e_bill ? imageUrl($this->e_bill) : null,
            'quality_check_certificate'     => $this->quality_check_certificate ? imageUrl($this->quality_check_certificate) : null,
            'insurance_certificate'         => $this->insurance_certificate ? imageUrl($this->insurance_certificate) : null,
            'other'                         => $this->other ? imageUrl($this->other) : null,
            'seller_credit_due_date'        => $this->seller_credit_due_date,
            'customer_credit_due_date'      => $this->customer_credit_due_date,
            'update_for'                    => $this->update_for,
            'driver_list'                   => $this->getDrivers ? CommodityProductOrderDriverResource::collection($this->getDrivers) : [],
        ];

        $seller_commodity_product   = SellerCommodityProduct::where('user_id', $this->seller_user_id)->where('commodity_product_id', $this->commodity_product_id)->where('brand_id', $this->brand_id)->first();

        $enquiry_data = ProductEnquiry::with('getBrand', 'getUser', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry', 'getMarkedSellerProductEnquiry.getUser')->findOrFail($this->product_enquiries_id);
        $markedSeller = $enquiry_data->getMarkedSellerProductEnquiry;
        $data['commission_type'] = $markedSeller->commission_type;
        if($data['commission_type'] == 'exclude'){
            $data['base_price'] += $data['commission'];
        }

        $packaging_arr = [];
        foreach ($seller_commodity_product->packaging_type as $packaging_charge) {
            $packaging_arr['name'] = getPackagingType($packaging_charge)->name;
            $packaging_arr['price'] = isset($seller_commodity_product->packaging_type_price[$packaging_charge]) ? $seller_commodity_product->packaging_type_price[$packaging_charge] : 0;
            $data['total_charges'] += $packaging_arr['price'];
            $data['packaging_charge'][] = $packaging_arr;
        }

        $other_charges_arr = [];
        foreach ($seller_commodity_product->charge_name as $charge_key => $charge_name) {
            $other_charges_arr['name'] = $charge_name;
            $other_charges_arr['price'] = isset($seller_commodity_product->charge_price[$charge_key]) ? $seller_commodity_product->charge_price[$charge_key] : 0;
            $other_charges_arr['operator'] = isset($seller_commodity_product->operator[$charge_key]) ? $seller_commodity_product->operator[$charge_key] : "";

            if($other_charges_arr['operator']){
                if($other_charges_arr['operator'] == "+"){
                    $data['total_charges'] += $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "-"){
                    $data['total_charges'] -= $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "*"){
                    $data['total_charges'] += 0;
                }elseif($other_charges_arr['operator'] == "/"){
                    $data['total_charges'] += 0;
                }elseif($other_charges_arr['operator'] == "%"){
                    $data['total_charges'] += 0;
                }
            }
            $data['other_charge'][] = $other_charges_arr;
        }

        if($seller_commodity_product->is_quality){
            $other_quantity_charge_arr = [];
            foreach ($seller_commodity_product->quality as $quality_key => $quality) {
                $other_quantity_charge_arr['name']          = $quality;
                $other_quantity_charge_arr['quality_price'] = $seller_commodity_product->quality_price[$quality_key];
                // $data['total_charges'] += $other_quantity_charge_arr['quality_price'];
                $data['other_quantity_charge'][] = $other_quantity_charge_arr;
            }
        }

        foreach($this->value as $variation){
            // $variation['tax']               = ($variation['price'] + $data['base_price']) * $seller_commodity_product->gst / 100;
            // $variation['per_unit_price']    = ($variation['price'] + $data['base_price']) + $variation['tax'] + $data['total_charges'];
            // $variation['final_price']       = $variation['per_unit_price'] * $variation['quantity'];

            $variation['total_price']       = ($variation['price'] + $data['base_price']) + $data['total_charges'] + $seller_commodity_product->loading_charge + $seller_commodity_product->insurance_charge;
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
