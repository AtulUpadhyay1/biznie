<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Http\Resources\Seller\OrderResource;
use App\Http\Resources\Seller\OrderDetailResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('seller_user_id', auth()->id())->with('getBrand')->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function show($id)
    {
        $data = CommodityProductOrder::find($id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        return response([
            'success'   => true,
            'data'      => new OrderDetailResource($data)
        ],200);
    }

    public function qualityCheck(Request $request)
    {
        $request->validate([
            'order_id'  => 'required',
            'image'     => 'required',
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }

        $data->quality_check_image = $request->image;
        $data->quality_check_image_status = 'pending';
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Quality check image updated successfully.',
        ],200);
    }

    public function updateInvoice(Request $request)
    {
        $request->validate([
            'order_id'      => 'required',
            'purpose'       => 'required',
            'purpose_file'  => 'required',
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $purpose            = $request->purpose;
        $data[$purpose]     = $request->purpose_file;
        $data->save();

        return response([
            'success'   => true,
            'message'   => ucfirst(str_replace("_"," ",$purpose)).' updated successfully.',
        ],200);
    }
}
