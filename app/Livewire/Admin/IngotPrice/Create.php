<?php

namespace App\Livewire\Admin\IngotPrice;

use Livewire\Component;
use App\Models\IngotPrice;
use App\Models\IngotPriceLocation;

class Create extends Component
{
    public $hidden_id, $location, $price, $date_time;
    public function render()
    {
        $location_list = IngotPriceLocation::orderBy('location', 'ASC')->get();
        return view('admin.ingot_price.form', compact('location_list') , ['page_title' => 'Create Ingot Price']);
    }

    public function save()
    {
        $this->validate([
            'location'  => 'required',
            'price'     => 'required|numeric',
            'date_time' => 'required'
        ]);

        $data = new IngotPrice;
        $data->location = $this->location;
        $data->price = $this->price;
        $data->date_time = $this->date_time;
        $data->save();

        session()->flash('success', 'Ingot price created successfully !!');
        return $this->redirectRoute('admin.ingot_price.index', navigate: true);
    }
}
