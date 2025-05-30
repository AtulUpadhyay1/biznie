<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderLedger;

class Ledger extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'View Order Ledger';
    public $hidden_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order Ledger '. $data->getProductEnquiry->unique_id;
        $ledgers = CommodityProductOrderLedger::where('order_id', $this->hidden_id)
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
        $total_credit_wallet = CommodityProductOrderLedger::where('order_id', $this->hidden_id)
            ->where('payment_mode', 'Credit Wallet')
            ->sum('amount');

        return view('admin.commodity_product_order.ledger', compact('data', 'ledgers', 'total_credit_wallet'));
    }
}
