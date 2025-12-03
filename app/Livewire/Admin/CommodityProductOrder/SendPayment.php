<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductSellerOrderLedger;

class SendPayment extends Component
{
    use WithFileUploads;
    public $page_title = 'Receive payment';
    public $hidden_id, $data, $transaction_amount, $transaction_account_name, $transaction_account_number, $transaction_bank_name, $transaction_number, $payment_method, $date_time, $description, $file, $status = 'pending';

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order '. $this->data->getProductEnquiry->unique_id;
    }

    public function render()
    {
        $paid_amount = CommodityProductSellerOrderLedger::where('order_id', $this->hidden_id)
            ->where('type', 'debit')
            ->sum('amount');

        $total_amount = $this->data->seller_invoice_amount ?? $this->data->total_amount;
        $remaining_balance = $total_amount - $paid_amount;
        return view('admin.commodity_product_order.send_payment', compact('paid_amount', 'remaining_balance', 'total_amount'));
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

        $this->validate([
            'transaction_amount' => 'required|numeric|min:0',
        ]);

        $ledgers = CommodityProductSellerOrderLedger::where('order_id', $this->hidden_id)->orderBy('id', 'DESC')->first();

        $paid_amount = CommodityProductSellerOrderLedger::where('order_id', $this->hidden_id)
            ->where('type', 'debit')
            ->sum('amount');

        $total_amount = $this->data->seller_invoice_amount ?? $this->data->total_amount;
        $remaining_balance = $total_amount - $paid_amount;

        if($ledgers && $remaining_balance <= 0){
            $this->dispatch('alert',
                type: 'error',
                message: 'Order already paid.',
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

        if($remaining_balance < $this->transaction_amount){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transaction amount must be less than or equal to remaining balance.',
            );
            return;
        }

        $ledger                       = new CommodityProductSellerOrderLedger;
        $ledger->order_id             = $order->id;
        $ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $ledger->type                 = 'debit';
        $ledger->amount               = $this->transaction_amount;
        $ledger->remaining_balance    = $remaining_balance;
        $ledger->transaction_account_name = $this->transaction_account_name;
        $ledger->transaction_account_number = $this->transaction_account_number;
        $ledger->transaction_bank_name = $this->transaction_bank_name;
        $ledger->transaction_number = $this->transaction_number;
        $ledger->payment_method = $this->payment_method;
        $ledger->payment_mode = 'manual';
        $ledger->date_time = $this->date_time;
        $ledger->description = $this->description ?? 'Amount credited for Order Id: '.$this->data->order_id;
        if($this->file){
            $file_name = time().'-'.rand(10, 99).'.'.$this->file->extension();
            $ledger->file = $this->file->storeAs('payments', $file_name, 'public');
        }
        $ledger->save();

        session()->flash('success', 'Payment successfully processed.');
        return $this->redirectRoute('admin.commodity-product-order.sellerLedger', $this->hidden_id, navigate: true);
    }
}
