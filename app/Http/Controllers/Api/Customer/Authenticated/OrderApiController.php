<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;
use App\Models\CommodityProductOrderLedger;
use App\Http\Resources\Customer\OrderResource;
use App\Http\Resources\Customer\OrderDetailResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('customer_user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit')->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function ledger($order_id)
    {
        $list = CommodityProductOrderLedger::where('order_id', $order_id)->get(['transaction_id', 'type', 'amount', 'description', 'created_at']);
        return response([
            'success'   => true,
            'data'      => $list
        ],200);
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

    public function qualityCheckStatus(Request $request)
    {
        $request->validate([
            'order_id'                      => 'required',
            'quality_check_image_status'    => 'required',
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $data->quality_check_image_status   = $request->quality_check_image_status;
        $data->quality_check_image_status_updated_by   = 'customer';
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Quality check status updated successfully.',
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
        $data->final_quantity_by_customer = $request->final_quantity;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Final quantity updated successfully.',
        ],200);
    }
}
