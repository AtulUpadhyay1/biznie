<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\CommodityProduct;
use App\Models\SellerCommodityProduct;

class SellerPrice extends Component
{
    public $page_title = 'Seller Price';
    public $hidden_id;
    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = CommodityProduct::findOrFail($this->hidden_id);
        $this->page_title       = $data->name.' - Seller Price';
    }

    public function render()
    {
        $list = SellerCommodityProduct::where('commodity_product_id', $this->hidden_id)->with('getStatePrice', 'getBrand', 'getUser', 'getUser.getBusiness')->get();
        return view('admin.commodity_product.seller_price', compact('list'));
    }
}
