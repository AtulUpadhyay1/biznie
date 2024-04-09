<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Http\Controllers\Controller;
use App\Models\ProductEnquiryHistory;
use App\Http\Resources\Customer\ProductEnquiryResource;

class ProductEnquiryApiController extends Controller
{
    public function index()
    {
        $list = ProductEnquiry::where('user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry')->paginate(getPaginate());
        return ProductEnquiryResource::collection($list);
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
}
