<?php

namespace App\Livewire\Admin\IngotPrice;

use Livewire\Component;
use App\Models\IngotPrice;
use App\Models\IngotPriceLocation;

class Edit extends Component
{
    public $hidden_id, $location, $price;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = IngotPrice::findOrFail($this->hidden_id);
        $this->location = $data->location;
        $this->price = $data->price;
    }

    public function render()
    {
        $location_list = IngotPriceLocation::orderBy('location', 'ASC')->get();
        return view('admin.ingot_price.form', compact('location_list'), ['page_title' => 'Edit Ingot Price']);
    }

    public function update()
    {
        $this->validate([
            'location'  => 'required',
            'price'     => 'required|numeric',
        ]);

        $data = IngotPrice::findOrFail($this->hidden_id);
        $data->location = $this->location;
        $data->price = $this->price;
        $data->save();

        session()->flash('success', 'Ingot price updated successfully !!');
        return $this->redirectRoute('admin.ingot_price.index', navigate: true);
    }
}
