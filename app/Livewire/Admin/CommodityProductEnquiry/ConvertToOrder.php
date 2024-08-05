<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\ProductEnquiry;
use Illuminate\Support\Facades\Auth;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CreditWalletTransaction;
use App\Models\CommodityProductOrderLedger;

class ConvertToOrder extends Component
{
    public $page_title = 'Conver To Order';
    public $hidden_id, $token_amount, $total_amount, $message;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $enquiry_data = ProductEnquiry::with('getBrand', 'getUser', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry')->findOrFail($this->hidden_id);

        return view('admin.commodity_product_enquiry.convert_to_order', compact('enquiry_data'));
    }

    public function setAmount($token_amount, $total_amount)
    {
        $this->token_amount = $token_amount;
        $this->total_amount = $total_amount;
        // dd([$this->token_amount, $this->total_amount]);
    }

    public function enquiryToOrder()
    {
        $enquiry_data = ProductEnquiry::with('getMarkedSellerProductEnquiry')->find($this->hidden_id);
        $mark_seller = $enquiry_data->getMarkedSellerProductEnquiry;

        $customer = User::find($mark_seller->customer_user_id);
        $user_total_balance = $customer->cash_balance + $customer->credit_balance;

        if($this->token_amount > $user_total_balance){
            $this->dispatch('alert',
                type : 'error',
                message : 'Your balance is not sufficient to convert this enquiry to order.',
            );
            return false;
        }

        $enquiry_data->status = 'ordered';
        $history = $enquiry_data->history;
        $history[] = ['status' => 'Ordered', 'created_at' => Carbon::now()];
        $enquiry_data->history = $history;
        $enquiry_data->save();


        $mark_seller->status = 'ordered';
        $history = $mark_seller->history;
        $history[] = ['status' => 'Ordered', 'created_at' => Carbon::now()];
        $mark_seller->history = $history;
        $mark_seller->save();

        $order                              = new CommodityProductOrder;
        $order->seller_user_id              = $mark_seller->user_id;
        $order->customer_user_id            = $mark_seller->customer_user_id;
        $order->product_enquiries_id        = $mark_seller->product_enquiries_id;
        $order->seller_product_enquiries_id = $mark_seller->id;
        $order->commodity_product_id        = $mark_seller->commodity_product_id;
        $order->brand_id                    = $mark_seller->brand_id;
        $order->unique_id                   = $mark_seller->unique_id;
        $order->order_id                    = 'OID-'.date('Ymd').'-'.rand(1111, 9999);
        $order->origin_city                 = $mark_seller->origin_city;
        $order->value                       = $mark_seller->value;
        $order->billing_address             = $mark_seller->billing_address;
        $order->delivery_address            = $mark_seller->delivery_address;
        $order->purpose                     = $mark_seller->purpose;
        $order->description                 = $mark_seller->description;
        $order->message                     = $mark_seller->message;
        $order->price                       = $mark_seller->price;
        $order->base_price                  = $mark_seller->base_price;
        $order->token_amount                = $this->token_amount;
        $order->transport_price             = $mark_seller->transport_price;
        $order->commission                  = $mark_seller->commission;
        $order->total_amount                = $this->total_amount;
        $order->paid_amount                 = $this->token_amount;
        $order->due_amount                  = $this->total_amount - $this->token_amount;
        $order->loading_address             = $mark_seller->loading_address;
        $order->delivery_by                 = $mark_seller->delivery_by;
        $order->status                      = 'pending';
        $order->history                     = [['status' => 'Order Confirmed By Customer', 'created_at' => Carbon::now()]];
        $order->save();

        $debit_ledger                       = new CommodityProductOrderLedger;
        $debit_ledger->order_id             = $order->id;
        $debit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $debit_ledger->type                 = 'debit';
        $debit_ledger->amount               = $this->total_amount;
        $debit_ledger->remaining_balance    = $this->total_amount;
        $debit_ledger->description          = 'Amount debited for Order Id: '.$order->order_id;
        $debit_ledger->save();

        $credit_ledger                       = new CommodityProductOrderLedger;
        $credit_ledger->order_id             = $order->id;
        $credit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $credit_ledger->type                 = 'credit';
        $credit_ledger->amount               = $this->token_amount;
        $credit_ledger->remaining_balance    = $debit_ledger->remaining_balance - $this->token_amount;
        $credit_ledger->description          = 'Amount credited for Order Id: '.$order->order_id;
        $credit_ledger->save();

        if($customer->cash_balance > $this->token_amount){

            // Cash balance
            $customer->cash_balance = $customer->cash_balance - $this->token_amount;
            $customer->save();

            $cash_history = new CashWalletTransaction;
            $cash_history->user_id           = $customer->id;
            $cash_history->amount            = $this->token_amount;
            $cash_history->description       = 'Amount debited for Order Id: '.$order->order_id;
            $cash_history->mode              = 'online';
            $cash_history->status            = 'debit';
            $cash_history->transaction_status= 'Amount debited';
            $cash_history->save();

            $cash_history->transaction_id    = 'TX-'.date('Ymd').$cash_history->id.$customer->id.rand(111, 999);
            $cash_history->save();

        }else{

            $remaining_amount = $this->token_amount - $customer->cash_balance;

            // Cash balance
            $cash_history = new CashWalletTransaction;
            $cash_history->user_id           = $customer->id;
            $cash_history->amount            = $customer->cash_balance;
            $cash_history->description       = 'Amount debited for Order Id: '.$order->order_id;
            $cash_history->mode              = 'online';
            $cash_history->status            = 'debit';
            $cash_history->transaction_status= 'Amount debited';
            $cash_history->save();

            $cash_history->transaction_id    = 'TX-'.date('Ymd').$cash_history->id.$customer->id.rand(111, 999);
            $cash_history->save();

            $customer->cash_balance = 0;
            $customer->save();

            // Credit Balance
            $remaining_amount = $customer->credit_balance - $remaining_amount;
            $customer->credit_balance = $remaining_amount;
            $customer->save();

            $credit_history = new CreditWalletTransaction;
            $credit_history->user_id           = $customer->id;
            $credit_history->amount            = $remaining_amount;
            $credit_history->description       = 'Amount debited for Order Id: '.$order->order_id;
            $credit_history->status            = 'debit';
            $credit_history->transaction_status= 'Amount debited';
            $credit_history->save();

            $credit_history->transaction_id    = 'TX-'.date('Ymd').$credit_history->id.$customer->id.rand(111, 999);
            $credit_history->save();
        }

        session()->flash('success', 'Product ordered successfully.');
        return $this->redirectRoute('admin.commodity-product-enquiry.index', navigate: true);
    }
}
