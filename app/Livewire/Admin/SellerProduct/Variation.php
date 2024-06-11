<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use App\Models\SellerCommodityProductStatePrice;

class Variation extends Component
{
    public $user_id, $product_id, $variation_price = [];
    public $selectAll = 0, $is_select_all = true;

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        foreach ($list as $key => $data) {
            $this->variation_price[$data->id]['is_selected']    = $data->is_selected;
            $this->variation_price[$data->id]['price']          = $data->price;
        }
        $collection = collect($this->variation_price);
        // $collection->pluck('is_selected')->all();
        $this->is_select_all = $collection->pluck('is_selected')->contains(0) ? false : true;

    }

    public function render()
    {
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        return view('admin.seller_product.variation', compact('list'), ['page_title' => 'Product Variation']);
    }

    public function allSelect($value)
    {
        foreach ($this->variation_price as $id => $priceData) {
            $this->variation_price[$id]['is_selected'] = $value;
        }
        $collection = collect($this->variation_price);
        $this->is_select_all = $collection->pluck('is_selected')->contains(0) ? false : true;
        $this->selectAll = $this->selectAll ? 0 : 1;
        // $this->checkSelectAll();
    }

    public function checkSelectAll()
    {
        // $collection = collect($this->variation_price);
        // $this->is_select_all = $collection->pluck('is_selected')->contains(0) ? false : true;
        // $collection = collect($this->variation_price);
        // $this->is_select_all = $collection->pluck('is_selected')->contains(0) ? false : true;
        // $this->selectAll = $this->selectAll ? 0 : 1;
    }

    public function save()
    {
        $list = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $this->product_id)->get();
        foreach ($list as $key => $data) {
            $this->validate([
                'variation_price.'.$data->id.'.price'    => 'required|min:0',
            ],[
                'variation_price.'.$data->id.'.price'    => 'Enter price for this variation.',
            ]);
            $data->price = $this->variation_price[$data->id]['price'];
            $data->is_selected = $this->variation_price[$data->id]['is_selected'] ? 1 : 0;
            $data->save();
        }
        $this->dispatch('alert',
            type : 'success',
            message : 'Variation price updated successfully.',
        );
    }
}
