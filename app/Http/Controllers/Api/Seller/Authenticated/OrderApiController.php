<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CreditWalletTransaction;
use App\Models\CommodityProductOrderDriver;
use App\Http\Resources\Seller\OrderResource;
use App\Models\CommodityProductSellerOrderLedger;
use App\Http\Resources\Seller\OrderDetailResource;

class OrderApiController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->is_staff == 0 ? auth()->id() : auth()->user()->added_by;
        $list = CommodityProductOrder::where('seller_user_id', $user_id)
            ->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getUnit', 'getProductEnquiry')
            ->latest()
            ->paginate(getPaginate());
        return OrderResource::collection($list);
    }

    public function show($id)
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getProductEnquiry', 'getCommodityProduct.getUnit', 'getCommodityProduct.getCategory', 'getDrivers')->find($id);
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

    public function qualityCheck(Request $request)
    {
        $request->validate([
            'order_id'  => 'required',
            'image'     => 'required',
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }

        $data->quality_check_image = $request->image;
        $data->seller_quality_check_message = $request->message;
        $data->quality_check_image_status = 'pending';
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Quality check image updated successfully.',
        ],200);
    }

    public function updateInvoice(Request $request)
    {
        $request->validate([
            'order_id'      => 'required',
            'purpose'       => 'required',
            'purpose_file'  => 'required',
        ]);
        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }
        $purpose            = $request->purpose;
        $data[$purpose]     = $request->purpose_file;
        $data->save();

        return response([
            'success'   => true,
            'message'   => ucfirst(str_replace("_"," ",$purpose)).' updated successfully.',
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
        $data->final_quantity_by_seller = $request->final_quantity;
        $data->save();

        return response([
            'success'   => true,
            'message'   => 'Final quantity updated successfully.',
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
        $history[] = ['status' => 'Order ' .ucwords($request->status). ' By Seller', 'created_at' => Carbon::now()];
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

    public function ledger($id)
    {
        $ledgers = CommodityProductSellerOrderLedger::where('order_id', $id)
            ->orderByDesc('id')
            ->select('id', 'transaction_id', 'type', 'amount', 'remaining_balance', 'description', 'transaction_account_name', 'transaction_account_number', 'transaction_bank_name', 'transaction_number', 'payment_method', 'payment_mode', 'date_time', 'file', 'status', 'created_at')
            ->paginate(getPaginate());

        $ledgers->getCollection()->transform(function ($ledger) {
            $ledger->date_time = dateTimeFormat($ledger->date_time ?? $ledger->created_at);
            $ledger->file = $ledger->file ? asset('storage/' . $ledger->file) : null;
            return $ledger;
        });

        if(!$ledgers){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],404);
        }
        return response([
            'success'   => true,
            'data'      => $ledgers
        ],200);
    }
}
