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

    public $data;
    public function mount($id)
    {
        $this->data = User::find($id);
    }

    public function render()
    {
        $cash_transaction = CashWalletTransaction::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        $credit_transaction = CreditWalletTransaction::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        return view('admin.customer_list.payment', compact('cash_transaction', 'credit_transaction'), ['page_title' => 'Customer Payment List']);
    }
}
