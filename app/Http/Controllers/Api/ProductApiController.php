<?php

namespace App\Http\Controllers\Api;

use App\Models\HomeProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $list = HomeProduct::with('getUser', 'getCommodityProduct', 'getSellerCommodityProduct', 'getBrand')->get();
        return response([
            'success'   => true,
            'list'      => ProductResource::collection($list)
        ],200);
    }
}
