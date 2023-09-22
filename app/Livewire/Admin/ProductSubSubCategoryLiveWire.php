<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class ProductSubSubCategoryLiveWire extends Component
{
    use WithFileUploads;
    public $formMode = false;
    public $hidden_id, $product_category_id, $product_sub_category_id, $business_category_id, $name, $icon,
    $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;
    public $product_category_list = [];
    public $product_sub_category_list = [];
    public $business_category_list  = null;

    public function render()
    {
        $list = ProductSubSubCategory::latest()->get();
        return view('admin.product_sub_sub_category.index', compact('list'));
    }

    public function setProductCategoryList()
    {
        $this->product_category_list=ProductCategory::where('business_category_id',$this->business_category_id)->get();
    }

    public function setProductSubCategoryList()
    {
        $this->product_sub_category_list = ProductSubCategory::where('product_category_id', $this->product_category_id)->get();
    }

    public function create()
    {
        $this->formMode = true;
        $this->business_category_list = BusinessCategory::where('status',1)->get();
        $this->resetInputFields();
    }

    public function cancel()
    {
        $this->formMode = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->hidden_id = null;
        $this->product_category_id = null;
        $this->product_sub_category_id = null;
        $this->name = null;
        $this->icon = null;
        $this->thumbnail = null;
        $this->banner = null;
        $this->showThumbnail = null;
        $this->showBanner = null;
        $this->meta_title= null;
        $this->meta_keywords = null;
        $this->meta_description = null;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
        ]);

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
        if($this->thumbnail){
            $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
            $data->thumbnail = $this->thumbnail->storeAs('product_subsubcategory', $thumbnail_name, 'public');
        }
        if($this->banner){
            $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
            $data->banner = $this->banner->storeAs('product_subsubcategory', $banner_name, 'public');
        }
        $data->save();

        $this->formMode = false;
        $this->resetInputFields();
    }
}
