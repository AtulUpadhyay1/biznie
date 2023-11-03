<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;

class Edit extends Component
{
    public $page_title = "Edit Attribute";

    public $hidden_id, $name;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = Attribute::findOrFail($this->hidden_id);
        $this->name = $data->name;
    }

    public function render()
    {
        return view('admin.attributes.form');
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required|unique:attributes,name,'.$this->hidden_id,
        ]);
        $data = Attribute::findOrFail($this->hidden_id);
        $data->name = $this->name;
        $data->save();
        session()->flash('success', 'Attribute created successfully !!');
        return $this->redirect('/admin/attribute',navigate: true);
    }
}
