<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashWalletTransaction;
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
    public $mode = 'manual', $transaction_amount, $transaction_account_name, $transaction_account_number, $transaction_bank_name, $transaction_number, $refund_payment_method, $date_time, $refund_description, $file;

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

        $creditWalletSums = CreditWalletTransaction::where('commodity_product_order_id', $this->hidden_id)
            ->where('user_id', $this->data->customer_user_id)
            ->selectRaw("
            SUM(CASE WHEN status = 'debit' THEN amount ELSE 0 END) as total_debit,
            SUM(CASE WHEN status = 'credit' THEN amount ELSE 0 END) as total_credit
            ")
            ->first();

        $this->total_credit_wallet = $creditWalletSums->total_debit ?? 0;
        $this->total_pay_credit_wallet = $creditWalletSums->total_credit ?? 0;

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

    public function addRefundBalance()
    {
        $order = $this->data;
        if(!$order){
            $this->dispatch('alert',
                type: 'error',
                message: 'Order not found',
            );
            return;
        }

        $this->validate([
            'transaction_amount' => 'required|numeric|min:0',
        ]);

        $advance_amount = CommodityProductOrderLedger::where('order_id', $this->hidden_id)->skip(1)->first();
        if (!$advance_amount) {
            $this->dispatch('alert',
                type: 'error',
                message: 'No advance amount found for this order.',
            );
            return;
        }
        if ($this->transaction_amount > $advance_amount->amount) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Refund amount must be less than or equal to advance amount.',
            );
            return;
        }

        $user = $this->data->getCustomer;

        if ($this->mode == 'credit_wallet'){
            if ($user->assign_credit_balance == 0){
                $this->dispatch('alert',
                    type: 'error',
                    message: 'Buyer does not have an active credit wallet balance.',
                );
                return;
            }
        }

        $order->paid_amount -= $this->transaction_amount;
        $order->due_amount += $this->transaction_amount;
        $order->save();

        $ledger = new CommodityProductOrderLedger;
        $ledger->order_id = $this->hidden_id;
        $ledger->transaction_id = "TNX-".time()."-".rand(1111, 9999);
        $ledger->type = 'debit';
        $ledger->amount = $this->transaction_amount;
        $ledger->remaining_balance = $this->data->due_amount;
        $ledger->transaction_account_name = $this->transaction_account_name;
        $ledger->transaction_account_number = $this->transaction_account_number;
        $ledger->transaction_bank_name = $this->transaction_bank_name;
        $ledger->transaction_number = $this->transaction_number;
        $ledger->payment_method = $this->payment_method;
        $ledger->payment_mode = ucwords(str_replace('_', ' ', $this->mode));
        $ledger->date_time = $this->date_time;
        $ledger->description = $this->description ?? 'Amount refunded for Order Id: '.$this->data->order_id;
        if($this->file){
            $file_name = time().'-'.rand(10, 99).'.'.$this->file->extension();
            $ledger->file = $this->file->storeAs('payments', $file_name, 'public');
        }
        $ledger->save();

        if ($this->mode == 'cash_wallet'){

            $user->cash_balance += $this->transaction_amount;
            $user->save();

            $cash_history = new CashWalletTransaction;
            $cash_history->user_id           = $this->data->customer_user_id;
            $cash_history->commodity_product_order_id   = $this->hidden_id;
            $cash_history->amount            = $this->transaction_account_number;
            $cash_history->description       = 'Amount refunded for Order Id: '.$this->data->order_id;
            $cash_history->mode              = 'online';
            $cash_history->status            = 'credit';
            $cash_history->transaction_status= 'Amount credited';
            $cash_history->save();

            $cash_history->transaction_id    = 'TX-'.date('Ymd').$cash_history->id.$cash_history->user_id.rand(111, 999);
            $cash_history->save();

        }else if ($this->mode == 'credit_wallet'){

            $user->credit_balance -= $this->transaction_amount;
            $user->save();

            $credit_history = new CreditWalletTransaction;
            $credit_history->user_id           = $this->data->customer_user_id;
            $credit_history->commodity_product_order_id = $this->hidden_id;
            $credit_history->amount            = $this->transaction_amount;
            $credit_history->description       = 'Amount refunded for Order Id: '.$this->data->order_id;
            $credit_history->status            = 'credit';
            $credit_history->transaction_status= 'Amount credited';
            $credit_history->save();

            $credit_history->transaction_id    = 'TX-'.date('Ymd').$credit_history->id.$credit_history->user_id.rand(111, 999);
            $credit_history->save();

        }

        session()->flash('success', 'Payment refund successfully.');
        return $this->redirectRoute('admin.commodity-product-order.ledger', $this->hidden_id, navigate: true);
    }
}
