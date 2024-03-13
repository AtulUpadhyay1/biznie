<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use App\Models\Address;
use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\CommodityProduct;
use App\Models\CommodityProductStatePrice;

class StatePriceFrom extends Component
{
    public $page_title = "Add State Wise Price";

    public $brand_id, $state_name, $city_name;

    public $hidden_id, $variation_count = 0, $variation_inputs = [], $selected_attributes = [], $variation = [], $unit = [];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = CommodityProduct::findOrFail($this->hidden_id);
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
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $state_list = Address::select('state')->groupBy('state')->orderBy('state', 'asc')->get();
        $city_list  = Address::where('state', $this->state_name)->select('city')->groupBy('city')->orderBy('city', 'asc')->get();
        $unit_list  = ProductUnit::active()->orderBy('name', 'asc')->get();
        $data       = CommodityProduct::findOrFail($this->hidden_id);

        $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
        if($check_price){
            $this->dispatch('alert',
                type : 'error',
                message : 'Price already updated for selected data.',
            );

        }

        return view('admin.commodity_product.state_price_from', compact('brand_list', 'state_list', 'city_list', 'unit_list', 'data'));
    }

    public function save()
    {
        $this->validate([
            'brand_id'      => 'required',
            'state_name'    => 'required',
            'city_name'     => 'required',
        ],[
            'brand_id.required'      => 'Please select a brand.',
            'state_name.required'    => 'Please select a state.',
            'city_name.required'     => 'Please select a city.',
        ]);

        $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
        if($check_price){
            $this->dispatch('alert',
                type : 'error',
                message : 'Price already updated for selected data.',
            );
            return 1;
        }
        $data = new CommodityProductStatePrice;
        $data->commodity_product_id = $this->hidden_id;
        $data->brand_id             = $this->brand_id;
        $data->state                = $this->state_name;
        $data->city                 = $this->city_name;
        $data->price                = $this->variation['Price'];
        $data->save();
        session()->flash('success', 'Product variation price updated successfully !!');
        return $this->redirectRoute('admin.commodity-product.index',navigate: true);
    }
}
