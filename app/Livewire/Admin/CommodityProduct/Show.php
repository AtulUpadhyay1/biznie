<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\CommodityProduct;

class Show extends Component
{
    public $page_title = 'Commodity Product Show';

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProduct::with('getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit')->find($this->hidden_id);
        return view('admin.commodity_product.show', compact('data'));
    }
}
