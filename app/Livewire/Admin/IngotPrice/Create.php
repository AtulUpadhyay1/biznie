<?php

namespace App\Livewire\Admin\IngotPrice;

use Livewire\Component;
use App\Models\IngotPrice;

class Create extends Component
{
    public $hidden_id, $location, $price;
    public function render()
    {
        return view('admin.ingot_price.form', ['page_title' => 'Create Ingot Price']);
    }

    public function save()
    {
        $this->validate([
            'location'  => 'required',
            'price'     => 'required|numeric',
        ]);

        $data = new IngotPrice;
        $data->location = $this->location;
        $data->price = $this->price;
        $data->save();

        session()->flash('success', 'Ingot price created successfully !!');
        return $this->redirectRoute('admin.ingot_price.index', navigate: true);
    }
}
