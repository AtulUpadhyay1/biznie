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
}
