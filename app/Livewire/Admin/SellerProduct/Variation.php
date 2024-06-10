<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use App\Models\SellerCommodityProductStatePrice;

class Variation extends Component
{
    public $user_id, $product_id;

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();

    }

    public function render()
    {
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();

        return view('admin.seller_product.variation', compact('list'), ['page_title' => 'Product Variation']);
    }
}
