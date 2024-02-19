<?php

namespace App\Livewire\Admin\ProductUnit;

use Livewire\Component;
use App\Models\ProductUnit;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $short_name;

    public function render()
    {
        return view('admin.product_unit.form', ['page_title' => 'Edit Product Unit']);
    }

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = ProductUnit::find($id);
        $this->name= $data->name;
        $this->short_name = $data->short_name;
    }

    public function update()
    {
        $this->validate([
            'name'          => 'required',
            'short_name'    => 'required',
        ]);

        try
        {
            $data = ProductUnit::find($this->hidden_id);
            $data->name = $this->name;
            $data->short_name = $this->short_name;
            $data->save();

            session()->flash('success', 'Product unit updated successfully !!');
            return $this->redirect('/admin/product-unit',navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }
    }
}
