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

    public $brand_id, $state_name, $city_name, $state_price_id;

    public $hidden_id, $variation_count = 0, $variation_inputs = [], $selected_attributes = [], $variation = [], $unit = [];

    protected $queryString = [
        'state_price_id'    => ['except' => ''],
    ];

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
        $this->variation['Price'][0] = 0;

        if($this->state_price_id){
            $get_state_price = CommodityProductStatePrice::findOrFail($this->state_price_id);
            $this->variation['Price'] = $get_state_price->price;
            $this->brand_id = $get_state_price->brand_id;
            $this->state_name = $get_state_price->state;
            $this->city_name = $get_state_price->city;
        }

        // if($data->variation && count($data->variation) > 0) {
        //     $this->variation['Price'] = $data->variation['Price'];
        // }else{
        //     $this->variation['Price'][0] = 0;
        // }

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

        if(!$this->state_price_id){
            $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
            if($check_price){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'Price already updated for selected data.',
                );

            }
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
        if(!$this->state_price_id){
            $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
            if($check_price){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'Price already updated for selected data.',
                );
                return 1;
            }
        }

        $data = new CommodityProductStatePrice;
        if($this->state_price_id){
            $data = CommodityProductStatePrice::findOrFail($this->state_price_id);
        }
        $data->commodity_product_id = $this->hidden_id;
        $data->brand_id             = $this->brand_id;
        $data->state                = $this->state_name;
        $data->city                 = $this->city_name;
        $data->price                = array_values($this->variation['Price']);
        $data->save();
        session()->flash('success', 'Product variation price updated successfully !!');
        return $this->redirectRoute('admin.commodity-product.index',navigate: true);
    }
}
