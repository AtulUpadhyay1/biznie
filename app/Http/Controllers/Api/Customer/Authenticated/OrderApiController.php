<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CreditWalletTransaction;
use App\Models\CommodityProductOrderDriver;
use App\Models\CommodityProductOrderLedger;
use App\Http\Resources\Customer\OrderResource;
use App\Http\Resources\Customer\OrderDetailResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $list = CommodityProductOrder::where('customer_user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit', 'getProductEnquiry')->latest()->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function ledger($order_id)
    {
        $list = CommodityProductOrderLedger::where('order_id', $order_id)->latest()
            ->get(['transaction_id', 'type', 'amount', 'remaining_balance', 'description', 'payment_mode', 'created_at'])
            ->map(function ($item) {
                $item->payment_mode = $item->payment_mode ?? 'Manual';
                $item->created_at_date = Carbon::parse($item->created_at)->format('d-m-Y H-i-s');
                return $item;
            });
        return response([
            'success'   => true,
            'data'      => $list
        ],200);
    }

    public function show($id)
    {
        $data = CommodityProductOrder::where('customer_user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getProductEnquiry', 'getCommodityProduct.getUnit', 'getCommodityProduct.getCategory', 'getDrivers')->find($id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],404);
        }
        return response([
            'success'   => true,
            'data'      => new OrderDetailResource($data)
        ],200);
    }

    public function qualityCheckStatus(Request $request)
    {
        $request->validate([
            'order_id'                      => 'required',
            'quality_check_image_status'    => 'required',
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],400);
        }
        $data->quality_check_image_status   = $request->quality_check_image_status;
        $data->admin_quality_check_message  = $request->message;
        $data->quality_check_image_status_updated_by   = 'customer';
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Quality check status updated successfully.',
        ],200);
    }

    public function updateFinalQuantity(Request $request)
    {
        $request->validate([
            'driver_id'         => 'required',
            'order_id'          => 'required',
            'final_quantity'    => 'required'
        ]);
        $data = CommodityProductOrderDriver::where('id', $request->driver_id)->where('order_id', $request->order_id)->first();
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $data->final_quantity_by_customer = $request->final_quantity;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Final quantity updated successfully.',
        ],200);
    }

    public function payOrderDue(Request $request, $id)
    {
        $order = CommodityProductOrder::find($id);
        $customer = User::find($order->customer_user_id);
        $user_total_balance = $customer->cash_balance + $customer->credit_balance;
        if($order->due_amount > $user_total_balance){
            return response([
                'success'   => false,
                'message'   => 'Your balance is not sufficient to complete this order payment. Please recharge your wallet and try again.'
            ], 400);
        }

        if($customer->cash_balance > $order->due_amount){

            // Cash balance
            $customer->cash_balance = $customer->cash_balance - $order->due_amount;
            $customer->save();

            $cash_history = new CashWalletTransaction;
            $cash_history->user_id           = $customer->id;
            $cash_history->amount            = $order->due_amount;
            $cash_history->description       = 'Amount debited for Order Id: '.$order->order_id;
            $cash_history->mode              = 'online';
            $cash_history->status            = 'debit';
            $cash_history->transaction_status= 'Amount debited';
            $cash_history->save();

            $cash_history->transaction_id    = 'TX-'.date('Ymd').$cash_history->id.$customer->id.rand(111, 999);
            $cash_history->save();

        }else{

            $remaining_amount = $order->due_amount - $customer->cash_balance;

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

        return response([
            'success'   => true,
            'message'   => 'Order Id '.$order->order_id.' payment successfully completed.',
        ],200);
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'order_id'  => 'required',
            'status'    => 'required'
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $data->status = $request->status;
        $data->cancel_reason = $request->cancel_reason;
        $history = $data->history;
        $history[] = ['status' => 'Order ' .ucwords($request->status). ' By Customer', 'created_at' => Carbon::now()];
        $data->history = $history;
        $data->save();

        if($request->status == 'cancel') {

            $user = User::find($data->customer_user_id);

            $cash_wallet_amount = CashWalletTransaction::where('commodity_product_order_id', $data->id)->where('status', 'debit')->sum('amount');

            if($cash_wallet_amount > 0) {
                $user->cash_balance = $user->cash_balance + $cash_wallet_amount;
                $user->save();

                $cash_history = new CashWalletTransaction;
                $cash_history->user_id           = $user->id;
                $cash_history->commodity_product_order_id = $data->id;
                $cash_history->amount            = $cash_wallet_amount;
                $cash_history->description       = 'Amount refunded from Order Id: '.$data->order_id;
                $cash_history->mode              = 'online';
                $cash_history->status            = 'credit';
                $cash_history->transaction_status= 'Amount Refunded';
                $cash_history->save();

                $cash_history->transaction_id    = 'TX-'.date('Ymd').$cash_history->id.$user->id.rand(111, 999);
                $cash_history->save();
            }

            $credit_wallet_amount = CreditWalletTransaction::where('commodity_product_order_id', $data->id)->where('status', 'debit')->sum('amount');

            if($credit_wallet_amount > 0) {
                $user->credit_balance = $user->credit_balance + $credit_wallet_amount;
                $user->save();

                $credit_history = new CreditWalletTransaction;
                $credit_history->user_id           = $user->id;
                $credit_history->commodity_product_order_id = $data->id;
                $credit_history->amount            = $credit_wallet_amount;
                $credit_history->description       = 'Amount refunded from Order Id: '.$data->order_id;
                $credit_history->status            = 'credit';
                $credit_history->transaction_status= 'Amount Refunded';
                $credit_history->save();

                $credit_history->transaction_id    = 'TX-'.date('Ymd').$credit_history->id.$user->id.rand(111, 999);
                $credit_history->save();

            }

        }

        return response([
            'success'   => true,
            'message'   => 'Order status updated successfully.',
        ],200);
    }
}
