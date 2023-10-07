<?php

namespace App\Livewire\Admin\IdentityType;

use Livewire\Component;
use App\Models\IdentityType;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id, $name;

    public function render()
    {
        return view('admin.identity_type.form', ['page_title' => 'Edit Identity Type']);
    }

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = IdentityType::find($id);
        $this->name = $data->name;

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

            session()->flash('success', 'Identity type updated successfully !!');
            return $this->redirect('/admin/identity-type',navigate: true);
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
