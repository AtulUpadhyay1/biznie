<?php

namespace App\Livewire\Admin\IngotPriceLocation;

use Livewire\Component;
use App\Models\IngotPriceLocation;

class Edit extends Component
{
    public $hidden_id, $location;

    public function mount($id){
        $this->hidden_id = $id;
        $data = IngotPriceLocation::find($id);
        $this->location = $data->location;
    }

    public function render()
    {
        return view('admin.ingot_price_location.form', ['page_title' => 'Update Ingot Location']);
    }

    public function update()
    {
        $this->validate([
            'location' => 'required',
        ]);

        $data = IngotPriceLocation::find($this->hidden_id);
        $data->location = $this->location;
        $data->save();
        session()->flash('success', 'Ingot Location Updated Successfully');
        return redirect()->route('admin.ingot_price_location.index');
    }

}
