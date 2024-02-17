<?php

namespace App\Http\Controllers\Api;

use App\Models\HomeProduct;
use Illuminate\Http\Request;
use App\Models\CommodityProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $commodity = CommodityProduct::first();
            $id = 0;
            if($commodity){
                $id = $commodity->id;
            }
            if($request->commodity_product_id){
                $id = $request->commodity_product_id;
            }
            $list = HomeProduct::where('commodity_product_id', $id)->with('getUser', 'getCommodityProduct', 'getSellerCommodityProduct', 'getBrand')->paginate(getPaginate());
            return ProductResource::collection($list);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function commodityProductList(Request $request)
    {
        $list = CommodityProduct::active()->latest()->get(['id', 'name', 'slug', 'thumbnail']);
        foreach ($list as $data) {
            $data->thumbnail = imageUrl($data->thumbnail);
        }
        return response([
            'success'        => true,
            'products_list'  => $list
        ],200);
    }
}
