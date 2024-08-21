<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use App\Models\CommodityProductOrder;

class History extends Component
{
    public $page_title = 'Order history';
    public $hidden_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getProductEnquiry')->findOrFail($this->hidden_id);
        return view('admin.commodity_product_order.history', compact('data'));
    }
}
