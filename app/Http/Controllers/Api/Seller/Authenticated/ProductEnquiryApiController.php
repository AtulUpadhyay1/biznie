<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SellerProductEnquiry;

class ProductEnquiryApiController extends Controller
{
    public function index(Request $request)
    {
        $list = SellerProductEnquiry::where('user_id', auth()->id())->latest()->get();
        return $list;
    }
}
