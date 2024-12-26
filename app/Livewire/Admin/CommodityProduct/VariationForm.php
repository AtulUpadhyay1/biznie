<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\CommodityProduct;
use App\Models\CommodityProductVariation;
use App\Models\CommodityProductStatePrice;
use App\Models\SellerCommodityProductStatePrice;

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
            CommodityProductStatePrice::where('commodity_product_variation_id', $id)->delete();
            SellerCommodityProductStatePrice::where('commodity_product_variation_id', $id)->delete();

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

    public function makeDefaultVariation($id)
    {
        $data = CommodityProductVariation::where('commodity_product_id', $this->hidden_id)->find($id);
        if(!$data){
            $this->dispatch('alert',
                type : 'error',
                message : 'Product variation not found !!',
            );
        }
        CommodityProductVariation::where('commodity_product_id', $this->hidden_id)->update(['is_default' => 0]);
        $data->is_default = 1;
        $data->save();

        $this->dispatch('alert',
            type : 'success',
            message : 'Default product variation set successfully !!',
        );
    }

    function save($redirect=true)
    {
        if($this->variation){
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

            $state_variations = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->select('commodity_product_state_id', 'brand_id', 'state', 'city')->distinct()->get();
            if($state_variations){
                foreach ($state_variations as $state_variation) {
                    $new_state_variation = new CommodityProductStatePrice;
                    $new_state_variation->commodity_product_id = $this->hidden_id;
                    $new_state_variation->commodity_product_variation_id = $product_variation->id;
                    $new_state_variation->commodity_product_state_id = $state_variation->commodity_product_state_id;
                    $new_state_variation->brand_id = $state_variation->brand_id;
                    $new_state_variation->state = $state_variation->state;
                    $new_state_variation->city = $state_variation->city;
                    $new_state_variation->value = $variation_arr;
                    $new_state_variation->price = 0;
                    $new_state_variation->save();
                }
            }

            $seller_state_variations = SellerCommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->select('user_id', 'commodity_product_state_id', 'brand_id', 'seller_commodity_product_id', 'state', 'city')->distinct()->get();
            if($seller_state_variations){
                foreach ($seller_state_variations as $seller_state_variation) {
                    $new_seller_state_variation = new SellerCommodityProductStatePrice;
                    $new_seller_state_variation->user_id = $seller_state_variation->user_id;
                    $new_seller_state_variation->commodity_product_id = $this->hidden_id;
                    $new_seller_state_variation->commodity_product_variation_id = $product_variation->id;
                    $new_seller_state_variation->commodity_product_state_id = $seller_state_variation->commodity_product_state_id;
                    $new_seller_state_variation->brand_id = $seller_state_variation->brand_id;
                    $new_seller_state_variation->seller_commodity_product_id = $seller_state_variation->seller_commodity_product_id;
                    $new_seller_state_variation->state = $seller_state_variation->state;
                    $new_seller_state_variation->city = $seller_state_variation->city;
                    $new_seller_state_variation->value = $variation_arr;
                    $new_seller_state_variation->price = 0;
                    $new_seller_state_variation->is_selected = 0;
                    $new_seller_state_variation->save();
                }
            }
        }

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

                    $state_prices = CommodityProductStatePrice::where('commodity_product_variation_id', $uploaded_variation_id)->get();
                    if($state_prices){
                        foreach($state_prices as $state_price){
                            $state_price->value = $variation_arr;
                            $state_price->save();
                        }
                    }

                    $seller_state_prices = SellerCommodityProductStatePrice::where('commodity_product_variation_id', $uploaded_variation_id)->get();
                    if($seller_state_prices){
                        foreach ($seller_state_prices as $seller_state_price) {
                            $seller_state_price->value = $variation_arr;
                            $seller_state_price->save();
                        }
                    }
                }
            }
        }

        if($redirect == true){
            session()->flash('success', 'Product variation updated successfully !!');
            return $this->redirectRoute('admin.commodity-product.index', navigate: true);
        }
    }

    public function updateUnit()
    {
        try {
            $data = CommodityProduct::find($this->hidden_id);
            $data->unit = $this->unit;
            $data->save();
            $this->dispatch('alert',
                type : 'success',
                message : 'Unit updated successfully !!',
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong !!',
            );
        }
    }
}
