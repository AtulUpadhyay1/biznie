<?php

namespace App\Livewire\Admin\Attributes;

use Livewire\Component;
use App\Models\Attribute;

class Index extends Component
{
    public $page_title = 'Attribute List';

    public function render()
    {
        $list = Attribute::latest()->get();
        return view('admin.attributes.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try{
            $data = Attribute::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Attribute active successfully !!': 'Attribute inactive successfully !!'
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
            Attribute::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Brand deleted successfully !!'
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
