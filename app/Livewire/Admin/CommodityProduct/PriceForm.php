<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use App\Models\CommodityProduct;

class PriceForm extends Component
{
    public $page_title = "Pricing & others";

    public $hidden_id, $base_price, $loading_charge, $insurance_charge, $quality_charge, $gst, $tcs;

    public $charge_name=[], $charge_price=[], $operator=[];
    public $charge = 0, $charge_inputs = [];

    public function mount($id)
    {
        $this->hidden_id = $id;

        $data = CommodityProduct::findOrFail($this->hidden_id);
        $this->base_price       = $data->base_price;
        $this->loading_charge   = $data->loading_charge;
        $this->insurance_charge = $data->insurance_charge;
        $this->quality_charge   = $data->quality_charge;
        $this->gst              = $data->gst;
        $this->tcs              = $data->tcs;

        $this->charge_name      = $data->charge_name;
        $this->charge_price     = $data->charge_price;
        $this->operator         = $data->operator;

        foreach($data->charge_name as $key => $value){
            array_push($this->charge_inputs, $key);
        }

        $this->charge          = $key??0;
    }

    public function render()
    {
        return view('admin.commodity_product.price_form');
    }

    public function addOtherChargesField($charge)
    {
        $charge = $charge + 1;
        $this->charge = $charge;
        array_push($this->charge_inputs, $charge);

        $this->charge_price[$charge] = 0;
        $this->operator[$charge] = '+';
    }

    public function removeOtherChargesField($charge)
    {
        unset($this->charge_inputs[$charge]);

        unset($this->charge_name[$charge+1]);
        unset($this->charge_price[$charge+1]);
        unset($this->operator[$charge+1]);
    }

    public function save()
    {
        $this->validate([
            'base_price'        => 'required'
        ]);
        foreach ($this->charge_inputs as $value) {
            $this->validate([
                'charge_name.'.$value     => 'required',
                'charge_price.'.$value    => 'required',
                'operator.'.$value        => 'required',
            ]);
        }

        $data = CommodityProduct::find($this->hidden_id);
        $data->base_price       = $this->base_price;
        $data->loading_charge   = $this->loading_charge;
        $data->insurance_charge = $this->insurance_charge;
        $data->quality_charge   = $this->quality_charge;
        $data->gst              = $this->gst;
        $data->tcs              = $this->tcs;
        $data->charge_name      = $this->charge_name;
        $data->charge_price     = $this->charge_price;
        $data->operator         = $this->operator;
        $data->save();

        session()->flash('success', 'Product price updated successfully !!');
        return $this->redirect('/admin/commodity-product',navigate: true);
    }
}
