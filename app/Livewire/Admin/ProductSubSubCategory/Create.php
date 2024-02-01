<?php

namespace App\Livewire\Admin\ProductSubSubCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $product_category_id, $product_sub_category_id, $business_category_id, $name, $icon,
    $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;
    public $product_category_list = [];
    public $product_sub_category_list = [];
    public $business_category_list  = null;

    public function render()
    {
        $this->business_category_list = BusinessCategory::where('status',1)->get();
        return view('admin.product_sub_sub_category.form', ['page_title' => 'Create Product Sub SubCategory']);
    }

    public function setProductCategoryList()
    {
        $this->product_category_list=ProductCategory::where('business_category_id',$this->business_category_id)->get();
    }

    public function setProductSubCategoryList()
    {
        $this->product_sub_category_list = ProductSubCategory::where('product_category_id', $this->product_category_id)->get();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
            // 'icon' => 'required',
            'business_category_id' => 'required',
            'product_category_id' => 'required',
            'product_sub_category_id' => 'required',
        ]);
        try{

            $data = new ProductSubSubCategory;
            $data->product_sub_category_id = $this->product_sub_category_id;
            $data->product_category_id = $this->product_category_id;
            $data->business_category_id = $this->business_category_id;
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            $data->thumbnail = imageUpload($this->thumbnail, 'product_subsubcategory');
            $data->banner = imageUpload($this->banner, 'product_subsubcategory');
            $data->save();

            session()->flash('success', 'Product sub sub category created successfully !!');
            return $this->redirectRoute('admin.product-sub-subcategory',navigate: true);

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }
}
