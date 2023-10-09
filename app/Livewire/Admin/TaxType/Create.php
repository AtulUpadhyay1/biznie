<?php

namespace App\Livewire\Admin\TaxType;

use App\Models\TaxType;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $value;

    public function render()
    {
        return view('admin.tax_type.form', ['page_title' => 'Create Tax Type']);
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

            session()->flash('success', 'Tax Type created successfully !!');
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
