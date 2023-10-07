<?php

namespace App\Livewire\Admin\Gst;

use App\Models\GstType;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $list = GstType::latest()->get();
        return view('admin.gst_type.index', compact('list'), ['page_title' => 'Gst Type']);
    }

    public function updateStatus($id)
    {
        try{
            $data = GstType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Gst type active successfully !!': 'Gst type inactive successfully !!'
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
            GstType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Gst type deleted successfully !!'
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
