<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\ProfileResource;

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
        $this->validate($request, [
            'email'             => 'required|unique:users,email,'.auth()->id(),
            'company_name'      => 'required',
            // 'seller_type'       => 'nullable|array|min:1',
            // 'seller_type.*'     => 'nullable|integer|min:1',
            // 'pan_number'        => 'nullable|unique:seller_kyc_details,pan_number,'.auth()->id(),
            // 'gst_number'        => 'required|unique:seller_kyc_details,gst_number,'.auth()->id(),
            'gst_number'        => 'required',
            'address'           => 'nullable',
        ]);

        $user = auth()->user();
        $user->name = $request->user_name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->save();

        $business = $user->getBusiness;
        $business->user_id = $user->id;
        $business->name = $request->company_name ?? $business->name;
        // $business->about = $request->about;
        // $business->category = $request->category;
        // $business->type = $request->type;
        $business->seller_type = $request->seller_type ?? $business->seller_type;
        $business->save();

        $seller_kyc = $user->getSellerKycDetail;
        $seller_kyc->identity_type = 'pan';
        $seller_kyc->identity_number = $request->pan_number ?? $seller_kyc->identity_number;
        $seller_kyc->gst_type = $request->gst_type ?? $seller_kyc->gst_type;
        $seller_kyc->gst_number = $request->gst_number ?? $seller_kyc->gst_number;
        // $seller_kyc->address  = $request->address;
        $seller_kyc->address_line_one = $request->address_line_one ?? $seller_kyc->address_line_one;
        $seller_kyc->address_line_two = $request->address_line_two ?? $seller_kyc->address_line_two;
        $seller_kyc->postal_code = $request->pin_code ?? $seller_kyc->postal_code;
        $seller_kyc->city = $request->city ?? $seller_kyc->city;
        $seller_kyc->state = $request->state ?? $seller_kyc->state;
        $seller_kyc->country = $request->country ?? $seller_kyc->country;
        $seller_kyc->save();

        return response([
            'success'   => true,
            'message'   => 'Profile updated successfully.',
            'data'      => new ProfileResource($user)
        ],200);
    }
}
