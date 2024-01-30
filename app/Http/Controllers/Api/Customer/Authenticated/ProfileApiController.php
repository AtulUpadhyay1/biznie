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
            'email'     => 'required|unique:users,email,'.auth()->id(),
            'type'      => 'nullable|array'
        ]);
        $user           = auth()->user();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->save();

        $user_detail    = UserDetail::where('user_id', $user->id)->first();
        if(!$user_detail){
            $user_detail = new UserDetail;
        }
        $user_detail->user_id       = $user->id;
        $user_detail->company_name  = $request->company_name;
        $user_detail->type          = $request->type??[];
        $user_detail->gst_number    = $request->gst_number;
        $user_detail->pan_number    = $request->pan_number;
        $user_detail->save();

        return response([
            'success'   => true,
            'message'   => 'Profile updated successfully.',
            'data'      => new ProfileResource($user)
        ],200);

        return $request->all();
    }
}
