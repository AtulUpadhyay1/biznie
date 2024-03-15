<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use App\Models\Address;
use Livewire\Component;
use App\Models\CommodityProduct;
use App\Models\CommodityProductStatePrice;

class Show extends Component
{
    public $page_title = 'Commodity Product Show';

    public $hidden_id, $brand_id, $state_name, $city_name ;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProduct::with('getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit', 'getStatePrice', 'getStatePrice.getBrand')->findOrFail($this->hidden_id);

        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $state_list = Address::select('state')->groupBy('state')->orderBy('state', 'asc')->get();
        $city_list  = Address::where('state', $this->state_name)->select('city')->groupBy('city')->orderBy('city', 'asc')->get();

        $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
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
        $check_price = CommodityProductStatePrice::where('commodity_product_id', $this->hidden_id)->where('brand_id', $this->brand_id)->where('state', $this->state_name)->where('city', $this->city_name)->first();
        if($check_price){
            $this->dispatch('alert',
                type : 'error',
                message : 'Price already updated for selected data !!',
            );
            return 1;
        }

        $get_state_price = CommodityProductStatePrice::findOrFail($state_price_id);

        $data = new CommodityProductStatePrice;
        $data->commodity_product_id = $this->hidden_id;
        $data->brand_id             = $this->brand_id;
        $data->state                = $this->state_name;
        $data->city                 = $this->city_name;
        $data->price                = $get_state_price->price;
        $data->save();
        session()->flash('success', 'Product state price copy successfully !!');
        return $this->redirectRoute('admin.commodity-product.show', $this->hidden_id, navigate: true);
    }

    public function deleteStatePrice($state_price_id)
    {
        CommodityProductStatePrice::destroy($state_price_id);
        $this->dispatch('alert',
            type : 'success',
            message : 'State price deleted successfully !!',
        );
    }
}
