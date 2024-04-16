<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderLedger;
use App\Http\Resources\Customer\OrderResource;
use App\Http\Resources\Customer\OrderDetailResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('customer_user_id', auth()->id())->with('getBrand')->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function ledger($order_id)
    {
        $list = CommodityProductOrderLedger::where('order_id', $order_id)->get(['transaction_id', 'type', 'amount']);
        return response([
            'success'   => true,
            'data'      => $list
        ],200);
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
}
