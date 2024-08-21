<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use App\Models\CommodityProductOrder;

class PrintInvoice extends Component
{
    public $page_title = 'Print Invoice';

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'Print Invoice '. $data->order_id;
        return view('admin.commodity_product_order.print_invoice', compact('data'));
    }
}
