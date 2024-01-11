<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\CommodityProduct;

class VariationForm extends Component
{
    public $page_title = "Product variation setup";

    public $hidden_id, $size=[], $size_price=[], $dimension=[], $dimension_price=[], $specification=[];
    public $variation = 0, $variation_inputs = [];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = CommodityProduct::findOrFail($this->hidden_id);
        $this->size             = $data->size;
        $this->size_price       = $data->size_price;
        // $this->dimension        = $data->dimension;
        // $this->dimension_price  = $data->dimension_price;
        $this->specification    = $data->specification;

        foreach($data->size as $key => $value){
            if($key != 0){
                array_push($this->variation_inputs, $key);
            }
        }

        $this->variation = $key??0;

    }

    public function render()
    {
        return view('admin.commodity_product.variation_form');
    }

    public function addVariationField($variation, $redirect)
    {
        $this->save($redirect);

        $variation = $variation + 1;
        $this->variation = $variation;
        array_push($this->variation_inputs, $variation);

        $this->size_price[$variation] = 0;
        $this->specification[$variation] = '';
    }

    public function removeVariationField($variation)
    {
        unset($this->variation_inputs[$variation]);

        unset($this->size[$variation+1]);
        unset($this->size_price[$variation+1]);
        unset($this->specification[$variation+1]);

        $this->variation -= 1;
    }

    public function save($redirect=true)
    {
        foreach ($this->variation_inputs as $value) {
            $this->validate([
                'size.'.$value          => 'required',
                'size_price.'.$value    => 'required',
                //'specification.'.$value => 'required',
            ]);
        }

        $data = CommodityProduct::find($this->hidden_id);
        $data->size             = $this->size;
        $data->size_price       = $this->size_price;
        $data->dimension        = $this->dimension;
        $data->dimension_price  = $this->dimension_price;
        $data->specification    = $this->specification;
        $data->save();

        if($redirect == true){
            session()->flash('success', 'Product price updated successfully !!');
            return $this->redirect('/admin/commodity-product',navigate: true);
        }
    }
}
