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

    public function state()
    {
        $state_list = Address::distinct()->orderBy('state', 'asc')->pluck('state')->toArray();
        
        return response([
            'success'   => true,
            'state_list'=> $state_list
        ],200);
    }

    public function city($state)
    {
        $city_list  = Address::where('state', $state)->orderBy('city', 'asc')->get()->pluck('city')->unique()->values()->toArray();
        return response([
            'success'   => true,
            'city_list' => $city_list
        ],200);
    }
}
