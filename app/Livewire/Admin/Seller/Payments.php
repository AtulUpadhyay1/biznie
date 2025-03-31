<?php

namespace App\Livewire\Admin\Seller;

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

    protected $queryString = [
        'mode'    => ['except' => ''],
    ];

    public function mount($id)
    {
        $this->data = User::where('type', 'seller')->with('getSellerKycDetail', 'getBusiness')->findOrFail($id);
        $this->credit_availability = $this->data->credit_availability;
        $this->credit_days = $this->data->credit_days;
        if(!$this->mode){
            $this->mode = 'cashwallet';
            return $this->redirectRoute('admin.seller.payments', ['id' => $id, 'mode' => 'cashwallet'], navigate: true);
        }
    }

    public function render()
    {
        $cash_transactions = CashWalletTransaction::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        $credit_transactions = CreditWalletTransaction::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        return view('admin.seller.payments', compact('cash_transactions', 'credit_transactions'), ['page_title' => 'Payments List']);
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
}
