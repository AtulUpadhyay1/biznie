<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
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
        $data = SellerProductEnquiry::with('getBrand', 'getSellerCommodityProduct')->findOrFail($id);
        return response([
            'success'   => true,
            'data'      => new ProductEnquiryDetailResource($data)
        ],200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'variation' => 'required|array',
            'price'     => 'required|array',
            'base_price'=> 'required',
            'loading_address' => 'required|array',
        ]);

        $data               = SellerProductEnquiry::find($id);
        $data->value        = $request->variation;
        $data->price        = $request->price;
        $data->base_price   = $request->base_price;
        $data->loading_address = $request->loading_address;
        $data->delivery_by  = 'seller';
        $data->status       = 'replied';
        $data->message      = $request->message;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Product enquiry updated successfully.'
        ],200);
    }
}
