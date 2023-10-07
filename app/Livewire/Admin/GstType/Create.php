<?php

namespace App\Livewire\Admin\GstType;

use App\Models\GstType;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $value;

    public function render()
    {
        return view('admin.gst_type.form', ['page_title' => 'Create Gst Type']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'value' => 'required',
        ]);

        try
        {
            $data = new GstType;
            $data->name = $this->name;
            $data->value = $this->value;
            $data->save();

            session()->flash('success', 'GST type created successfully !!');
            return $this->redirect('/admin/gst-type',navigate: true);
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
