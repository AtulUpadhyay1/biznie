<?php

namespace App\Livewire\Admin\BusinessType;

use Livewire\Component;
use App\Models\BusinessType;
use Illuminate\Support\Str;

class Index extends Component
{

    public function render()
    {
        $list = BusinessType::latest()->get();
        return view('admin.business_type.index', compact('list'), ['page_title' => 'Business Type']);
    }

    public function updateStatus($id)
    {
        try{
            $data = BusinessType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Business type active successfully !!': 'Business type inactive successfully !!'
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
            $data = BusinessType::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Business type featured successfully !!': 'Business type unfeatured successfully !!'
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
            BusinessType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Business type deleted successfully !!'
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
