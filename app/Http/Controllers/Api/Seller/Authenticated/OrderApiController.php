<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;
use App\Http\Resources\Seller\OrderResource;
use App\Http\Resources\Seller\OrderDetailResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('seller_user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit')->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function show($id)
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit', 'getCommodityProduct.getCategory', 'getDrivers')->find($id);
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
        $data->seller_quality_check_message = $request->message;
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

    public function updateFinalQuantity(Request $request)
    {
        $request->validate([
            'driver_id'         => 'required',
            'order_id'          => 'required',
            'final_quantity'    => 'required'
        ]);
        $data = CommodityProductOrderDriver::where('id', $request->driver_id)->where('order_id', $request->order_id)->first();
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $data->final_quantity_by_seller = $request->final_quantity;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Final quantity updated successfully.',
        ],200);
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'order_id'  => 'required',
            'status'    => 'required'
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $data->status = $request->status;
        $history = $data->history;
        $history[] = ['status' => 'Order ' .ucfirst($request->status). ' By Seller', 'created_at' => Carbon::now()];
        $data->history = $history;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Order status updated successfully.',
        ],200);
    }
}
