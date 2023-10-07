<?php

namespace App\Livewire\Admin\GstType;

use App\Models\GstType;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $value;

    public function render()
    {
        return view('admin.gst_type.form', ['page_title' => 'Edit Gst Type']);
    }

    public function mount($id)
    {

        $this->hidden_id = $id;
        $data = GstType::find($id);
        $this->name = $data->name;
        $this->value = $data->value;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
            'value' => 'required',
        ]);

        try
        {
            $data = GstType::find($this->hidden_id);
            $data->name = $this->name;
            $data->value = $this->value;
            $data->save();

            session()->flash('success', 'GST type updated successfully !!');
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
