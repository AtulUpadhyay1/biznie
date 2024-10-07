<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transporter\ProfileResource;

class ProfileApiController extends Controller
{
    public function profile()
    {
        return response([
            'success'   => true,
            'data'      => new ProfileResource(auth()->user())
        ],200);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'phone'         => 'required|numeric|digits:10',
            'gst_number'    => 'required',
            'address'       => 'required',
            'company_name'  => 'required',
        ]);

        $user = auth()->user();
        $user->name     = $request->name;
        $user->type     = 'transporter';
        $user->phone    = $request->phone;
        $user->save();

        $transporter = auth()->user()->getTransporterDetail;
        $transporter->company_name  = $request->company_name;
        $transporter->gst_number    = $request->gst_number;
        $transporter->address       = $request->address;
        $transporter->alternate_phone = $request->alternate_phone;
        $transporter->aadhar_number = $request->aadhar_number;
        $transporter->address       = $request->address;
        $transporter->save();

        return response([
            'success'   => true,
            'message'   => 'Profile updated successfully.',
            'data'      => new ProfileResource($user)
        ],200);
    }
}
