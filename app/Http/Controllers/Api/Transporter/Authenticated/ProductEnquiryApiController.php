<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransporterProductEnquiry;

class ProductEnquiryApiController extends Controller
{
    public function index(Request $request)
    {
        $list = TransporterProductEnquiry::where('user_id', auth()->id())->latest()->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getSellerCommodityProduct')->paginate(getPaginate());
        return $list;
    }
}
