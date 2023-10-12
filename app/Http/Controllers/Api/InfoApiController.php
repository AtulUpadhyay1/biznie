<?php

namespace App\Http\Controllers\Api;

use App\Models\GstType;
use App\Models\SellerType;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use App\Http\Controllers\Controller;

class InfoApiController extends Controller
{
    public function registrationFormInfo()
    {
        try {

            return response([
                'success'           => true,
                'business_category' => BusinessCategory::active()->get(['id', 'name']),
                'business_type'     => BusinessType::active()->get(['id', 'name']),
                'seller_type'       => SellerType::active()->get(['id', 'name']),
                'gst_type'          => GstType::active()->get(['id', 'name']),
            ]);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }
}
