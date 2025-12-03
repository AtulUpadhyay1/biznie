<?php

namespace App\Http\Resources\Seller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CommodityProductSellerOrderLedger;
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
            'description'       => $this->description ?? $this->getProductEnquiry->description,
            'message'           => $this->message,
            'selected_quality'           => $this->getProductEnquiry->quality,
            'selected_packaging_charge'  => $this->getProductEnquiry->packaging_charge,
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
            'seller_commission' => $this->getProductEnquiry?->getMarkedSellerProductEnquiry?->commission ?? 0,
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
            'quality_check_message' => $this->seller_quality_check_message,
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
            'credit_days'                   => $this->getSellerProductEnquiry->seller_credit_days,
            'update_for'                    => $this->update_for,
            'driver_list'                   => $this->getDrivers ? CommodityProductOrderDriverResource::collection($this->getDrivers) : [],
            'invoice_list'                  => [],
            'total_invoice_amount'          => 0,

        ];

        $seller_commodity_product   = SellerCommodityProduct::where('user_id', $this->seller_user_id)->where('commodity_product_id', $this->commodity_product_id)->where('brand_id', $this->brand_id)->first();

        $enquiry_data = ProductEnquiry::with('getBrand', 'getUser', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry', 'getMarkedSellerProductEnquiry.getUser')->findOrFail($this->product_enquiries_id);
        $seller_enquiry_data = $enquiry_data->getMarkedSellerProductEnquiry;
        $data['commission_type'] = $seller_enquiry_data->commission_type;
        $data['load_within'] = $seller_enquiry_data->load_within ? $seller_enquiry_data->load_within : 0;

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

        if($data['commission_type'] == 'include'){
            $data['commission'] = $seller_enquiry_data->commission * ($data['total_quantity'] + $data['gst'] / 100);
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

        $paid_amout = CommodityProductSellerOrderLedger::where('order_id', $this->id)
            ->where('type', 'debit')
            ->sum('amount');

        $data['paid_amount'] = $paid_amout;
        $data['due_amount'] = $data['total_amount'] - $data['paid_amount'];

        if($this->getDrivers->sum('amount') > 0){
            foreach ($this->getDrivers as $driver_data){
                if($driver_data->seller_invoices){
                    $expiryDate = Carbon::parse($driver_data['ebill_expiry_date']);
                    $daysLeft = $expiryDate->isToday() ? 0 : ($expiryDate->isPast() ? 0 : $expiryDate->diffInDays(now()) + 1);
                    $seller_invoices = $driver_data->seller_invoices;
                    $data['invoice_list'][] = [
                        'vehicle_number'    => $driver_data->vehicle_number,
                        'name'              => null,
                        'invoice'           => isset($seller_invoices['invoice']) ? imageUrl($seller_invoices['invoice']) : null,
                        'ebill'             => isset($seller_invoices['ebill']) ? imageUrl($seller_invoices['ebill']) : null,
                        'ebill_expiry_date' => $seller_invoices['ebill_expiry_date'] ?? null,
                        'days_left'         => $daysLeft,
                        'is_expired_today'  => $expiryDate->isToday(),
                        'transport_receipt' => isset($seller_invoices['transport_receipt']) ? imageUrl($seller_invoices['transport_receipt']) : null,
                        'amount'            => $driver_data['amount'],
                        'debit_note'        => isset($seller_invoices['debit_note']) ? imageUrl($seller_invoices['debit_note']) : null,
                        'debit_note_amount' => isset($seller_invoices['debit_note_amount']) ? $seller_invoices['debit_note_amount'] : null,
                        'credit_note'       => isset($seller_invoices['credit_note']) ? imageUrl($seller_invoices['credit_note']) : null,
                        'credit_note_amount'=> isset($seller_invoices['credit_note_amount']) ? $seller_invoices['credit_note_amount'] : null,
                    ];

                    $data['total_invoice_amount'] += $driver_data->amount;
                }
            }
        } elseif ($this->seller_invoices && count($this->seller_invoices) > 0){
            foreach ($this->seller_invoices as $invoice){
                $expiryDate = Carbon::parse($invoice['ebill_expiry_date']);
                $daysLeft = $expiryDate->isToday() ? 0 : ($expiryDate->isPast() ? 0 : $expiryDate->diffInDays(now()) + 1);
                $data['invoice_list'][] = [
                    'vehicle_number'    => null,
                    'name'              => $invoice['name'],
                    'invoice'           => $invoice['invoice_file'] ? imageUrl($invoice['invoice_file']) : null,
                    'ebill'             => $invoice['ebill'] ? imageUrl($invoice['ebill']) : null,
                    'ebill_expiry_date' => $invoice['ebill_expiry_date'],
                    'days_left'         => $daysLeft,
                    'is_expired_today'  => $expiryDate->isToday(),
                    'transport_receipt' => isset($invoice['transport_receipt']) ? imageUrl($invoice['transport_receipt']) : null,
                    'amount'            => $invoice['amount'],
                    'debit_note'        => isset($invoice['debit_note']) ? imageUrl($invoice['debit_note']) : null,
                    'debit_note_amount' => isset($invoice['debit_note_amount']) ? $invoice['debit_note_amount'] : null,
                    'credit_note'       => isset($invoice['credit_note']) ? imageUrl($invoice['credit_note']) : null,
                    'credit_note_amount'=> isset($invoice['credit_note_amount']) ? $invoice['credit_note_amount'] : null,
                ];
                $data['total_invoice_amount'] += $invoice['amount'];
            }
        }

        return $data;
    }
}
