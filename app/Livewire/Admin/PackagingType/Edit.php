<?php

namespace App\Livewire\Admin\PackagingType;

use Livewire\Component;
use App\Models\PackagingType;
use App\Models\CommodityProduct;

class Edit extends Component
{
    public $page_title = "Edit Packaging Type";

    public $hidden_id, $name;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = PackagingType::findOrFail($this->hidden_id);
        $this->name = $data->name;

        $check = CommodityProduct::whereJsonContains('packaging_type', ''.$id)->first();
        if($check){
            session()->flash('error', "Can't edit this packaging type !!");
            return $this->redirectRoute('admin.packaging-type.index', navigate: true);
        }
    }

    public function render()
    {
        return view('admin.packaging_type.form');
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required|unique:packaging_types,name,'.$this->hidden_id,
        ]);
        $data = PackagingType::findOrFail($this->hidden_id);
        $data->name = $this->name;
        $data->save();
        session()->flash('success', 'Attribute created successfully !!');
        return $this->redirectRoute('admin.packaging-type.index',navigate: true);
    }
}
