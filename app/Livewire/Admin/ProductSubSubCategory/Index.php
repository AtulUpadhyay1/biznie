<?php

namespace App\Livewire\Admin\ProductSubSubCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Index extends Component
{
    use WithFileUploads;

    public $search;
    protected $queryString = [
        'search'        => ['except' => '']
    ];

    public function render()
    {
        $list = ProductSubSubCategory::search($this->search)->latest()->get();
        return view('admin.product_sub_sub_category.index', compact('list'), ['page_title' => 'Product Sub Sub Category']);
    }

    public function updateStatus($id)
    {
        try{
            $data = ProductSubSubCategory::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Product sub sub category active successfully !!': 'Product sub sub category inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }

    }

    public function updateFeatured($id)
    {
        try{
            $data = ProductSubSubCategory::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Product sub sub category featured successfully !!': 'Product sub sub category unfeatured successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

    public function delete($id)
    {
        try{
            ProductSubSubCategory::destroy($id);

            $this->dispatch('alert',
                type: 'success',
                message: 'Product sub sub category deleted successfully !!'
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
