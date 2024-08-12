<?php

namespace App\Http\Controllers\Api;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAddressApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = UserAddress::where('user_id', auth()->id())->select(['id', 'pincode', 'address_line_one', 'address_line_two', 'city', 'state', 'country'])->get();
        return response()->json([
            'success'   => true,
            'list'      => $list
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pincode'           => 'required',
            'address_line_one'  => 'required',
            'address_line_two'  => 'required',
            'city'              => 'required',
            'state'             => 'required',
        ]);

        $data = new UserAddress;
        $data->user_id          = auth()->id();
        $data->pincode          = $request->pincode;
        $data->address_line_one = $request->address_line_one;
        $data->address_line_two = $request->address_line_two;
        $data->city             = $request->city;
        $data->state            = $request->state;
        $data->country          = 'India';
        $data->save();
        return response()->json([
           'success'    => true,
           'message'    => 'Address saved successfully.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pincode'           => 'required',
            'address_line_one'  => 'required',
            'address_line_two'  => 'required',
            'city'              => 'required',
            'state'             => 'required',
        ]);

        $data = UserAddress::where('user_id', auth()->id())->find($id);
        if(!$data){
            return response()->json([
               'success'    => false,
               'message'    => 'Invalid id provided.'
            ], 400);
        }

        $data->pincode          = $request->pincode;
        $data->address_line_one = $request->address_line_one;
        $data->address_line_two = $request->address_line_two;
        $data->city             = $request->city;
        $data->state            = $request->state;
        $data->country          = 'India';
        $data->save();
        return response()->json([
           'success'    => true,
           'message'    => 'Address updated successfully.'
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = UserAddress::where('user_id', auth()->id())->find($id);
        if(!$data){
            return response()->json([
               'success'    => false,
               'message'    => 'Invalid id provided.'
            ], 400);
        }

        $data->delete();

        return response()->json([
           'success'    => true,
           'message'    => 'Address deleted successfully.'
        ], 200);
    }
}
