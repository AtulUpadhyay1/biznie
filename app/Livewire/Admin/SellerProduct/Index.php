<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use App\Models\SellerCommodityProduct;

class Index extends Component
{
    public $hidden_id;
    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $list = SellerCommodityProduct::where('user_id', $this->hidden_id)->with('getCategory', 'getSubCategory', 'getUnit', 'getBrand', 'getStatePrice')->latest()->get();
        return view('admin.seller_product.index', ['page_title' => 'Seller Product']);
    }
}
