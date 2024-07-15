<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CreditWalletRequest;
use App\Http\Controllers\Controller;
use App\Models\CreditWalletTransaction;
use App\Models\CreditWalletDocumentType;
use App\Http\Resources\CreditWalletRequestResource;

class CreditWalletApiController extends Controller
{
    public function creditWalletDocument()
    {
        $type = CreditWalletDocumentType::active()->select('id', 'name', 'title', 'description')->latest()->get();
        return response()->json([
            'success'   => true,
            'data'      => $type
        ], 200);
    }

    public function creditWallet()
    {
        $transaction = CreditWalletTransaction::where('user_id', auth()->id())->select(['transaction_id', 'amount', 'description', 'notes', 'status', 'transaction_status', 'created_at'])->simplePaginate(getPaginate());
        return response([
            'success'               => true,
            'credit_balance'        => auth()->user()->credit_balance,
            'assign_credit_balance' => auth()->user()->assign_credit_balance,
            'transaction'           => $transaction
        ],200);
    }

    public function creditWalletRequestList()
    {
        $list = CreditWalletRequest::where('user_id', auth()->id())->get();
        return response([
           'success'    => true,
            'data'      => CreditWalletRequestResource::collection($list)
        ],200);
    }

    public function creditWalletRequest(Request $request)
    {
        $request->validate([
            // 'reference_number'      => 'required',
            'credit_wallet_document_type_id'    => 'required',
            // 'documents'             => 'required|array',
        ]);

        $check = CreditWalletRequest::where('user_id', auth()->id())->where('status', 'Pending')->first();
        if($check){
            return response([
                'success'   => false,
                'message'   => 'Not eligible for request. Please try again later.',
            ],400);
        }

        $credit_wallet_request = new CreditWalletRequest;
        $credit_wallet_request->user_id = auth()->id();
        // $credit_wallet_request->document = $request->documents;
        $credit_wallet_request->reference_number = $request->reference_number;
        $credit_wallet_request->status = 'Pending';
        $credit_wallet_request->notes = $request->notes;
        $credit_wallet_request->description = $request->description;
        $credit_wallet_request->credit_wallet_document_type_id = $request->credit_wallet_document_type_id;
        $credit_wallet_request->document_type = CreditWalletDocumentType::find($request->credit_wallet_document_type_id)->title;
        $credit_wallet_request->save();

        return response([
            'success'   => true,
            'message'   => 'Credit wallet request submitted successfully.',
        ],200);
    }
}
