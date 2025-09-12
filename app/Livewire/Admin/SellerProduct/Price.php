<?php

namespace App\Livewire\Admin\SellerProduct;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductHistory;

class Price extends Component
{
    public $user_id, $product_id;
    public $price_validity, $quantity, $base_price, $loading_charge, $insurance_charge, $quality_charge, $gst, $tcs;

    public $charge_name=[], $charge_price=[], $operator=[];
    public $charge = 0, $charge_inputs = [];

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;

        $data = SellerCommodityProduct::findOrFail($product_id);
        $this->base_price       = $data->base_price;

        $date = Carbon::parse($data->price_validity);
        $this->price_validity   = $date->format('Y-m-d\TH:i');
        $this->quantity         = $data->quantity;
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


    }

    public function render()
    {
        return view('admin.seller_product.price', ['page_title' => 'Update Price']);
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

        try {
            $data = SellerCommodityProduct::findOrFail($this->product_id);
            $data->base_price       = $this->base_price;
            $data->price_validity   = Carbon::parse($this->price_validity)->format('Y-m-d h:i A');
            $data->quantity         = $this->quantity;
            $data->loading_charge   = $this->loading_charge;
            $data->insurance_charge = $this->insurance_charge;
            $data->quality_charge   = $this->quality_charge;
            $data->gst              = $this->gst;
            $data->tcs              = $this->tcs;
            $data->charge_price     = $this->charge_price;
            $data->save();

            $data_history               = new SellerCommodityProductHistory;
            $data_history->user_id      = $this->user_id;
            $data_history->commodity_product_id = $data->commodity_product_id;
            $data_history->seller_commodity_product_id = $data->id;
            $data_history->seller_commodity_product_detail = $data;
            $data_history->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Product price updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong. Please try again later.',
            );
        }
    }
}
