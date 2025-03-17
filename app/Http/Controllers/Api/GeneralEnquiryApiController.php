<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GeneralEnquiry;
use App\Http\Controllers\Controller;

class GeneralEnquiryApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'                          => 'required',
            'company_name'                  => 'required',
            'contact_number'                => 'required',
            'email'                         => 'required|email',
            'gst_number'                    => 'required',
            'delivery_location'             => 'required',
            'brand_id'                      => 'required|integer|exists:brands,id',
            'seller_commodity_product_id'   => 'required|integer|exists:seller_commodity_products,id',
            'requirement'                   => 'required',
            'message'                       => 'required',
        ]);

        $user = User::where('email', $request->email)->where('phone', $request->contact_number)->first();

        $data = new GeneralEnquiry;
        $data->name = $request->name;
        $data->user_id = $user ? $user->id : null;
        $data->company_name = $request->company_name;
        $data->contact_number = $request->contact_number;
        $data->email = $request->email;
        $data->gst_number = $request->gst_number;
        $data->delivery_location = $request->delivery_location;
        $data->brand_id = $request->brand_id;
        $data->seller_commodity_product_id = $request->seller_commodity_product_id;
        $data->requirement = $request->requirement;
        $data->message = $request->message;
        $data->status = 'pending';
        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'General enquiry submitted successfully',
        ], 200);
    }
}
