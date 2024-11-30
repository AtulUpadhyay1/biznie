<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\Vehicle as VehicleModel;
use Livewire\Component;
use App\Models\TransporterDetail;

class Vehicle extends Component
{
    public $page_title = 'Assign Vehicle';
    public $hidden_id, $vehicle = [];

    public function mount($id)
    {
        $this->hidden_id      = $id;
        $transporter = TransporterDetail::where('user_id', $this->hidden_id)->firstOrFail();
        $this->vehicle = $transporter->vehicle ?? [];
    }

    public function render()
    {
        $vehicle_list = VehicleModel::active()->latest()->get();
        return view('admin.transporter.vehicle', compact('vehicle_list'));
    }

    public function assignVehicle()
    {
        $transporter = TransporterDetail::where('user_id', $this->hidden_id)->first();
        $transporter->vehicle = $this->vehicle;
        $transporter->save();
        session()->flash('success', 'Vehicle assigned successfully.');
        return $this->redirectRoute('admin.transporter.vehicle', $this->hidden_id, navigate: true);
    }
}
