<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use App\Models\Address;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProduct;
use App\Models\CommodityProductState;
use App\Models\CommodityProductStatePrice;

class Show extends Component
{
    use WithFileUploads;

    public $page_title = 'Commodity Product Show';

    public $hidden_id, $brand_id, $state_name, $city_name ;
    public $chart;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProduct::with('getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit', 'getStateVariation.getBrand', 'getStateVariation', 'getStateVariation.getStateVariationPrice')->findOrFail($this->hidden_id);
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $state_list = Address::select('state')->groupBy('state')->orderBy('state', 'asc')->get();
        $city_list  = Address::where('state', $this->state_name)->select('city')->groupBy('city')->orderBy('city', 'asc')->get();

        $check_price = CommodityProductState::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
        if($check_price){
            $this->dispatch('alert',
                type : 'error',
                message : 'Price already updated for selected data !!',
            );

        }

        return view('admin.commodity_product.show', compact('data', 'brand_list', 'state_list', 'city_list'));
    }

    public function copyStatePrice($state_price_id)
    {
        $this->validate([
            'brand_id'      => 'required',
            'state_name'    => 'required',
            'city_name'     => 'required',
        ]);
        $check_price = CommodityProductState::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
        if($check_price){
            $this->dispatch('alert',
                type : 'error',
                message : 'Price already updated for selected data !!',
            );
            return 1;
        }

        $get_state_price = CommodityProductStatePrice::where('commodity_product_state_id', $state_price_id)->get();

        $state_data = new CommodityProductState;
        $state_data->commodity_product_id = $this->hidden_id;
        $state_data->brand_id             = $this->brand_id;
        $state_data->state                = $this->state_name;
        $state_data->city                 = $this->city_name;
        $state_data->save();

        foreach ($get_state_price as $state_price) {
            $data = new CommodityProductStatePrice;
            $data->commodity_product_id             = $this->hidden_id;
            $data->commodity_product_variation_id   = $state_price->commodity_product_variation_id;
            $data->commodity_product_state_id       = $state_data->id;
            $data->brand_id                         = $this->brand_id;
            $data->state                            = $this->state_name;
            $data->city                             = $this->city_name;
            $data->value                            = $state_price->value;
            $data->price                            = $state_price->price;
            $data->save();
        }

        session()->flash('success', 'Product state price copy successfully !!');
        return $this->redirectRoute('admin.commodity-product.show', $this->hidden_id, navigate: true);
    }

    public function chartStatePrice($state_price_id)
    {
        $this->validate([
            'chart' => 'required|image|mimes:jpg,png,jpeg',
        ], [
            'chart.required' => 'Please select a chart file.'
        ]);
        $data = CommodityProductState::findOrFail($state_price_id);
        $data->chart = imageUpload($this->chart, 'chart', $data->chart);
        $data->save();

        session()->flash('success', 'State price chart update successfully !!');
        return $this->redirectRoute('admin.commodity-product.show', $this->hidden_id, navigate: true);
    }

    public function deleteStatePrice($state_price_id)
    {
        CommodityProductState::destroy($state_price_id);
        CommodityProductStatePrice::where('commodity_product_state_id', $state_price_id)->delete();
        $this->dispatch('alert',
            type : 'success',
            message : 'State price deleted successfully !!',
        );
    }
}
