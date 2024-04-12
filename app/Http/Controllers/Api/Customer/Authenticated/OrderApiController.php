<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Http\Resources\Customer\OrderResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('customer_user_id', auth()->id())->with('getBrand')->paginate(getPaginate());
        return OrderResource::collection($list);
    }
}
