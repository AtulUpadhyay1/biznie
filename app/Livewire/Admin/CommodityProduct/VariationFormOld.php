<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\CommodityProduct;

class VariationFormOld extends Component
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
        if($data->size){
            foreach($data->size as $key => $value){
                if($key != 0){
                    array_push($this->variation_inputs, $key);
                }
            }
        }

        $this->variation = $key??0;

        $this->size_price[0] = 0;
        $this->specification[0] = '';

    }

    public function render()
    {
        return view('admin.commodity_product.variation_form_old');
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

    }

    public function save($redirect=true)
    {
        $this->validate([
            'size.0'          => 'required',
            'size_price.0'    => 'required|min:0',
            //'specification.'.$value => 'required',
        ],[
            'size.0.required' => 'Enter size.',
            'size_price.0.required' => 'Enter price.'
        ]);
        foreach ($this->variation_inputs as $value) {
            $this->validate([
                'size.'.$value          => 'required',
                'size_price.'.$value    => 'required|min:0',
                //'specification.'.$value => 'required',
            ],[
                'size.'.$value.'.required'       => 'Enter size.',
                'size_price.'.$value.'.required' => 'Enter price.'
            ]);
        }

        $data = CommodityProduct::find($this->hidden_id);
        $data->size             = array_values($this->size);
        $data->size_price       = array_values($this->size_price);
        $data->dimension        = array_values($this->dimension);
        $data->dimension_price  = array_values($this->dimension_price);
        $data->specification    = array_values($this->specification);
        $data->save();

        if($redirect == true){
            session()->flash('success', 'Product variation updated successfully !!');
            return $this->redirect('/admin/commodity-product',navigate: true);
        }
    }
}
