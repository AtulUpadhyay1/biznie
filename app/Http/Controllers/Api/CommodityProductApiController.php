<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CommodityProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommodityProductResource;

class CommodityProductApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $list = CommodityProduct::active()->latest();
            if(isset($request->category_id) && $request->category_id){
                $list = $list->where('category_id', $request->category_id);
            }
            $list = $list->get();

            return response([
                'success'   => true,
                'products_list'  => CommodityProductResource::collection($list)
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
