<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductUnit;
use Livewire\WithFileUploads;

class ProductUnitLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;
    public $hidden_id, $name, $unit;

    public function render()
    {
        $list = ProductUnit::latest()->get();
        return view('admin.product_unit.index', compact('list'), ['page_title' => 'Product Unit']);
    }

    public function create()
    {
        $this->formMode = true;
        $this->resetInputFields();
    }

    public function cancel()
    {
        $this->formMode = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->hidden_id = null;
        $this->name = null;
        $this->unit = null;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'unit' => 'required',
        ]);

        try
        {
            $data = new ProductUnit;
            $data->name = $this->name;
            $data->unit = $this->unit;
            $data->save();
            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Product unit created successfully !!'
            );
        }

        catch(\Exception $e)
        {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

    public function edit($id)
    {
        $this->hidden_id = $id;
        $data = ProductUnit::find($id);
        $this->name= $data->name;
        $this->unit = $data->unit;
        $this->formMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
            'unit' => 'required',
        ]);

        try
        {
            $data = ProductUnit::find($this->hidden_id);
            $data->name = $this->name;
            $data->unit = $this->unit;
            $data->save();
            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Product unit updated successfully !!'
            );
        }

        catch(\Exception $e)
        {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

    public function updateStatus($id)
    {
        try{
            $data = ProductUnit::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Product unit active successfully !!': 'Product unit inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }

    }

    public function delete($id)
    {
        try{
            ProductUnit::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Product unit deleted successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
