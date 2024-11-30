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
        $request->validate([
            'name'          => 'required',
            'phone'         => 'required|numeric|digits:10',
            'gst_number'    => 'required',
            'address'       => 'required',
            'company_name'  => 'required',
        ]);

        $user = auth()->user();

        if($user->type == 'transporter'){
            return response([
                'success'   => false,
                'message'   => 'Yor are already a transporter.'
            ],400);
        }

        $user->name     = $request->name;
        $user->type     = 'transporter';
        $user->phone    = $request->phone;
        $user->save();

        $user_log_history = new UserPromotionHistory;
        $user_log_history->user_id = $user->id;
        $user_log_history->old_type = "customer";
        $user_log_history->new_type = "transporter";
        $user_log_history->save();

        $transporter = new TransporterDetail;
        $transporter->user_id       = $user->id;
        $transporter->company_name  = $request->company_name;
        $transporter->gst_number    = $request->gst_number;
        $transporter->address       = $request->address;
        $transporter->alternate_phone = $request->alternate_phone;
        $transporter->aadhar_number = $request->aadhar_number;
        $transporter->save();

        return response([
            'success'   => true,
            'message'   => 'Congratulations, Now you are a transporter.'
        ],200);
    }
}
