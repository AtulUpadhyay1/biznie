<?php

namespace App\Livewire\Admin\CommodityProductOrderDriver;

use Livewire\Component;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;

class Quantity extends Component
{
    public $page_title = 'Update Vehicle Quantity';
    public $hidden_id, $order_id, $driver_data, $order_data;

    public function mount($order_id, $id)
    {
        $this->hidden_id = $id;
        $this->order_id = $order_id;

        $this->driver_data = CommodityProductOrderDriver::findOrFail($this->hidden_id);
        $this->order_data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getProductEnquiry')->findOrFail($this->order_id);

    }

    public function render()
    {
        return view('admin.commodity_product_order_driver.quantity');
    }
}
