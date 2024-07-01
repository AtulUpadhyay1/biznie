<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Illuminate\Http\Request;
use App\Models\CreditWalletRequest;
use App\Http\Controllers\Controller;

class CreditWalletApiController extends Controller
{
    public function creditWalletRequest(Request $request)
    {
        $request->validate([
            'reference_number'      => 'required',
            'amount'                => 'nullable|min:1',
            'documents'             => 'required|array',
        ]);

        $check = CreditWalletRequest::where('user_id', auth()->id())->where('status', 'Approved')->first();
        if($check){
            return response([
                'success'   => false,
                'message'   => 'Not eligible for request.',
            ],400);
        }

        $credit_wallet_request = new CreditWalletRequest;
        $credit_wallet_request->user_id = auth()->id();
        $credit_wallet_request->document = $request->documents;
        $credit_wallet_request->reference_number = $request->reference_number;
        $credit_wallet_request->status = 'Pending';
        $credit_wallet_request->notes = $request->notes;
        $credit_wallet_request->description = $request->description;
        $credit_wallet_request->save();

        return response([
            'success'   => true,
            'message'   => 'Credit wallet request submited successfully.',
        ],200);
    }
}
