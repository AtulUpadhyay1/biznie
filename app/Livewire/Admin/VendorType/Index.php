<?php

namespace App\Livewire\Admin\VendorType;

use Livewire\Component;
use App\Models\VendorType;
use Illuminate\Support\Str;

class Index extends Component
{

    public function render()
    {
        $list = VendorType::latest()->get();
        return view('admin.vendor_type.index', compact('list'), ['page_title' => 'Vendor Type']);
    }

    public function updateStatus($id)
    {
        try{
            $data = VendorType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Vendor type active successfully !!': 'Vendor type inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function updateFeatured($id)
    {
        try{
            $data = VendorType::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Vendor type featured successfully !!': 'Vendor type unfeatured successfully !!'
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
            VendorType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Vendor type deleted successfully !!'
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
