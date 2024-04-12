<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Http\Resources\Seller\OrderResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('seller_user_id', auth()->id())->with('getBrand')->paginate(getPaginate());
        return OrderResource::collection($list);
    }
}
