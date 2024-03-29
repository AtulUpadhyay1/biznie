<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;
use App\Models\CommodityProduct;

class Edit extends Component
{
    public $page_title = "Edit Attribute";

    public $hidden_id, $name;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = Attribute::findOrFail($this->hidden_id);
        $this->name = $data->name;

        $check = CommodityProduct::whereJsonContains('attributes', ''.$id)->first();
        if($check){
            session()->flash('error', "Can't edit this attribute !!");
            return $this->redirectRoute('admin.attribute.index', navigate: true);
        }
    }

    public function render()
    {
        return view('admin.attributes.form');
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required||not_regex:/[.,\/@#$%&*+_=\-!~`]/|unique:attributes,name,'.$this->hidden_id,
        ]);
        $data = Attribute::findOrFail($this->hidden_id);
        $data->name = $this->name;
        $data->save();
        session()->flash('success', 'Attribute created successfully !!');
        return $this->redirectRoute('admin.attribute.index', navigate: true);
    }
}
