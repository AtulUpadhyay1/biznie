<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;

class Show extends Component
{
    public $user_id, $product_id;
    public $page_title = 'Product Details';

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;
    }

    public function render()
    {
        $data = SellerCommodityProduct::with('getBrand')->findOrFail($this->product_id);
        $variation_list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        $this->page_title = "Product Details: {$data->name}";
        return view('admin.seller_product.show', compact('data', 'variation_list'));
    }
}
