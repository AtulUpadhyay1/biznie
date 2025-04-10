<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use App\Models\CommodityProduct;
use App\Models\CommodityProductOrder;
use App\Models\SellerCommodityProductStatePrice;

class Edit extends Component
{
    public $page_title = 'Edit Order';
    public $hidden_id, $variation_id = [], $variation_quantity = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getCommodityProduct', 'getCustomer', 'getCustomer.getUserDetail', 'getSeller', 'getSeller.getBusiness', 'getTransporter')->findOrFail($this->hidden_id);
        $variations = SellerCommodityProductStatePrice::where('user_id', $data->seller_user_id)
            ->where('commodity_product_id', $data->commodity_product_id)
            ->where('brand_id', $data->brand_id)
            ->where('is_selected', 1)
            ->where('is_brand_selling', 1)
            ->get();
        return view('admin.commodity_product_order.edit', compact('data', 'variations'));
    }

    public function update()
    {
        $this->validate([
            'variation_id'          => 'required|array',
            'variation_quantity'    => 'required|array',
        ]);

        $data = CommodityProductOrder::findOrFail($this->hidden_id);
        $variation_arr = [];
        foreach ($this->variation_id as $key => $variation_id) {
            $variation = SellerCommodityProductStatePrice::with('getSellerCommodityProduct')->find($variation_id);

            $value_arr = [];
            foreach ($variation->value as $value){
                $value['unit']  = null;
                if($variation->getSellerCommodityProduct && $variation->getSellerCommodityProduct->commodity_product_id){
                    $commodity = CommodityProduct::find($variation->getSellerCommodityProduct->commodity_product_id);
                    if($commodity && $commodity->unit){
                        $value['unit']['name']          = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->name : '';
                        $value['unit']['short_name']    = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->short_name : '';
                    }
                }
                $value_arr[] = $value;
            }

            $variation_arr[] = [
                'id'            => $variation_id,
                'value'         => $value_arr,
                'price'         => $variation->price,
                'is_selected'   => $variation->is_selected,
                'is_brand_selling' => 1,
                'quantity'      => $this->variation_quantity[$variation_id],
                'stock'         => $variation->stock,
                'user_selected' => "true",
                'tax'           => 0,
                'per_unit_price'=> 0,
                'final_price'   => 0
            ];
        }

        $data->value = $variation_arr;
        $data->save();

        session()->flash('success', 'Order updated successfully.');
        return redirect()->route('admin.commodity-product-order.show', $this->hidden_id);
    }
}
