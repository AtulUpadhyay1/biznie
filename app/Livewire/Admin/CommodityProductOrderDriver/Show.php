<?php

namespace App\Livewire\Admin\CommodityProductOrderDriver;

use Livewire\Component;
use App\Models\CommodityProductOrderDriver;

class Show extends Component
{
    public $page_title = 'View Vehicle';
    public $hidden_id, $order_id;
    public function mount($order_id, $id)
    {
        $this->hidden_id = $id;
        $this->order_id = $order_id;
    }

    public function render()
    {
        $data = CommodityProductOrderDriver::findOrFail($this->hidden_id);
        return view('admin.commodity_product_order_driver.show', compact('data'));
    }
}
