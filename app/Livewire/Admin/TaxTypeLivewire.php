<?php

namespace App\Livewire\Admin;

use App\Models\TaxType;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaxTypeLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;
    public $hidden_id, $name, $value;

    public function render()
    {
        $list = TaxType::latest()->get();
        return view('admin.tax_type.index', compact('list'), ['page_title' => 'tax-type']);
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
        $this->value = null;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'value' => 'required',
        ]);

        try
        {
            $data = new TaxType;
            $data->name = $this->name;
            $data->value = $this->value;
            $data->save();
            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Tax type created successfully !!'
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
        $data = TaxType::find($id);
        $this->name = $data->name;
        $this->value = $data->value;
        $this->formMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
            'value' => 'required',
        ]);

        try
        {
            $data = TaxType::find($this->hidden_id);
            $data->name = $this->name;
            $data->value = $this->value;
            $data->save();
            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Tax type updated successfully !!'
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
            $data = TaxType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Tax type active successfully !!': 'Tax type inactive successfully !!'
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
        try
        {
            TaxType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Tax type deleted successfully !!'
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
