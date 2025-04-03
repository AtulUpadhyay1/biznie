<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashWalletTransaction;
use App\Models\CreditWalletTransaction;

class Payments extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $data, $mode, $credit_availability = 0, $credit_days = 0;
    public $amount = 0, $payment_method = 'cash', $description, $notes;

    protected $queryString = [
        'mode'    => ['except' => ''],
    ];

    public function mount($id)
    {
        $this->data = User::findOrFail($id);
        $this->credit_availability = $this->data->credit_availability;
        $this->credit_days = $this->data->credit_days;
        if(!$this->mode){
            $this->mode = 'cashwallet';
            return $this->redirectRoute('admin.customer-payment-list', ['id' => $id, 'mode' => 'cashwallet'], navigate: true);
        }
    }

    public function render()
    {
        $cash_transactions = CashWalletTransaction::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        $credit_transactions = CreditWalletTransaction::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        return view('admin.customer_list.payment', compact('cash_transactions', 'credit_transactions'), ['page_title' => 'Customer Payment List']);
    }

    public function updateCreditAvailability()
    {
        $this->validate([
            'credit_days' => 'required|numeric|min:1',
        ]);
        $user = $this->data;
        $user->credit_availability = $this->credit_availability;
        $user->credit_days = $this->credit_days;
        $user->save();
        $this->dispatch('alert',
            type : 'success',
            message : 'Credit availability updated successfully.',
        );
    }

    public function resetForm()
    {
        $this->amount = 0;
        $this->payment_method = 'cash';
        $this->description = null;
        $this->note = null;
    }

    public function addCashWalletBalanace()
    {
        $this->validate([
            'amount' =>'required|numeric|min:1',
        ]);
        $user = $this->data;
        $user->cash_balance += $this->amount;
        $user->save();

        $transaction = new CashWalletTransaction;
        $transaction->user_id = $this->data->id;
        $transaction->amount = $this->amount;
        $transaction->mode = $this->payment_method;
        $transaction->description = $this->description;
        $transaction->notes = $this->notes;
        $transaction->status = 'credit';
        $transaction->transaction_status = 'Fund added by admin';
        $transaction->save();

        $transaction->transaction_id = 'TX-'.date('Ymd').$transaction->id.$transaction->user_id.rand(111, 999);
        $transaction->save();

        session()->flash('success', 'Cash wallet balance added successfully.');
        return $this->redirectRoute('admin.customer-payment-list', ['id' => $this->data->id, 'mode' => $this->mode], navigate: true);
    }

    public function addCreditWalletBalanace()
    {
        $this->validate([
            'amount' =>'required|numeric|min:1',
        ]);
        $user = $this->data;
        if($user->assign_credit_balance > $this->amount){
            $this->dispatch('alert',
                type : 'error',
                message : 'You can not add more than assigned credit balance.',
            );
            return ;
        }

        $used_credit_balance = $user->assign_credit_balance - $user->credit_balance;
        if($used_credit_balance < $this->amount){
            $this->dispatch('alert',
                type : 'error',
                message : 'You can not add more than available credit balance.',
            );
            return ;
        }
        $user->credit_balance += $this->amount;
        $user->save();

        $history = new CreditWalletTransaction;
        $history->user_id           = $this->data->id;
        $history->amount            = $this->amount;
        $history->description       = $this->description;
        $history->notes             = $this->notes;
        $history->status            = 'credit';
        $history->transaction_status= 'Credit added by admin';
        $history->save();

        $history->transaction_id    = 'TX-'.date('Ymd').$history->id.$history->user_id.rand(111, 999);
        $history->save();

        session()->flash('success', 'Credit wallet balance added successfully.');
        return $this->redirectRoute('admin.customer-payment-list', ['id' => $this->data->id, 'mode' => 'creditwallet'], navigate: true);
    }
}
