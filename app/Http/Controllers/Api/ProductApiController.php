<?php

namespace App\Http\Controllers\Api;

use App\Models\HomeProduct;
use Illuminate\Http\Request;
use App\Models\CommodityProduct;
use App\Http\Controllers\Controller;
use App\Models\SellerCommodityProduct;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\AllSellerCommodityProductResource;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $commodity = CommodityProduct::first();
            $id = 0;
            $q = HomeProduct::with('getUser', 'getCommodityProduct', 'getSellerCommodityProduct', 'getBrand');
            // if($commodity){
            //     $id = $commodity->id;
            // }
            // if($request->commodity_product_id){
            //     $id = $request->commodity_product_id;
            // }
            if($request->search){
                $searchTerm = $request->search;
                $q->whereHas('getCommodityProduct', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                })->orWhereHas('getBrand', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
            }
            $list = $q->paginate(getPaginate());
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
        try {

            $list = CommodityProduct::active()->latest()->get(['id', 'name', 'slug', 'thumbnail']);
            foreach ($list as $data) {
                $data->thumbnail = imageUrl($data->thumbnail);
            }
            return response([
                'success'        => true,
                'products_list'  => $list
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function show($id)
    {
        try {
            $data = HomeProduct::with('getCommodityProduct', 'getSellerCommodityProduct', 'getBrand', 'getSellerStatePrice')->findOrFail($id);
            $seller_product_list = SellerCommodityProduct::where('id', '!=', $id)->where('user_id', $data->user_id)->with('getBrand')->latest()->get();
            return response([
                'success'        => true,
                'products_data'  => new ProductDetailResource($data),
                'seller_product_list' => AllSellerCommodityProductResource::collection($seller_product_list)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function allSellerCommodityProductList()
    {
        $list = SellerCommodityProduct::get();
        return response([
            'success'        => true,
            'products_data'  => AllSellerCommodityProductResource::collection($list)
        ],200);
    }
}
