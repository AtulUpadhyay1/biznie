<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
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
        $type = CreditWalletDocumentType::active()->select('id', 'name', 'title', 'description', 'forms')->latest()->get();
        return response()->json([
            'success'   => true,
            'data'      => $type
        ], 200);
    }

    public function creditWallet()
    {
        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;
        $transaction = CreditWalletTransaction::where('user_id', $user_id)
            ->select([
                'transaction_id',
                'amount',
                'description',
                'notes',
                'status',
                'transaction_status',
                'created_at'
            ])
            ->latest()
            ->simplePaginate(getPaginate());
        foreach ($transaction as $item) {
            $item->created_date = Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
        }
        return response([
            'success'               => true,
            'credit_balance'        => auth()->user()->credit_balance,
            'assign_credit_balance' => auth()->user()->assign_credit_balance,
            'transaction'           => $transaction
        ],200);
    }

    public function creditWalletRequestList()
    {
        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;
        $list = CreditWalletRequest::where('user_id', $user_id)->get();
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
            'forms'                     => 'required|array',
        ]);

        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;

        $check = CreditWalletRequest::where('user_id', $user_id)
            ->where('status', 'Pending')
            ->first();
        if($check){
            return response([
                'success'   => false,
                'message'   => 'A request is already pending approval. Please wait for it to be processed before submitting a new one.',
            ],400);
        }

        if(CreditWalletRequest::where('user_id', $user_id)->where('status', 'Approved')->exists()){
            return response([
               'success'   => false,
               'message'   => 'A request has already been submitted and approved.',
            ],400);
        }

        $credit_wallet_request = new CreditWalletRequest;
        $credit_wallet_request->user_id = $user_id;
        // $credit_wallet_request->document = $request->documents;
        $credit_wallet_request->reference_number = $request->reference_number;
        $credit_wallet_request->status = 'Pending';
        $credit_wallet_request->notes = $request->notes;
        $credit_wallet_request->description = $request->description;
        $credit_wallet_request->credit_wallet_document_type_id = $request->credit_wallet_document_type_id;
        $credit_wallet_request->document_type = CreditWalletDocumentType::find($request->credit_wallet_document_type_id)->title;
        $credit_wallet_request->form_data = $request->forms;
        $credit_wallet_request->save();

        return response([
            'success'   => true,
            'message'   => 'Credit wallet request submitted successfully.',
        ],200);
    }
}
