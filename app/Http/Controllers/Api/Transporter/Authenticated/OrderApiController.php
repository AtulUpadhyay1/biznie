<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Http\Resources\Transporter\OrderResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('transporter_user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit', 'getProductEnquiry')->latest()->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    /** The route existed with no method behind it, so every call failed. */
    public function show($id)
    {
        $data = CommodityProductOrder::where('transporter_user_id', auth()->id())
            ->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit', 'getProductEnquiry')
            ->find($id);

        if (! $data) {
            return response(['success' => false, 'message' => 'Order not found.'], 404);
        }

        return response(['success' => true, 'data' => new OrderResource($data)], 200);
    }
}
