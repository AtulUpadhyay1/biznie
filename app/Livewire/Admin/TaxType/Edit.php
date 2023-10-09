<?php

namespace App\Livewire\Admin\TaxType;

use App\Models\TaxType;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $value;

    public function render()
    {
        return view('admin.tax_type.form', ['page_title' => 'Edit Tax Type']);
    }

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = TaxType::find($id);
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
            $data = TaxType::find($this->hidden_id);
            $data->name = $this->name;
            $data->value = $this->value;
            $data->save();

            session()->flash('success', 'Tax Type updated successfully !!');
            return $this->redirect('/admin/tax-type',navigate: true);
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
