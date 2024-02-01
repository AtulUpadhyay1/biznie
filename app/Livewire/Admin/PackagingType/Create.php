<?php

namespace App\Livewire\Admin\PackagingType;

use Livewire\Component;
use App\Models\PackagingType;

class Create extends Component
{
    public $page_title = "Add Packaging Type";

    public $hidden_id, $name;

    public function render()
    {
        return view('admin.packaging_type.form');
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|unique:packaging_types,name',
        ]);
        $data = new PackagingType;
        $data->name = $this->name;
        $data->save();
        session()->flash('success', 'Packaging type created successfully !!');
        return $this->redirectRoute('admin.packaging-type.index',navigate: true);
    }
}
