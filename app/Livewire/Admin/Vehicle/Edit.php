<?php

namespace App\Livewire\Admin\Vehicle;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $page_title = 'Edit Vehicle';
    public $hidden_id, $name, $type, $capacity, $photo, $show_photo;

    public function mount($id)
    {
        $this->authorize('vehicle-edit');
        $this->hidden_id    = $id;
        $data               = Vehicle::find($this->hidden_id);
        $this->name         = $data->name;
        $this->type         = $data->type;
        $this->capacity     = $data->capacity;
        $this->show_photo   = $data->photo;

    }

    public function render()
    {
        return view('admin.vehicle.form');
    }

    public function update()
    {
        $this->validate([
            'name'      => 'required',
            'type'      => 'required',
            'capacity'  => 'required',
            'photo'     => 'nullable|image',
        ]);

        $data = Vehicle::find($this->hidden_id);
        $data->name = $this->name;
        $data->type = $this->type;
        $data->capacity = $this->capacity;
        $data->photo = $this->photo ? imageUpload($this->photo, 'vehicle') : $data->photo;
        $data->save();

        session()->flash('success', 'Vehicle updated successfully.');
        return $this->redirectRoute('admin.vehicle.index', navigate: true);
    }
}
