<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CreditWalletTransaction;
use App\Models\TransporterProductEnquiry;
use App\Models\CommodityProductOrderLedger;

class TransporterReceivePayment extends Component
{
    use WithFileUploads;
    public $page_title = 'Receive payment for Transporter';
    public $hidden_id, $data, $transporter_data, $mode = 'manual', $transaction_amount, $transaction_account_name, $transaction_account_number, $transaction_bank_name, $transaction_number, $payment_method, $date_time, $description, $file;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->transporter_data = TransporterProductEnquiry::where('product_enquiries_id', $this->data->product_enquiries_id)
            ->where('is_mark', 1)
            ->with('getUser')
            ->first();

        $this->page_title = 'View Order '. $this->data->getProductEnquiry->unique_id;
    }

    public function render()
    {
        return view('admin.commodity_product_order.transporter_receive_payment');
    }

    public function save()
    {
        $order = $this->data;
        if(!$order){
            $this->dispatch('alert',
                type: 'error',
                message: 'Order not found',
            );
            return;
        }

        if(!$order->transporter_user_id){
            $this->dispatch('alert',
                type: 'error',
                message: 'No transporter assigned to this order.',
            );
            return;
        }

        $this->validate([
            'transaction_amount' => 'required|numeric|min:0',
        ]);

        // Calculate transporter due amount from transport_price
        $transporter_total = $order->transport_price ?? 0;

        // Get already paid amount to transporter
        $transporter_paid = CommodityProductOrderLedger::where('order_id', $this->hidden_id)
            ->where('ledger_type', 'transporter')
            ->where('type', 'credit')
            ->sum('amount');

        $transporter_due = $transporter_total - $transporter_paid;

        if($transporter_due <= 0){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transporter payment already completed.',
            );
            return;
        }

        if($this->transaction_amount <= 0){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transaction amount must be greater than 0.',
            );
            return;
        }

        if($this->transaction_amount > $transporter_due){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transaction amount must be less than or equal to due amount.',
            );
            return;
        }

        $user = $this->data->getCustomer;
        if ($this->mode == 'cash_wallet'){
            if ($user->cash_balance < $this->transaction_amount){
                $this->dispatch('alert',
                    type: 'error',
                    message: 'Insufficient cash wallet balance.',
                );
                return;
            }
        }
        if ($this->mode == 'credit_wallet'){
            if ($user->credit_balance < $this->transaction_amount){
                $this->dispatch('alert',
                    type: 'error',
                    message: 'Insufficient credit wallet balance.',
                );
                return;
            }
        }

        $ledger = new CommodityProductOrderLedger;
        $ledger->order_id = $this->hidden_id;
        $ledger->transaction_id = "TNX-".time()."-".rand(1111, 9999);
        $ledger->type = 'credit';
        $ledger->ledger_type = 'transporter';
        $ledger->amount = $this->transaction_amount;
        $ledger->remaining_balance = $transporter_due - $this->transaction_amount;
        $ledger->transaction_account_name = $this->transaction_account_name;
        $ledger->transaction_account_number = $this->transaction_account_number;
        $ledger->transaction_bank_name = $this->transaction_bank_name;
        $ledger->transaction_number = $this->transaction_number;
        $ledger->payment_method = $this->payment_method;
        $ledger->payment_mode = ucwords(str_replace('_', ' ', $this->mode));
        $ledger->date_time = $this->date_time;
        $ledger->description = $this->description ?? 'Payment received for Transporter - Order Id: '.$this->data->order_id;
        if($this->file){
            $file_name = time().'-'.rand(10, 99).'.'.$this->file->extension();
            $ledger->file = $this->file->storeAs('payments', $file_name, 'public');
        }
        $ledger->save();

        if ($this->mode == 'cash_wallet'){

            $user->cash_balance -= $this->transaction_amount;
            $user->save();

            $cash_history = new CashWalletTransaction;
            $cash_history->user_id           = $this->data->customer_user_id;
            $cash_history->commodity_product_order_id   = $this->hidden_id;
            $cash_history->amount            = $this->transaction_amount;
            $cash_history->description       = 'Amount debited for Transporter Payment - Order Id: '.$this->data->order_id;
            $cash_history->mode              = 'online';
            $cash_history->status            = 'debit';
            $cash_history->transaction_status= 'Amount debited';
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
            $credit_history->description       = 'Amount debited for Transporter Payment - Order Id: '.$this->data->order_id;
            $credit_history->status            = 'debit';
            $credit_history->transaction_status= 'Amount debited';
            $credit_history->save();

            $credit_history->transaction_id    = 'TX-'.date('Ymd').$credit_history->id.$credit_history->user_id.rand(111, 999);
            $credit_history->save();

        }

        session()->flash('success', 'Transporter payment received successfully.');
        return $this->redirectRoute('admin.commodity-product-order.ledger', $this->hidden_id, navigate: true);
    }
}
