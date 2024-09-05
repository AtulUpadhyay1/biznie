<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Models\TransporterDetail;
use App\Http\Controllers\Controller;
use App\Models\UserPromotionHistory;

class BecomeTransporterApiController extends Controller
{
    public function becomeTransport(Request $request)
    {
        $this->validate($request, [
            'user_name'         => 'required',
            'email'             => 'required|unique:users,email,'.auth()->id(),
            'company_name'      => 'required',
            'type'              => 'required|array|min:1',
            'type.*'            => 'required|integer|min:1',
            'pan_number'        => 'required',
            'gst_number'        => 'required',
            'address'           => 'required',
        ]);

        $user = auth()->user();

        if($user->type == 'transporter'){
            return response([
                'success'   => false,
                'message'   => 'Yor are already a transporter.'
            ],400);
        }

        $user->name = $request->user_name;
        $user->email = $request->email;
        $user->type = 'transporter';
        $user->save();

        $user_log_history = new UserPromotionHistory;
        $user_log_history->user_id = $user->id;
        $user_log_history->old_type = "customer";
        $user_log_history->new_type = "transporter";
        $user_log_history->save();

        $transporter_details = new TransporterDetail;
        $transporter_details->user_id = $user->id;
        $transporter_details->company_name = $request->company_name;
        $transporter_details->type = $request->type;
        $transporter_details->pan_number = $request->pan_number;
        $transporter_details->gst_number = $request->gst_number;
        $transporter_details->address = $request->address;
        $transporter_details->save();

        return response([
            'success'   => true,
            'message'   => 'Congratulations, Now you are a transporter.'
        ],200);
    }
}
