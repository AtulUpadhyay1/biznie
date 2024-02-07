<?php

namespace App\Livewire\Admin\Address;

use App\Models\Address;
use Livewire\Component;

class Edit extends Component
{
    public $page_title = "Edit Address";

    public $hidden_id, $pincode, $city, $state, $country;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = Address::findOrFail($this->hidden_id);
        $this->pincode  = $data->pincode;
        $this->city     = $data->city;
        $this->state    = $data->state;
        $this->country  = $data->country;

    }

    public function render()
    {
        return view('admin.address.form');
    }

    public function update()
    {
        $this->validate([
            'pincode'   => 'required|unique:addresses,pincode,'.$this->hidden_id,
            'city'      => 'required',
            'state'     => 'required',
            'country'   => 'required',
        ]);
        $data           = Address::findOrFail($this->hidden_id);
        $data->pincode  = $this->pincode;
        $data->city     = $this->city;
        $data->state    = $this->state;
        $data->country  = $this->country;
        $data->save();
        session()->flash('success', 'Address updated successfully !!');
        return $this->redirectRoute('admin.address.index', navigate: true);
    }
}
