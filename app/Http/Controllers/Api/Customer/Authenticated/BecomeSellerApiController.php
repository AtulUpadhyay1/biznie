<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\SellerKycDetail;
use App\Http\Controllers\Controller;
use App\Models\UserPromotionHistory;

class BecomeSellerApiController extends Controller
{
    public function becomeSeller(Request $request)
    {
        $this->validate($request, [
            'user_name'         => 'required',
            'name'              => 'required',
            'about'             => 'required',
            'category'          => 'required',
            'type'              => 'required',
            'seller_type'       => 'required',
        ]);

        $user = auth()->user();
        $user->name = $request->user_name;
        $user->type = 'seller';
        $user->save();

        $user_log_history = new UserPromotionHistory;
        $user_log_history->user_id = $user->id;
        $user_log_history->old_type = "customer";
        $user_log_history->new_type = "seller";
        $user_log_history->save();

        $business = new Business;
        $business->user_id = $user->id;
        $business->name = $request->name;
        $business->about = $request->about;
        $business->category = $request->category;
        $business->type = $request->type;
        $business->seller_type = $request->seller_type;
        $business->save();

        return response([
            'success'   => true,
            'message'   => 'Congratulations, Now you are a seller. Complete your next steps.'
        ],200);

    }

    public function updatedAddress(Request $request)
    {
        $this->validate($request, [
            'address'           => 'required',
            'postal_code'       => 'required',
            'city'              => 'required',
            'state'             => 'required',
            'country'           => 'required',
        ]);

        $data = SellerKycDetail::where('user_id', auth()->id())->first();
        if(!$data){
            $data = new SellerKycDetail;
            $data->user_id = auth()->id();
        }
        $data->address = $request->address;
        $data->postal_code = $request->postal_code;
        $data->city = $request->city;
        $data->state = $request->state;
        $data->country = $request->country;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Address details update successfully.'
        ],200);

    }

    public function updatedBankDetails(Request $request)
    {
        $this->validate($request, [
            'account_number'        => 'required',
            'account_holder_name'   => 'required',
            'bank_name'             => 'required',
            'ifsc_code'             => 'required',
        ]);

        $data = SellerKycDetail::where('user_id', auth()->id())->first();
        if(!$data){
            $data = new SellerKycDetail;
            $data->user_id = auth()->id();
        }
        $data->account_number = $request->account_number;
        $data->account_holder_name = $request->account_holder_name;
        $data->bank_name = $request->bank_name;
        $data->ifsc_code = $request->ifsc_code;
        $data->bank_status = 'pending';
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Bank details update successfully.'
        ],200);
    }

    public function updatedKycDetails(Request $request)
    {
        $this->validate($request, [
            'identity_type'         => 'required',
            'identity_number'       => 'required',
            'identity_proof'        => 'required',
            'address_type'          => 'required',
            'address_proof'         => 'required',
            'business_registration_certificate' => 'required',
            'business_registration_number'      => 'required',
            'gst_type'             => 'required',
            'gst_number'           => 'required',
        ]);
        $data = SellerKycDetail::where('user_id', auth()->id())->first();
        if(!$data){
            $data = new SellerKycDetail;
            $data->user_id = auth()->id();
        }
        $data->identity_type = $request->identity_type;
        $data->identity_number = $request->identity_number;
        $data->identity_proof = $request->identity_proof;
        $data->identity_proof_bank = $request->identity_proof_bank;
        $data->address_type = $request->address_type;
        $data->address_proof = $request->address_proof;
        $data->address_proof_back = $request->address_proof_back ?? null;
        $data->business_registration_certificate = $request->business_registration_certificate;
        $data->business_registration_number = $request->business_registration_number;
        $data->trademark_registration_proof = $request->trademark_registration_proof;
        $data->gst_type = $request->gst_type;
        $data->gst_number = $request->gst_number;
        $data->status = 'uploaded';
        $data->save();
        return response([
            'success'   => true,
            'message'   => 'Kyc details update successfully.'
        ],200);
    }
}
