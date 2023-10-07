<?php

namespace App\Livewire\Admin\ProductSubCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;

class Index extends Component
{
    use WithFileUploads;
    public $hidden_id, $product_category_id, $business_category_id, $name, $icon,
    $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;
    public $product_category_list = [];
    public $business_category_list  = null;

    public function render()
    {
        $list = ProductSubCategory::latest()->get();
        return view('admin.product_sub_category.index', compact('list'), ['page_title' => 'Product Sub Category']);
    }

    public function setProductCategoryList()
    {
        $this->product_category_list=ProductCategory::where('business_category_id',$this->business_category_id)->get();
    }

    public function updateStatus($id)
    {
        try{
            $data = ProductSubCategory::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Product sub category active successfully !!': 'Product sub category inactive successfully !!'
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
            $data = ProductSubCategory::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Product sub category featured successfully !!': 'Product sub category unfeatured successfully !!'
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
            ProductSubCategory::destroy($id);

            $this->dispatch('alert',
                type: 'success',
                message: 'Product sub category deleted successfully !!'
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
