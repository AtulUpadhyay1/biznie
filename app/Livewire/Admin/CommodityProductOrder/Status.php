<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CreditWalletTransaction;

class Status extends Component
{
    public $page_title = 'Order Status';
    public $hidden_id, $data, $status, $cancel_reason, $cancel_reason_text;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->status = $this->data->status;
    }

    public function render()
    {
        $this->page_title = 'Order Status For - '. $this->data->getProductEnquiry->unique_id;
        return view('admin.commodity_product_order.status');
    }

    public function updateStatus()
    {
        try {
            $this->data->status = $this->status;
            $this->data->cancel_reason = $this->cancel_reason != 'Other' ? $this->cancel_reason : $this->cancel_reason_text;
            $history = $this->data->history;
            $history[] = ['status' => 'Order ' .ucwords($this->status). ' By Admin', 'created_at' => Carbon::now()];
            $this->data->history = $history;
            $this->data->save();

            if($this->status == 'cancel') {

                $user = User::find($this->data->customer_user_id);

                $cash_wallet_amount = CashWalletTransaction::where('commodity_product_order_id', $this->data->id)->where('status', 'debit')->sum('amount');

                if($cash_wallet_amount > 0) {
                    $user->cash_balance = $user->cash_balance + $cash_wallet_amount;
                    $user->save();

                    $cash_history = new CashWalletTransaction;
                    $cash_history->user_id           = $user->id;
                    $cash_history->commodity_product_order_id = $this->data->id;
                    $cash_history->amount            = $cash_wallet_amount;
                    $cash_history->description       = 'Amount refunded from Order Id: '.$this->data->order_id;
                    $cash_history->mode              = 'online';
                    $cash_history->status            = 'credit';
                    $cash_history->transaction_status= 'Amount Refunded';
                    $cash_history->save();

                    $cash_history->transaction_id    = 'TX-'.date('Ymd').$cash_history->id.$user->id.rand(111, 999);
                    $cash_history->save();
                }

                $credit_wallet_amount = CreditWalletTransaction::where('commodity_product_order_id', $this->data->id)->where('status', 'debit')->sum('amount');

                if($credit_wallet_amount > 0) {
                    $user->credit_balance = $user->credit_balance + $credit_wallet_amount;
                    $user->save();

                    $credit_history = new CreditWalletTransaction;
                    $credit_history->user_id           = $user->id;
                    $credit_history->commodity_product_order_id = $this->data->id;
                    $credit_history->amount            = $credit_wallet_amount;
                    $credit_history->description       = 'Amount refunded from Order Id: '.$this->data->order_id;
                    $credit_history->status            = 'credit';
                    $credit_history->transaction_status= 'Amount Refunded';
                    $credit_history->save();

                    $credit_history->transaction_id    = 'TX-'.date('Ymd').$credit_history->id.$user->id.rand(111, 999);
                    $credit_history->save();

                }

            }

            $this->dispatch('alert',
                type : 'success',
                message : 'Status updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong. Please try again later.',
            );
        }
    }
}
