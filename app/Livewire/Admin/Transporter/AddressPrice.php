<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\Address;
use Livewire\Component;
use App\Models\TransporterDetail;

class AddressPrice extends Component
{
    public $page_title = 'Manage Belt';
    public $loading_address, $state_name = [], $city_name;

    public function mount($id)
    {
        $this->hidden_id      = $id;
        $transporter = TransporterDetail::where('user_id', $this->hidden_id)->first();
    }

    public function render()
    {
        $state_list = Address::select('state')->groupBy('state')->orderBy('state', 'asc')->get();
        $city_list  = Address::whereIn('state', $this->state_name)->select('city')->groupBy('city')->orderBy('city', 'asc')->get();

        return view('admin.transporter.address_price', compact('state_list', 'city_list'));
    }

    public function save()
    {
        dd(1);

    }
}
