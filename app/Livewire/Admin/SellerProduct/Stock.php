<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use App\Models\SellerCommodityProductStatePrice;

class Stock extends Component
{
    public $user_id, $product_id, $variation_stock = [];

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        foreach ($list as $key => $data) {
            $this->variation_stock[$data->id]['stock'] = $data->stock ?? 0;
        }
    }

    public function render()
    {
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        return view('admin.seller_product.stock', compact('list'), ['page_title' => 'Update Stock']);
    }

    public function save()
    {
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        foreach ($list as $key => $data) {
            $this->validate([
                'variation_stock.'.$data->id.'.stock'    => 'required|min:0',
            ],[
                'variation_stock.'.$data->id.'.stock'    => 'Enter stock for this variation.',
            ]);
            $data->stock = $this->variation_stock[$data->id]['stock'];
            $data->save();
        }
        $this->dispatch('alert',
            type : 'success',
            message : 'Variation stock updated successfully.',
        );
    }
}
