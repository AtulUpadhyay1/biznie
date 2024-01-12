<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\CommodityProduct;

class QualityForm extends Component
{
    public $page_title = "Product quality";

    public $hidden_id, $is_quality = 0, $quality=[], $quality_price=[];
    public $quality_field = 0, $quality_inputs = [];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = CommodityProduct::findOrFail($this->hidden_id);
        $this->quality             = $data->quality;
        $this->quality_price       = $data->quality_price;

        foreach($data->quality as $key => $value){
            if($key != 0){
                array_push($this->quality_inputs, $key);
            }
        }

        $this->quality_field = $key??0;

    }

    public function render()
    {
        return view('admin.commodity_product.quality_form');
    }

    public function addQualityField($quality_field)
    {
        $quality_field = $quality_field + 1;
        $this->quality_field = $quality_field;
        array_push($this->quality_inputs, $quality_field);
    }

    public function removeQualityField($quality_field)
    {
        unset($this->quality_inputs[$quality_field]);

        unset($this->quality[$quality_field+1]);
        unset($this->quality_price[$quality_field+1]);

        $this->quality_field -= 1;
    }

    public function save()
    {
        //dd($this->quality_inputs);
        $this->validate([
            'quality.0'          => 'required',
            'quality_price.0'    => 'required',
        ]);
        foreach ($this->quality_inputs as $value) {
            $this->validate([
                'quality.'.$value          => 'required',
                'quality_price.'.$value    => 'required',
            ]);
        }

        $data = CommodityProduct::find($this->hidden_id);
        $data->is_quality       = 1;
        $data->quality          = $this->quality;
        $data->quality_price    = $this->quality_price;
        $data->save();

        session()->flash('success', 'Product price updated successfully !!');
        return $this->redirect('/admin/commodity-product',navigate: true);
    }

}
