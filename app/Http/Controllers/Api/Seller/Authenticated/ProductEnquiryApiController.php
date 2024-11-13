<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Http\Controllers\Controller;
use App\Models\SellerProductEnquiry;
use App\Http\Resources\Seller\ProductEnquiryResource;
use App\Http\Resources\Seller\ProductEnquiryDetailResource;

class ProductEnquiryApiController extends Controller
{
    public function index(Request $request)
    {
        $list = SellerProductEnquiry::where('user_id', auth()->id())->latest()->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getSellerCommodityProduct')->paginate(getPaginate());
        return ProductEnquiryResource::collection($list);
    }

    public function show($id)
    {
        $data = SellerProductEnquiry::where('user_id', auth()->id())->with('getBrand', 'getSellerCommodityProduct')->find($id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Product enquiry not found.'
            ],400);
        }

        $bidding_list = SellerProductEnquiry::where('user_id', '!=', auth()->id())
            ->where('product_enquiries_id', $data->product_enquiries_id)
            ->where('status', '!=', 'pending')
            ->orderBy('base_price', 'asc')
            ->with(['getUser:id,name,phone'])
            ->select(['base_price', 'user_id'])
            ->get()
            ->map(function ($item) {
                unset($item->user_id);
                return $item;
            });
        return response([
            'success'   => true,
            'data'      => new ProductEnquiryDetailResource($data),
            'bidding_list' => $bidding_list
        ],200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'variation' => 'required|array',
            'price'     => 'required|array',
            'base_price'=> 'required',
            'loading_address' => 'required|array',
            'price_validity'  => 'required'
        ]);

        $data               = SellerProductEnquiry::find($id);
        $data->value        = $request->variation;
        $data->price        = $request->price;
        $data->base_price   = $request->base_price;
        $data->loading_address = $request->loading_address;
        $data->transport_price = $request->transport_price;
        $data->price_validity = $request->price_validity;
        $data->delivery_by  = $request->delivery_by ?? 'biznie';
        $data->description  = $request->description;
        $data->status       = 'replied';

        $history = $data->history;
        $history[] = ['status' => 'Replied', 'created_at' => Carbon::now()];
        $data->history = $history;

        $data->message      = $request->message;
        $data->save();

        $enquiry = ProductEnquiry::findOrFail($data->product_enquiries_id);
        if($enquiry){
            if($enquiry->status != 'Seller Replied'){
                $enquiry->status = 'Seller Replied';
                $history = $enquiry->history;
                $history[] = ['status' => 'Seller Replied', 'created_at' => Carbon::now()];
                $enquiry->history = $history;
                $enquiry->save();
            }
        }

        return response([
            'success'   => true,
            'message'   => 'Product enquiry updated successfully.'
        ],200);
    }
}
