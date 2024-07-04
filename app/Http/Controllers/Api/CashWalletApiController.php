<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashWalletTransaction;

class CashWalletApiController extends Controller
{
    public function cashWallet()
    {
        $transaction = CashWalletTransaction::where('user_id', auth()->id())->get(['transaction_id', 'amount', 'description', 'notes', 'mode', 'status', 'transaction_status']);
        return response([
            'success'               => true,
            'cash_balance'          => auth()->user()->cash_balance,
            'transaction'           => $transaction
        ],200);
    }
}
