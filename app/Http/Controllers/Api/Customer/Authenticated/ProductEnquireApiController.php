<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Http\Controllers\Controller;

class ProductEnquireApiController extends Controller
{
    public function index()
    {

    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'commodity_product_id'  => 'required',
            'brand_id'              => 'required',
            'origin_city'           => 'required',
            'variation'             => 'required',
            'billing_address'       => 'required',
            'delivery_address'      => 'required',
        ]);

        try {

            $data = new ProductEnquiry;
            $data->user_id              = auth()->id();
            $data->commodity_product_id = $request->commodity_product_id;
            $data->brand_id             = $request->brand_id;
            $data->origin_city          = $request->origin_city;
            $data->variation            = $request->variation;
            $data->billing_address      = $request->billing_address;
            $data->delivery_address     = $request->delivery_address;
            $data->consignee_detail     = $request->consignee_detail;
            $data->purpose              = $request->purpose;
            $data->description          = $request->description;
            $data->save();
            return response([
                'success'   => true,
                'message'   => 'Product enquiry added successfully.'
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }
}
