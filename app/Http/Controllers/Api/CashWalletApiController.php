<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashWalletTransaction;

class CashWalletApiController extends Controller
{
    public function cashWallet()
    {
        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;
        $transaction = CashWalletTransaction::where('user_id', $user_id)
            ->select([
                'transaction_id',
                'amount',
                'description',
                'notes',
                'mode',
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
            'cash_balance'          => auth()->user()->cash_balance,
            'transaction'           => $transaction
        ],200);
    }
}
