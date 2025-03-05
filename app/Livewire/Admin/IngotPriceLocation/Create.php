<?php

namespace App\Livewire\Admin\IngotPriceLocation;

use Livewire\Component;
use App\Models\IngotPriceLocation;

class Create extends Component
{
    public $hidden_id, $location;
    public function render()
    {
        return view('admin.ingot_price_location.form', ['page_title' => 'Create Ingot Location']);
    }

    public function save()
    {
        $this->validate([
            'location' => 'required',
        ]);

        $data = new IngotPriceLocation;
        $data->location = $this->location;
        $data->save();
        session()->flash('message', 'Ingot Location Created Successfully');
        return redirect()->route('admin.ingot_price_location.index');
    }
}
