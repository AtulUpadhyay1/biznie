<?php

namespace App\Http\Controllers\Api\Customer\Authenticated;

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
        $list = CommodityProductOrder::where('customer_user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit')->latest()->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function ledger($order_id)
    {
        $list = CommodityProductOrderLedger::where('order_id', $order_id)->get(['transaction_id', 'type', 'amount', 'remaining_balance', 'description', 'created_at']);
        return response([
            'success'   => true,
            'data'      => $list
        ],200);
    }

    public function show($id)
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit', 'getCommodityProduct.getCategory', 'getDrivers')->find($id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
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
}
