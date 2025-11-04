<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\ProfileResource;

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
            'name'      => 'required',
            'company_name' => 'required',
            'email'     => 'required|unique:users,email,'.auth()->id(),
            'type'      => 'nullable|array'
        ]);
        $user           = auth()->user();
        $user->name     = $request->name ?? $user->name;
        $user->email    = $request->email ?? $user->email;
        $user->save();

        $user_detail    = UserDetail::where('user_id', auth()->id())->first();
        if($user_detail){
            $request->validate([
                'gst_number'    => 'nullable|unique:user_details,gst_number,'.$user_detail->id,
                'pan_number'    => 'nullable|unique:user_details,pan_number,'.$user_detail->id,
            ]);
        }else{
            $request->validate([
                'gst_number'    => 'nullable|unique:user_details,gst_number',
                'pan_number'    => 'nullable|unique:user_details,pan_number',
            ]);
        }
        if(!$user_detail){
            $user_detail = new UserDetail;
        }
        $user_detail->user_id       = $user->id;
        if($request->profile_photo){
            $user_detail->profile_photo = $request->profile_photo;
        }
        $user_detail->company_name  = $request->company_name;
        if($request->company_logo){
            $user_detail->company_logo = $request->company_logo;
        }
        $user_detail->company_address       = $request->company_address ?? $user_detail->company_address;
        $user_detail->address_line_one = $request->address_line_one ?? $user_detail->address_line_one;
        $user_detail->address_line_two = $request->address_line_two ?? $user_detail->address_line_two;
        $user_detail->postal_code = $request->pin_code ?? $user_detail->postal_code;
        $user_detail->city = $request->city ?? $user_detail->city;
        $user_detail->state = $request->state ?? $user_detail->state;
        $user_detail->country = $request->country ?? $user_detail->country;
        $user_detail->type          = $request->type ? $request->type : ($user_detail->type ? $user_detail->type : []);
        $user_detail->gst_number    = $request->gst_number ?? $user_detail->gst_number;
        $user_detail->pan_number    = $request->pan_number ?? $user_detail->pan_number;
        $user_detail->save();

        return response([
            'success'   => true,
            'message'   => 'Profile updated successfully.',
            'data'      => new ProfileResource($user)
        ],200);
    }
}
