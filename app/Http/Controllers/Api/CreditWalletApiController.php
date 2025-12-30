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
        $list = CreditWalletRequest::where('user_id', $user_id)->with('getDocumentType')->get();
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

    public function creditWalletRequestDetail($id)
    {
        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;
        $request = CreditWalletRequest::where('user_id', $user_id)
            ->where('id', $id)
            ->with('getDocumentType:id,name,forms')
            ->select('id', 'credit_wallet_document_type_id', 'form_data', 'notes', 'description', 'status')
            ->first();

        if(!$request){
            return response([
               'success'   => false,
               'message'   => 'Credit wallet request not found.',
            ],404);
        }

        if($request->form_data){
            $form_data = [];
            foreach ($request->form_data as $key => $form_value) {
                $form_value['file_url'] = null;
                if(is_array($form_value) && isset($form_value['type']) && $form_value['type'] == 'file' && isset($form_value['value'])){
                    $form_value['file_url'] = imageUrl($form_value['value']);
                }
                $form_data[$key] = $form_value;
            }
            $request->form_data = $form_data;
        }

        return response([
           'success'   => true,
           'data'      => $request,
        ],200);
    }

    public function creditWalletRequestUpdate(Request $request, $id)
    {
        $request->validate([
            'forms'  => 'required',
        ]);

        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;

        $credit_wallet_request = CreditWalletRequest::where('user_id', $user_id)
            ->where('id', $id)
            ->first();

        if(!$credit_wallet_request){
            return response([
               'success'   => false,
               'message'   => 'Credit wallet request not found.',
            ],404);
        }

        if($credit_wallet_request->status != 'Pending' && $credit_wallet_request->status != 'Rejected'){
            return response([
               'success'   => false,
               'message'   => 'Only pending or rejected requests can be updated.',
            ],400);
        }

        $credit_wallet_request->form_data = $request->forms;
        $credit_wallet_request->status = 'Pending';
        $credit_wallet_request->save();

        return response([
            'success'   => true,
            'message'   => 'Credit wallet request updated successfully.',
        ],200);
    }
}
