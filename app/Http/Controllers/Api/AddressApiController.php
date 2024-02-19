<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AddressApiController extends Controller
{
    public function getAddress($pincode)
    {
        try {
            $data = Address::where('pincode', $pincode)->first(['pincode', 'city', 'state', 'country']);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Pincode not found.',
                ],400);
            }

            return response([
                'success'   => true,
                'data'      => $data
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
