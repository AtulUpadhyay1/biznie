<?php

namespace App\Livewire\Admin\Vehicle;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $page_title = 'Create Vehicle';
    public $name, $type, $capacity, $photo;

    public function render()
    {
        return view('admin.vehicle.form');
    }

    public function save()
    {
        $this->validate([
            'name'      => 'required',
            'type'      => 'required',
            'capacity'  => 'required',
            'photo'     => 'required|image',
        ]);

        $data = new Vehicle;
        $data->name = $this->name;
        $data->type = $this->type;
        $data->capacity = $this->capacity;
        $data->photo = imageUpload($this->photo, 'vehicle');
        $data->save();

        session()->flash('success', 'Vehicle created successfully.');
        return $this->redirectRoute('admin.vehicle.index', navigate: true);
    }
}
