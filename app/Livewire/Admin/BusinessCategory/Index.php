<?php

namespace App\Livewire\Admin\BusinessCategory;

use Livewire\Component;
use App\Models\BusinessCategory;

class Index extends Component
{
    public function render()
    {
        $list = BusinessCategory::latest()->get();
        return view('admin.business_category.index', compact('list'), ['page_title' => 'Business Category']);
    }

    public function updateStatus($id)
    {
        try{

            $data = BusinessCategory::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Business category active successfully !!': 'Business category inactive successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function updateFeatured($id)
    {
        try{
            $data = BusinessCategory::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Business category featured successfully !!': 'Business category unfeatured successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function delete($id)
    {
        try{

            BusinessCategory::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Business category deleted successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }

    }
}
