<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductOrder;
use App\Models\CreditWalletTransaction;
use App\Models\CommodityProductOrderLedger;

class Ledger extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'View Order Ledger';
    public $hidden_id, $total_credit_wallet = 0, $total_pay_credit_wallet, $due_credit_wallet, $data;
    public $amount = 0, $payment_method = 'cash', $description, $notes;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order Ledger '. $this->data->getProductEnquiry->unique_id;
        $ledgers = CommodityProductOrderLedger::where('order_id', $this->hidden_id)
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());

        $this->total_credit_wallet = CreditWalletTransaction::where('commodity_product_order_id', $this->hidden_id)
            ->where('user_id', $this->data->customer_user_id)
            ->where('status', 'debit')
            ->sum('amount');


        $this->total_pay_credit_wallet = CreditWalletTransaction::where('commodity_product_order_id', $this->hidden_id)
            ->where('user_id', $this->data->customer_user_id)
            ->where('status', 'credit')
            ->sum('amount');

        $this->due_credit_wallet = $this->total_credit_wallet - $this->total_pay_credit_wallet;

        return view('admin.commodity_product_order.ledger', compact('ledgers'));
    }

    public function addCreditWalletBalanace()
    {
        $this->validate([
            'amount' =>'required|numeric|min:1',
        ]);
        $user = $this->data->getCustomer;
        if($this->amount > $user->assign_credit_balance){
            $this->dispatch('alert',
                type : 'error',
                message : 'You can not add more than assigned credit balance.',
            );
            return ;
        }

        if($this->amount > $this->due_credit_wallet){
            $this->dispatch('alert',
                type : 'error',
                message : 'You can not add more than due credit wallet balance.',
            );
            return ;
        }

        $used_credit_balance = $user->assign_credit_balance - $user->credit_balance;
        if($this->amount > $used_credit_balance ){
            $this->dispatch('alert',
                type : 'error',
                message : 'You can not add more than available credit balance.',
            );
            return ;
        }
        $user->credit_balance += $this->amount;
        $user->save();

        $history = new CreditWalletTransaction;
        $history->user_id           =  $user->id;
        $history->commodity_product_order_id = $this->hidden_id;
        $history->amount            = $this->amount;
        $history->description       = 'Amount credit for Order Id: '.$this->data->order_id;
        $history->notes             = $this->notes;
        $history->status            = 'credit';
        $history->transaction_status= 'Amount credited';
        $history->save();

        $history->transaction_id    = 'TX-'.date('Ymd').$history->id.$history->user_id.rand(111, 999);
        $history->save();

        session()->flash('success', 'Credit wallet balance added successfully.');
        return $this->redirectRoute('admin.commodity-product-order.ledger', $this->hidden_id, navigate: true);
    }
}
