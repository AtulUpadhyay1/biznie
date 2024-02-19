<?php

namespace App\Livewire\Admin\Address;

use App\Models\Address;
use Livewire\Component;

class Create extends Component
{
    public $page_title = "Add Address";

    public $hidden_id, $pincode, $city, $state, $country;

    public function render()
    {
        return view('admin.address.form');
    }

    public function save()
    {
        $this->validate([
            'pincode'   => 'required|unique:addresses,pincode',
            'city'      => 'required',
            'state'     => 'required',
            'country'   => 'required',
        ]);
        $data           = new Address;
        $data->pincode  = $this->pincode;
        $data->city     = $this->city;
        $data->state    = $this->state;
        $data->country  = $this->country;
        $data->save();
        session()->flash('success', 'Address created successfully !!');
        return $this->redirectRoute('admin.address.index', navigate: true);
    }
}
