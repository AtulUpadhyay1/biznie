<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\TransporterAddressPrice;

class FreightFinder extends Component
{
    public $loading_address = '';
    public $state = '';
    public $city = '';
    public $loading_addresses = [];
    public $states = [];
    public $cities = [];
    public $transporters = [];
    public $showResults = false;

    public function mount()
    {
        $this->loadLoadingAddresses();
    }

    public function loadLoadingAddresses()
    {
        $this->loading_addresses = TransporterAddressPrice::select('loading_address')
            ->distinct()
            ->whereNotNull('loading_address')
            ->where('loading_address', '!=', '')
            ->orderBy('loading_address')
            ->pluck('loading_address')
            ->toArray();
    }

    public function updatedLoadingAddress($value)
    {
        $this->state = '';
        $this->city = '';
        $this->states = [];
        $this->cities = [];
        $this->transporters = [];
        $this->showResults = false;

        if ($value) {
            $this->states = TransporterAddressPrice::select('state')
                ->where('loading_address', $value)
                ->distinct()
                ->whereNotNull('state')
                ->orderBy('state')
                ->pluck('state')
                ->toArray();
        }
    }

    public function updatedState($value)
    {
        $this->city = '';
        $this->cities = [];
        $this->transporters = [];
        $this->showResults = false;

        if ($value && $this->loading_address) {
            $this->cities = TransporterAddressPrice::select('city')
                ->where('loading_address', $this->loading_address)
                ->where('state', $value)
                ->distinct()
                ->whereNotNull('city')
                ->orderBy('city')
                ->pluck('city')
                ->toArray();
        }
    }

    public function search()
    {
        $this->validate([
            'loading_address' => 'required',
            'state' => 'required',
            'city' => 'required',
        ], [
            'loading_address.required' => 'Please select a loading address.',
            'state.required' => 'Please select a state.',
            'city.required' => 'Please select a city.',
        ]);

        $this->transporters = TransporterAddressPrice::with('getUser')
            ->where('loading_address', $this->loading_address)
            ->where('state', $this->state)
            ->where('city', $this->city)
            ->get();

        $this->showResults = true;

        if ($this->transporters->isEmpty()) {
            $this->dispatch('alert', type: 'info', message: 'No transporters found for the selected criteria.');
        }
    }

    public function clearFilters()
    {
        $this->loading_address = '';
        $this->state = '';
        $this->city = '';
        $this->states = [];
        $this->cities = [];
        $this->transporters = [];
        $this->showResults = false;
    }

    public function render()
    {
        return view('admin.freight-finder');
    }
}
