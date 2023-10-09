<?php

namespace App\Livewire\Admin\TaxType;

use App\Models\TaxType;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{

    public function render()
    {
        $list = TaxType::latest()->get();
        return view('admin.tax_type.index', compact('list'), ['page_title' => 'tax-type']);
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
