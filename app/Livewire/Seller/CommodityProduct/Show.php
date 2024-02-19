<?php

namespace App\Livewire\Seller\CommodityProduct;

use Livewire\Component;
use App\Models\SellerCommodityProduct;

class Show extends Component
{
    public $page_title = 'Commodity Product Show';

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = SellerCommodityProduct::with('getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit')->findOrFail($this->hidden_id);
        return view('seller.commodity_product.show', compact('data'))->layout('seller.layouts.app');
    }
}
