<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('admin.commodity_product.index', ['page_title' => 'Commodity Product']);
    }
}
