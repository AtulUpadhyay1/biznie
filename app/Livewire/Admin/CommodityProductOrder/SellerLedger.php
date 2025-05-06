<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductSellerOrderLedger;

class SellerLedger extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'View Seller Ledger';
    public $hidden_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Seller Ledger '. $data->getProductEnquiry->unique_id;
        $ledgers = CommodityProductSellerOrderLedger::where('order_id', $this->hidden_id)
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());

        return view('admin.commodity_product_order.seller_ledger', compact('data', 'ledgers'));
    }
}
