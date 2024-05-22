<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Http\Controllers\Controller;
use App\Models\SellerProductEnquiry;
use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiryHistory;
use App\Models\CommodityProductOrderLedger;
use App\Models\SellerCommodityProductStatePrice;
use App\Http\Resources\Customer\ProductEnquiryResource;
use App\Http\Resources\Customer\ProductEnquiryDetailResource;

class ProductEnquiryApiController extends Controller
{
    public function index()
    {
        $list = ProductEnquiry::where('user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry')->latest()->paginate(getPaginate());
        return ProductEnquiryResource::collection($list);
    }

    public function show($id)
    {
        $data = ProductEnquiry::with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry')->findOrFail($id);
        return response([
            'success'   => true,
            'data'      => new ProductEnquiryDetailResource($data)
        ],200);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'commodity_product_id'  => 'required',
            'brand_id'              => 'required',
            'origin_city'           => 'required',
            'variation'             => 'required',
            'billing_address'       => 'required',
            'delivery_address'      => 'required',
        ]);

        try {

            $data = new ProductEnquiry;
            $data->user_id              = auth()->id();
            $data->commodity_product_id = $request->commodity_product_id;
            $data->brand_id             = $request->brand_id;
            $data->unique_id            = 'PE-'.time().'-'.rand(1111, 9999);
            $data->origin_city          = $request->origin_city;
            $data->variation            = $request->variation;
            $data->billing_address      = $request->billing_address;
            $data->delivery_address     = $request->delivery_address;
            $data->consignee_detail     = $request->consignee_detail;
            $data->purpose              = $request->purpose;
            $data->description          = $request->description;
            $data->price                = $request->price;
            $data->history              = [['status' => 'pending', 'created_at' => Carbon::now()]];
            $data->save();

            $data_history = new ProductEnquiryHistory;
            $data_history->user_id      = auth()->id();
            $data_history->commodity_product_id = $request->commodity_product_id;
            $data_history->brand_id             = $request->brand_id;
            $data_history->product_enquiry_id   = $data->id;
            $data_history->unique_id            = $data->unique_id;
            $data_history->origin_city          = $request->origin_city;
            $data_history->variation            = $request->variation;
            $data_history->billing_address      = $request->billing_address;
            $data_history->delivery_address     = $request->delivery_address;
            $data_history->consignee_detail     = $request->consignee_detail;
            $data_history->purpose              = $request->purpose;
            $data_history->description          = $request->description;
            $data_history->price                = $request->price;
            $data_history->save();

            $title = 'Product Enquiry';
            $body = 'Dear '.auth()->user()->name.', Your product enquiry has been successfully submitted.';
            $type = 'product_enquiry';
            $data_info = [
                'unique_id'     => $data->unique_id,
            ];
            sendNotification(auth()->user(), $title, $body, $type, $data_info, true);

            // Enquiry Send to Seller
            if(websiteSetupValue('enquiry_send_to_seller') && websiteSetupValue('enquiry_send_to_seller') == 1){
                $enquiry_data = ProductEnquiry::with('getBrand', 'getCommodityProduct')->findOrFail($data->id);
                $variation_arr = [];
                foreach($enquiry_data->variation as $variations_value){
                    foreach($variations_value['value'] as $variation){
                        $variation_data['id']      = $variation['id'];
                        $variation_data['name']    = $variation['name'];
                        $variation_data['value']   = $variation['value'];
                        $variation_arr[] = $variation_data;
                    }
                }

                $seller_ids = SellerCommodityProductStatePrice::where(function($query) use ($variation_arr){
                    foreach ($variation_arr as $variation) {
                        $query->orWhereJsonContains('value', $variation)->where('is_selected', '1');
                    }
                })->pluck('user_id')->toArray();
                $seller_ids_with_count = array_count_values($seller_ids);
                $seller_ids = array_keys(array_filter($seller_ids_with_count, function($count) use ($variation_arr){
                    return $count === count($variation_arr);
                }));

                foreach ($seller_ids as $user_id) {
                    $product_state_prices = SellerCommodityProductStatePrice::where('user_id', $user_id)->where(function($query) use ($variation_arr){
                        foreach ($variation_arr as $variation) {
                            $query->orWhereJsonContains('value', $variation);
                        }
                    })->with('getSellerCommodityProduct')->get();

                    $price_arr = [];
                    foreach ($product_state_prices as $key => $product_state_price) {
                        $price_arr[] = $product_state_price->price;
                    }

                    $new_variation_arr = [];
                    foreach($enquiry_data->variation as $key => $enquiry_variations){
                        $enquiry_variations = $enquiry_variations;
                        $enquiry_variations['price'] = $price_arr[$key] ?? 0;
                        if($enquiry_variations['price'] == 0){
                            $enquiry_variations['is_selected'] = "0";
                        }
                        $new_variation_arr[] = $enquiry_variations;
                    }

                    $data = SellerProductEnquiry::where('user_id', $user_id)->where('product_enquiries_id', $enquiry_data->id)->first();
                    if(!$data){
                        $data                   = new SellerProductEnquiry;
                    }
                    $data->user_id              = $user_id;
                    $data->product_enquiries_id = $enquiry_data->id;
                    $data->customer_user_id     = $enquiry_data->user_id;
                    $data->commodity_product_id = $enquiry_data->commodity_product_id;
                    $data->brand_id             = $enquiry_data->brand_id;
                    $data->unique_id            = $enquiry_data->unique_id;
                    $data->origin_city          = $enquiry_data->origin_city;
                    $data->value                = $new_variation_arr;
                    $data->billing_address      = $enquiry_data->billing_address;
                    $data->delivery_address     = $enquiry_data->delivery_address;
                    $data->consignee_detail     = $enquiry_data->consignee_detail;
                    $data->purpose              = $enquiry_data->purpose;
                    $data->description          = $enquiry_data->description;
                    $data->message              = $enquiry_data->message;
                    $data->price                = $price_arr;
                    $data->base_price           = $product_state_prices[0]->getSellerCommodityProduct->base_price;
                    $data->loading_address      = $product_state_prices[0]->getSellerCommodityProduct->loading_address;
                    $data->status               = $data->status ?? 'pending';
                    if(!$data->history){
                        $data->history          = [['status' => 'New Enquiry', 'created_at' => Carbon::now()]];
                    }
                    $data->save();

                    $user = User::find($user_id);

                    $title = 'New Product Enquiry';
                    $body = 'Dear '.$user->name.', Your have new product enquiry. Please fill your price.';
                    $type = 'product_enquiry';
                    $data_info = [
                        'unique_id'     => $data->unique_id,
                    ];
                    sendNotification($user, $title, $body, $type, $data_info, true);

                }

                $enquiry_data->status = 'Enquiry Send To Seller';
                $history = $enquiry_data->history;
                $history[] = ['status' => 'Enquiry Send To Seller', 'created_at' => Carbon::now()];
                $enquiry_data->history = $history;
                $enquiry_data->save();
            }

            return response([
                'success'   => true,
                'message'   => 'Product enquiry added successfully.'
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }

    }

    public function update(Request $request, $id)
    {
        try {

            $data = ProductEnquiry::findOrFail($id);
            $data->price    = $request->price;
            $data->message  = [[
                'message'   => $request->message,
                'date'      => date('Y-m-d H:i:s')
            ]];
            $data->save();

            $data_history = new ProductEnquiryHistory;
            $data_history->user_id              = auth()->id();
            $data_history->commodity_product_id = $data->commodity_product_id;
            $data_history->brand_id             = $data->brand_id;
            $data_history->product_enquiry_id   = $data->id;
            $data_history->unique_id            = $data->unique_id;
            $data_history->origin_city          = $data->origin_city;
            $data_history->variation            = $data->variation;
            $data_history->billing_address      = $data->billing_address;
            $data_history->delivery_address     = $data->delivery_address;
            $data_history->consignee_detail     = $data->consignee_detail;
            $data_history->purpose              = $data->purpose;
            $data_history->description          = $data->description;
            $data_history->price                = $data->price;
            $data_history->message              = $data->message;
            $data_history->save();

            return response([
                'success'   => true,
                'message'   => 'Product enquiry updated successfully.'
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function enquiryToOrder(Request $request, $id)
    {
        $request->validate([
            'token_amount'  => 'required|numeric|min:1'
        ]);

        $enquiry_data = ProductEnquiry::with('getMarkedSellerProductEnquiry')->find($id);
        $enquiry_data->status = 'ordered';
        $history = $enquiry_data->history;
        $history[] = ['status' => 'Ordered', 'created_at' => Carbon::now()];
        $enquiry_data->history = $history;
        $enquiry_data->save();

        $mark_seller = $enquiry_data->getMarkedSellerProductEnquiry;
        $mark_seller->status = 'ordered';
        $history = $mark_seller->history;
        $history[] = ['status' => 'Ordered', 'created_at' => Carbon::now()];
        $mark_seller->history = $history;
        $mark_seller->save();

        $order                              = new CommodityProductOrder;
        $order->seller_user_id              = $mark_seller->user_id;
        $order->customer_user_id            = $mark_seller->customer_user_id;
        $order->product_enquiries_id        = $mark_seller->product_enquiries_id;
        $order->seller_product_enquiries_id = $mark_seller->id;
        $order->commodity_product_id        = $mark_seller->commodity_product_id;
        $order->brand_id                    = $mark_seller->brand_id;
        $order->unique_id                   = $mark_seller->unique_id;
        $order->order_id                    = 'OID-'.time().'-'.rand(1111, 9999);
        $order->origin_city                 = $mark_seller->origin_city;
        $order->value                       = $mark_seller->value;
        $order->billing_address             = $mark_seller->billing_address;
        $order->delivery_address            = $mark_seller->delivery_address;
        $order->purpose                     = $mark_seller->purpose;
        $order->description                 = $mark_seller->description;
        $order->message                     = $mark_seller->message;
        $order->price                       = $mark_seller->price;
        $order->base_price                  = $mark_seller->base_price;
        $order->token_amount                = $request->token_amount;
        $order->transport_price             = $mark_seller->transport_price;
        $order->commission                  = $mark_seller->commission;
        $order->loading_address             = $mark_seller->loading_address;
        $order->delivery_by                 = $mark_seller->delivery_by;
        $order->status                      = 'pending';
        $order->history                     = ['status' => 'Order Confirmed By Customer', 'created_at' => Carbon::now()];
        $order->save();

        $debit_ledger                       = new CommodityProductOrderLedger;
        $debit_ledger->order_id             = $order->id;
        $debit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $debit_ledger->type                 = 'debit';
        $debit_ledger->amount               = $order->token_amount;
        $debit_ledger->save();

        $credit_ledger                       = new CommodityProductOrderLedger;
        $credit_ledger->order_id             = $order->id;
        $credit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $credit_ledger->type                 = 'credit';
        $credit_ledger->amount               = $order->base_price;
        $credit_ledger->save();

        return response([
            'success'   => true,
            'message'   => 'Product ordered successfully.'
        ],200);
    }
}
