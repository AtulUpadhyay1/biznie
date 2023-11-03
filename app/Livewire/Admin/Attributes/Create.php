<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;

class Create extends Component
{
    public $page_title = "Add Attribute";

    public $hidden_id, $name;

    public function render()
    {
        return view('admin.attributes.form');
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|unique:attributes,name',
        ]);
        $data = new Attribute;
        $data->name = $this->name;
        $data->save();
        session()->flash('success', 'Attribute created successfully !!');
        return $this->redirect('/admin/attribute',navigate: true);
    }
}
