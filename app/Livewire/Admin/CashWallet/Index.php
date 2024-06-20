<?php

namespace App\Livewire\Admin\CashWallet;

use App\Models\User;
use Livewire\Component;
use App\Models\CashWalletTransaction;

class Index extends Component
{
    public $page_title = "Cash Wallet";

    public $user_id, $amount, $description, $notes, $mode = 'cash';

    public function render()
    {
        $user_list = User::where('status', 'active')->orderBy('name', 'asc')->get();
        $user_detail = User::find($this->user_id);
        $latest_transactions = CashWalletTransaction::where('user_id', $this->user_id)->latest()->take(10)->get();
        return view('admin.cash_wallet.index', compact('user_list', 'user_detail', 'latest_transactions'));
    }

    public function resetForm()
    {
        $this->amount       = null;
        $this->description  = null;
        $this->notes        = null;
        $this->mode         = 'cash';
    }

    public function save()
    {
        $this->validate([
            'user_id'   => 'required',
            'amount'    => 'required|numeric|min:1',
        ]);

        try {

            $user = User::find($this->user_id);
            $user->cash_balance = $user->cash_balance + $this->amount;
            $user->save();

            $history = new CashWalletTransaction;
            $history->user_id           = $this->user_id;
            $history->amount            = $this->amount;
            $history->description       = $this->description;
            $history->notes             = $this->notes;
            $history->mode              = $this->mode;
            $history->status            = 'credit';
            $history->transaction_status= 'Fund added by admin';
            $history->save();

            $history->transaction_id    = 'TX-'.date('Ymd').$history->id.$this->user_id.rand(111, 999);
            $history->save();

            $this->resetForm();

            $this->dispatch('alert',
                type : 'success',
                message : 'Fund added in cash wallet.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong. Please try again later.',
            );
        }
    }
}
