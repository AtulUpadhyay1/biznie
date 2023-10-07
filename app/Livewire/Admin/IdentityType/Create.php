<?php

namespace App\Livewire\Admin\IdentityType;

use Livewire\Component;
use App\Models\IdentityType;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $name;

    public function render()
    {
        return view('admin.identity_type.form', ['page_title' => 'Create Identity Type']);
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

            session()->flash('success', 'Identity type created successfully !!');
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
