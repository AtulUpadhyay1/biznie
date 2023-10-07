<?php

namespace App\Livewire\Admin\ProductCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;

class Index extends Component
{
    use WithFileUploads;

    public function render()
    {
        $list = ProductCategory::latest()->get();
        return view('admin.product_category.index', compact('list'), ['page_title' => 'Product Category']);
    }

    public function updateStatus($id)
    {
        try{
            $data = ProductCategory::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Product category active successfully !!': 'Product category inactive successfully !!'
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
            $data = ProductCategory::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Product category featured successfully !!': 'Product category unfeatured successfully !!'
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
            ProductCategory::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Product Category deleted successfully !!'
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
