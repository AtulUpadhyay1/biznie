<?php

namespace App\Livewire\Admin\ProductUnit;

use Livewire\Component;
use App\Models\ProductUnit;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $short_name;

    public function render()
    {
        return view('admin.product_unit.form', ['page_title' => 'Create Product Unit']);
    }

    public function save()
    {
        $this->validate([
            'name'          => 'required',
            'short_name'    => 'required',
        ]);

        try
        {
            $data = new ProductUnit;
            $data->name = $this->name;
            $data->short_name = $this->short_name;
            $data->save();

            session()->flash('success', 'Product unit created successfully !!');
            return $this->redirect('/admin/product-unit',navigate: true);
        }

        catch(\Exception $e)
        {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }
}
