<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\CommodityProduct;
use App\Models\CommodityProductVariation;

class VariationForm extends Component
{
    public $page_title = "Product variation";

    public $hidden_id, $variation_inputs = [], $selected_attributes = [], $variation=[], $uploaded_variation = [], $unit = [];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = CommodityProduct::with('getCommodityProductVariation')->findOrFail($this->hidden_id);
        $this->page_title       = $data->name.' - Product variation';
        $this->selected_attributes = $data->attributes;

        foreach ($this->selected_attributes as $attribute) {
            $this->unit[getAttribute($attribute)->name] = $data->unit ? $data->unit[getAttribute($attribute)->name] : '';
        }

    }

    public function render()
    {
        $data = CommodityProduct::with('getCommodityProductVariation')->findOrFail($this->hidden_id);
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $this->setVariationData();
        return view('admin.commodity_product.variation_form', compact('unit_list','data'));
    }

    public function setVariationData()
    {
        $data = CommodityProduct::with('getCommodityProductVariation')->findOrFail($this->hidden_id);

        $attribute_name_arr = [];
        foreach($this->selected_attributes as $attribute){
            $attribute_name_arr [] = getAttribute($attribute)->name;
        }

        if($data->getCommodityProductVariation && count($data->getCommodityProductVariation) > 0){
            foreach ($data->getCommodityProductVariation as $product_variation) {
                foreach($product_variation->value as $variation_key => $variation_value){

                    $uploaded_variation_data[$attribute_name_arr[$variation_key]] = $variation_value['value'];
                    $this->uploaded_variation[$product_variation->id] = $uploaded_variation_data;

                }
            }
        }
    }

    public function addVariation($redirect)
    {
        $this->save($redirect);
        foreach ($this->selected_attributes as $attribute) {
            $this->variation[getAttribute($attribute)->name] = null;
        }
    }

    public function removeVariation($id)
    {
        try {
            CommodityProductVariation::destroy($id);
            $this->dispatch('alert',
                type : 'success',
                message : 'Product variation deleted successfully !!',
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong !!',
            );
        }

    }

    function save($redirect=true)
    {
        foreach ($this->selected_attributes as $attribute) {
            $this->validate([
                'variation.'.getAttribute($attribute)->name    => 'required',
            ],[
                'variation.'.getAttribute($attribute)->name    => 'Enter '.getAttribute($attribute)->name.'.',
            ]);
        }

        // dd($this->variation);

        $variation_arr = [];
        foreach ($this->selected_attributes as $key => $attribute) {
            $variation_data['id']       = $attribute;
            $variation_data['name']     = getAttribute($attribute)->name;
            $variation_data['value']    = $this->variation[getAttribute($attribute)->name];
            $variation_arr[]            = $variation_data;
        }
        $product_variation = new CommodityProductVariation;
        $product_variation->commodity_product_id = $this->hidden_id;
        $product_variation->value = $variation_arr;
        $product_variation->save();

        if($this->uploaded_variation && count($this->uploaded_variation) > 0) {
            foreach ($this->uploaded_variation as $uploaded_variation_id => $uploaded_variation) {
                $uploaded_product_variation = CommodityProductVariation::find($uploaded_variation_id);
                if($uploaded_product_variation){
                    $variation_arr = [];
                    foreach ($this->selected_attributes as $key => $attribute) {
                        $variation_data['id']       = $attribute;
                        $variation_data['name']     = getAttribute($attribute)->name;
                        $variation_data['value']    = $uploaded_variation[getAttribute($attribute)->name];
                        $variation_arr[]            = $variation_data;
                    }
                    $uploaded_product_variation->value = $variation_arr;
                    $uploaded_product_variation->save();
                }
            }
        }

        if($redirect == true){
            session()->flash('success', 'Product variation updated successfully !!');
            return $this->redirectRoute('admin.commodity-product.index', navigate: true);
        }
    }
}
