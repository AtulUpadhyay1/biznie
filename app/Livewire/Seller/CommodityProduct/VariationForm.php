<?php

namespace App\Livewire\Seller\CommodityProduct;

use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\SellerCommodityProduct;

class VariationForm extends Component
{
    public $page_title = "Product variation";

    public $hidden_id, $variation_count = 0, $variation_inputs = [], $selected_attributes = [], $variation = [], $unit = [];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = SellerCommodityProduct::findOrFail($this->hidden_id);
        $this->selected_attributes = $data->attributes;

        $this->variation = $data->variation;

        if($data->variation && count($data->variation) > 0){
            foreach($data->variation['Price'] as $key => $value){
                if($key != 0){
                    array_push($this->variation_inputs, $key);
                }
            }
        }

        $this->variation_count = $key??0;
        if($data->variation && count($data->variation) > 0) {
            $this->variation['Price'] = $data->variation['Price'];
        }else{
            $this->variation['Price'][0] = 0;
        }

        foreach ($this->selected_attributes as $attribute) {
            $this->unit[getAttribute($attribute)->name] = $data->unit ? $data->unit[getAttribute($attribute)->name] : '';
        }

    }

    public function render()
    {
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $data = SellerCommodityProduct::findOrFail($this->hidden_id);
        return view('seller.commodity_product.variation_form', compact('unit_list', 'data'))->layout('seller.layouts.app');
    }

    public function addVariationField($variation_count, $redirect)
    {
        $this->save($redirect);

        $variation_count = $variation_count + 1;
        $this->variation_count = $variation_count;
        array_push($this->variation_inputs, $variation_count);

        $this->variation['Price'][$variation_count] = 0;

    }

    public function removeVariationField($variation_count)
    {
        unset($this->variation_inputs[$variation_count]);

        foreach ($this->selected_attributes as $attribute) {
            unset($this->variation[getAttribute($attribute)->name][$variation_count+1]);
        }
        unset($this->variation['Price'][$variation_count+1]);

    }

    function save($redirect=true)
    {
        //dd($this->variation);

        $this->validate([
            'variation.Price.0'          => 'required|min:0',
        ],[
            'variation.Price.0.required' => 'Enter price.',
        ]);
        foreach ($this->selected_attributes as $attribute) {
            $this->validate([
                'variation.'.getAttribute($attribute)->name.'.0'    => 'required',
            ],[
                'variation.'.getAttribute($attribute)->name.'.0'    => 'Enter '.getAttribute($attribute)->name.'.',
            ]);

            foreach ($this->variation_inputs as $value) {
                $this->validate([
                    'variation.'.getAttribute($attribute)->name.'.'.$value  => 'required',
                    'variation.Price.'.$value                               => 'required|min:0',
                ],[
                    'variation.'.getAttribute($attribute)->name.'.'.$value  => 'Enter '.getAttribute($attribute)->name.'.',
                    'variation.Price.'.$value.'.required'                   => 'Enter price.',

                ]);
            }
        }

        $data = SellerCommodityProduct::find($this->hidden_id);
        $data->unit = $this->unit;
        $data->variation = $this->variation;
        $data->save();

        if($redirect == true){
            session()->flash('success', 'Product variation updated successfully !!');
            return $this->redirectRoute('seller.commodity-product.index',navigate: true);
        }
    }
}
