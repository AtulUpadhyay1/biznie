<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserOtp;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductState;
use App\Models\CreditWalletTransaction;

class Status extends Component
{
    public $page_title = 'Order Status';
    public $hidden_id, $data, $enquiry_data, $seller_enquiry_data, $loading_address, $status, $cancel_reason, $cancel_reason_text, $otp;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getProductEnquiry', 'getSellerProductEnquiry')->findOrFail($this->hidden_id);
        $this->enquiry_data = ProductEnquiry::with('getBrand', 'getUser', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry', 'getMarkedSellerProductEnquiry.getUser')->findOrFail($this->data->product_enquiries_id);
        $this->seller_enquiry_data = $this->enquiry_data->getMarkedSellerProductEnquiry;
        $this->loading_address = CommodityProductState::where('commodity_product_id', $this->data->commodity_product_id)
            ->where('brand_id', $this->data->brand_id)
            ->first();
        $this->status = $this->data->status;
    }

    public function render()
    {
        $this->page_title = 'Order Status For - '. $this->data->getProductEnquiry->unique_id;
        return view('admin.commodity_product_order.status');
    }

    public function sendOtp()
    {
        $phone = $this->data->getCustomer?->phone;
        sendOtp($phone);
        $this->dispatch('alert',
            type : 'success',
            message : 'OTP sent successfully. Please enter the OTP to proceed.',
        );
    }

    public function verifyOtp()
    {
        $phone = $this->data->getCustomer?->phone;

        $checkOtp = UserOtp::where('phone', $phone)->where('otp', $this->otp)->first();
        if(!$checkOtp){
            $this->dispatch('alert',
                type : 'error',
                message : 'Please enter a valid OTP.',
            );
            return ;
        }
        $checkOtp->delete();
        $this->dispatch('alert',
            type : 'success',
            message : 'OTP verified successfully.',
        );

        $this->updateStatus();
    }

    public function updateStatus()
    {
        try {
            $this->data->status = $this->status;
            $this->data->cancel_reason = $this->cancel_reason != 'Other' ? $this->cancel_reason : $this->cancel_reason_text;
            $history = $this->data->history;
            $history[] = ['status' => 'Order ' .ucwords($this->status). ' By Admin', 'created_at' => Carbon::now()];
            $this->data->history = $history;

            if($this->status == 'dispatched'){
                $seller_product_enquiry = $this->data->getSellerProductEnquiry;
                if ($seller_product_enquiry?->seller_credit_days && $seller_product_enquiry?->customer_credit_days) {
                    if (is_null($this->data->seller_credit_due_date) && is_null($this->data->customer_credit_due_date)) {
                        $this->data->seller_credit_due_date = Carbon::now()->addDays($seller_product_enquiry->seller_credit_days)->toDateString();
                        $this->data->customer_credit_due_date = Carbon::now()->addDays($seller_product_enquiry->customer_credit_days)->toDateString();
                    }
                }
            }
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

            // $this->dispatch('alert',
            //     type : 'success',
            //     message : 'Status updated successfully.',
            // );

            session()->flash('success', 'Status updated successfully.');
            return $this->redirectRoute('admin.commodity-product-order.status', $this->hidden_id, navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong. Please try again later.',
            );
        }
    }
}
