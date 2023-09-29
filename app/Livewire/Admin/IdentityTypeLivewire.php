<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\IdentityType;
use Livewire\WithFileUploads;

class IdentityTypeLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;
    public $hidden_id, $name;

    public function render()
    {
        $list = IdentityType::latest()->get();
        return view('admin.identity_type.index', compact('list'), ['page_title' => 'identity-type']);
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
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
        ]);

        try
        {
            $data = new IdentityType;
            $data->name = $this->name;
            $data->save();
            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Identity type created successfully !!'
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
        $data = IdentityType::find($id);
        $this->name = $data->name;
        $this->formMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
        ]);

        try
        {
            $data = IdentityType::find($this->hidden_id);
            $data->name = $this->name;
            $data->save();
            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Identity type updated successfully !!'
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
            $data = IdentityType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Identity type active successfully !!': 'Identity type inactive successfully !!'
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
            IdentityType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Identity type deleted successfully !!'
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
