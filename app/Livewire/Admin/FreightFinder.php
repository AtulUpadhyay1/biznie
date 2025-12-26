<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\TransporterAddressPrice;

class FreightFinder extends Component
{
    public $state = '';
    public $city = '';
    public $states = [];
    public $cities = [];
    public $transporters = [];
    public $showResults = false;

    public function mount()
    {
        $this->loadStates();
    }

    public function loadStates()
    {
        $this->states = TransporterAddressPrice::select('state')
            ->distinct()
            ->whereNotNull('state')
            ->orderBy('state')
            ->pluck('state')
            ->toArray();
    }

    public function updatedState($value)
    {
        $this->city = '';
        $this->cities = [];
        $this->transporters = [];
        $this->showResults = false;

        if ($value) {
            $this->cities = TransporterAddressPrice::select('city')
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
            'state' => 'required',
            'city' => 'required',
        ], [
            'state.required' => 'Please select a state.',
            'city.required' => 'Please select a city.',
        ]);

        $this->transporters = TransporterAddressPrice::with('getUser')
            ->where('state', $this->state)
            ->where('city', $this->city)
            ->get();

        $this->showResults = true;

        if ($this->transporters->isEmpty()) {
            $this->dispatch('alert', type: 'info', message: 'No transporters found for the selected location.');
        }
    }

    public function clearFilters()
    {
        $this->state = '';
        $this->city = '';
        $this->cities = [];
        $this->transporters = [];
        $this->showResults = false;
    }

    public function render()
    {
        return view('admin.freight-finder');
    }
}
