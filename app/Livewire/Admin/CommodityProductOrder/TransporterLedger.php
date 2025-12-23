<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;
use App\Models\CommodityProductSellerOrderLedger;
use App\Models\CommodityProductTransporterOrderLedger;

class TransporterLedger extends Component
{
    use WithFileUploads;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'View Transporter Ledger';
    public $hidden_id, $data, $added_by = 'seller', $payment_method = 'cash', $amount, $file, $driver_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Transporter Ledger '. $this->data->getProductEnquiry->unique_id;
        $ledgers = CommodityProductTransporterOrderLedger::where('order_id', $this->hidden_id)
            ->with('getDrivers')
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
        $driver_list = CommodityProductOrderDriver::where('order_id', $this->hidden_id)->get();
        $total_paid = CommodityProductTransporterOrderLedger::where('order_id', $this->hidden_id)->sum('amount');
        return view('admin.commodity_product_order.transporter_ledger', compact('ledgers', 'total_paid', 'driver_list'));
    }

    function save()
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
            'amount'    => 'required|numeric',
            'driver_id' => 'required|exists:commodity_product_order_drivers,id',
        ]);

        if($this->amount > $order->transporter_invoice_amount){
            $this->dispatch('alert',
                type: 'error',
                message: 'You cannot pay more than the actual amount.',
            );
            return;
        }

        $ledgers = CommodityProductTransporterOrderLedger::where('order_id', $this->hidden_id)->orderBy('id', 'DESC')->first();

        if($ledgers && $ledgers->remaining_balance <= 0){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transporter already paid.',
            );
            return;
        }

        if($this->amount <= 0){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transaction amount must be greater than 0.',
            );
            return;
        }

        if($ledgers && $ledgers->remaining_balance <= $this->amount){
            $this->dispatch('alert',
                type: 'error',
                message: 'Transaction amount must be less than or equal to remaining balance.',
            );
            return;
        }

        $remaining_balance = ($ledgers ? $ledgers->remaining_balance : $order->total_freight_amount) - $this->amount;

        $ledger                       = new CommodityProductTransporterOrderLedger;
        $ledger->order_id             = $order->id;
        $ledger->driver_id            = $this->driver_id;
        $ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $ledger->type                 = 'credit';
        $ledger->amount               = $this->amount;
        $ledger->remaining_balance    = $remaining_balance;
        $ledger->payment_method = $this->payment_method;
        $ledger->payment_mode = 'manual';
        $ledger->description = $this->description ?? 'Amount credited for Order Id: '.$this->data->order_id;
        if($this->file){
            $file_name = time().'-'.rand(10, 99).'.'.$this->file->extension();
            $ledger->file = $this->file->storeAs('payments', $file_name, 'public');
        }
        $ledger->added_by = $this->added_by;
        $ledger->added_by_id = auth()->id();
        $ledger->save();

        $driver_data = CommodityProductOrderDriver::where('order_id', $this->hidden_id)->find($this->driver_id);
        $driver_data->advance_amount += $this->amount;
        $driver_data->save();

        if($ledger->added_by == 'seller'){

            $selller_ledgers = CommodityProductSellerOrderLedger::where('order_id', $this->hidden_id)->orderBy('id', 'DESC')->first();

            $seller_credit_ledger                       = new CommodityProductSellerOrderLedger;
            $seller_credit_ledger->order_id             = $order->id;
            $seller_credit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
            $seller_credit_ledger->type                 = 'credit';
            $seller_credit_ledger->amount               = $this->amount;
            $seller_credit_ledger->remaining_balance    = $selller_ledgers->remaining_balance + $this->amount;
            $seller_credit_ledger->description          = 'Amount credited for Order Id: '.$order->order_id . ' for freight.';
            $seller_credit_ledger->save();
        }

        session()->flash('success', 'Payment successfully processed.');
        return $this->redirectRoute('admin.commodity-product-order.transporterLedger', $this->hidden_id, navigate: true);

    }
}
