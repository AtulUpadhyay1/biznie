<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SellerProductEnquiry;
use App\Http\Resources\Seller\ProductEnquiryResource;

class ProductEnquiryApiController extends Controller
{
    public function index(Request $request)
    {
        $list = SellerProductEnquiry::where('user_id', auth()->id())->latest()->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getSellerCommodityProduct')->paginate(getPaginate());
        return ProductEnquiryResource::collection($list);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'variation' => 'required|array',
            'price'     => 'required|array',
        ]);

        $data               = SellerProductEnquiry::find($id);
        $data->value        = $request->variation;
        $data->price        = $request->price;
        $data->delivery_by  = 'seller';
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Product enquiry updated successfully.'
        ],200);
    }
}
