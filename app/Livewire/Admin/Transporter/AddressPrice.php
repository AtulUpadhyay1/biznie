<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\Address;
use Livewire\Component;
use App\Models\TransporterDetail;
use App\Models\TransporterAddressPrice;

class AddressPrice extends Component
{
    public $page_title = 'Manage Belt';
    public $hidden_id, $loading_address, $state_name = [], $selected_city = [], $min_price = [], $max_price = [];

    public function mount($id)
    {
        $this->hidden_id      = $id;
        $transporter = TransporterDetail::where('user_id', $this->hidden_id)->first();
    }

    public function render()
    {
        $state_list = Address::select('state')->groupBy('state')->orderBy('state', 'asc')->get();
        $city_list  = Address::whereIn('state', $this->state_name)->orderBy('city', 'asc')->get()->groupBy('state')
        ->map(function ($cities) {
            return $cities->pluck('city')->toArray();
        });

        return view('admin.transporter.address_price', compact('state_list', 'city_list'));
    }

    public function save()
    {
        $this->validate([
            'loading_address'   => 'required',
            'state_name'        => 'required',
        ]);
        if($this->selected_city){
            foreach ($this->selected_city as $selected_city) {
                $this->validate([
                    'min_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'required',
                    'max_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'required',
                ],[
                    'min_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'Enter '.$selected_city.' Min Price.',
                    'max_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'Enter '.$selected_city.' Max Price.',
                ]);
            }

            foreach ($this->selected_city as $selected_city) {

                $states_data = Address::where('city', $selected_city)->first();

                $data = new TransporterAddressPrice;
                $data->user_id = $this->hidden_id;
                $data->loading_address = $this->loading_address;
                $data->state = $states_data ? $states_data->state : '';
                $data->city = $selected_city;
                $data->min_price = $this->min_price[strtolower(str_replace(" ","_",$selected_city))];
                $data->max_price = $this->max_price[strtolower(str_replace(" ","_",$selected_city))];
                $data->save();
            }

            session()->flash('success', 'Vehicle assigned successfully.');
            return $this->redirectRoute('admin.transporter.addressPrice', $this->hidden_id, navigate: true);
        }

        $this->dispatch('alert',
            type : 'error',
            message : 'Please select a city first and try again.',
        );
    }
}
