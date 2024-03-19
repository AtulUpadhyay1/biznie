<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use App\Models\Address;
use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\CommodityProduct;
use App\Models\CommodityProductState;
use App\Models\CommodityProductVariation;
use App\Models\CommodityProductStatePrice;

class StatePriceFrom extends Component
{
    public $page_title = "Add State Wise Price";

    public $brand_id, $state_name, $city_name, $state_price_id;

    public $hidden_id, $variation_inputs = [], $selected_attributes = [], $variation=[], $uploaded_variation = [], $unit = [];

    protected $queryString = [
        'state_price_id'    => ['except' => ''],
    ];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = CommodityProduct::with('getCommodityProductVariation')->findOrFail($this->hidden_id);
        $this->page_title       = $data->name.' - Add State Wise Price';
        $this->selected_attributes = $data->attributes;

        $attribute_name_arr = [];
        foreach($this->selected_attributes as $attribute){
            $attribute_name_arr [] = getAttribute($attribute)->name;
        }

        if($data->getCommodityProductVariation && count($data->getCommodityProductVariation) > 0){
            foreach ($data->getCommodityProductVariation as $product_variation) {
                foreach($product_variation->value as $variation_key => $variation_value){

                    $uploaded_variation_data[$attribute_name_arr[$variation_key]] = $variation_value['value'];
                    $uploaded_variation_data['Price'] = '0';
                    $this->uploaded_variation[$product_variation->id] = $uploaded_variation_data;

                }
            }
        }

        if($this->state_price_id){
            $get_state_price = CommodityProductStatePrice::findOrFail($this->state_price_id);
            $this->variation['Price'] = $get_state_price->price;
            $this->brand_id = $get_state_price->brand_id;
            $this->state_name = $get_state_price->state;
            $this->city_name = $get_state_price->city;
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

        if(!$this->state_price_id){
            $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
            if($check_price){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'Price already updated for selected data !!',
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
                    message : 'Price already updated for selected data !!',
                );
                return 1;
            }
        }

        if(! $this->uploaded_variation && count($this->uploaded_variation) == 0){
            $this->dispatch('alert',
                type : 'error',
                message : 'No variation update for this product !!',
            );
            return 1;
        }

        $state_data = new CommodityProductState;
        $state_data->commodity_product_id = $this->hidden_id;
        $state_data->brand_id             = $this->brand_id;
        $state_data->state                = $this->state_name;
        $state_data->city                 = $this->city_name;
        $state_data->save();

        foreach ($this->uploaded_variation as $uploaded_variation_id => $uploaded_variation) {
            $uploaded_product_variation = CommodityProductVariation::find($uploaded_variation_id);
            if($uploaded_product_variation){
                $data = new CommodityProductStatePrice;
                $data->commodity_product_id             = $this->hidden_id;
                $data->commodity_product_variation_id   = $uploaded_product_variation->id;
                $data->commodity_product_state_id       = $state_data->id;
                $data->brand_id                         = $this->brand_id;
                $data->state                            = $this->state_name;
                $data->city                             = $this->city_name;
                $data->value                            = $uploaded_product_variation->value;
                $data->price                            = $this->uploaded_variation[$uploaded_product_variation->id]['Price'];
                $data->save();
            }
        }

        // $data = new CommodityProductStatePrice;
        // if($this->state_price_id){
        //     $data = CommodityProductStatePrice::findOrFail($this->state_price_id);
        // }
        // $data->commodity_product_id = $this->hidden_id;
        // $data->brand_id             = $this->brand_id;
        // $data->state                = $this->state_name;
        // $data->city                 = $this->city_name;
        // $data->price                = array_values($this->variation['Price']);
        // $data->save();
        session()->flash('success', 'Product variation price updated successfully !!');
        // if($this->state_price_id){
        //     return $this->redirectRoute('admin.commodity-product.show', $this->hidden_id, navigate: true);
        // }
        return $this->redirectRoute('admin.commodity-product.index', navigate: true);
    }
}
