<?php

namespace App\Livewire\Admin\ProductUnit;

use Livewire\Component;
use App\Models\ProductUnit;
use Livewire\WithFileUploads;

class Index extends Component
{

    public function render()
    {
        $list = ProductUnit::latest()->get();
        return view('admin.product_unit.index', compact('list'), ['page_title' => 'Product Unit']);
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
