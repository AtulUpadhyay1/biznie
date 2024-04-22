<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use App\Models\CommodityProductOrder;

class Show extends Component
{
    public $page_title = 'View Order';

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order '. $data->order_id;
        return view('admin.commodity_product_order.show', compact('data'));
    }
}
