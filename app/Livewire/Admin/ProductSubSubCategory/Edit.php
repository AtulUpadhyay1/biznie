<?php

namespace App\Livewire\Admin\ProductSubSubCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Edit extends Component
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
        return view('admin.product_sub_sub_category.form', ['page_title' => 'Edit Product Sub Sub Category']);
    }

    public function setProductCategoryList()
    {
        $this->product_category_list=ProductCategory::where('business_category_id',$this->business_category_id)->get();
    }

    public function setProductSubCategoryList()
    {
        $this->product_sub_category_list = ProductSubCategory::where('product_category_id', $this->product_category_id)->get();
    }

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = ProductSubSubCategory::find($id);
        $this->business_category_list=BusinessCategory::where('status',1)->get();
        $this->product_category_list=ProductCategory::where('business_category_id',$data->business_category_id)->get();
        $this->product_sub_category_list = ProductSubCategory::where('product_category_id', $data->product_category_id)->get();
        $this->name = $data->name;
        $this->product_sub_category_id = $data->product_sub_category_id;
        $this->product_category_id = $data->product_category_id;
        $this->business_category_id = $data->business_category_id;
        $this->icon = $data->icon;
        $this->showThumbnail = $data->thumbnail;
        $this->showBanner = $data->banner;
        $this->meta_title= $data->meta_title;
        $this->meta_keywords = $data->meta_keywords;
        $this->meta_description = $data->meta_description;
    }

    public function update()
    {

        $this->validate([
            'name'  => 'required',
            'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg',
            'banner'    => 'nullable|image|mimes:jpg,png,jpeg',
            'icon' => 'required',
            'business_category_id' => 'required',
            'product_category_id' => 'required',
            'product_sub_category_id' => 'required',
        ]);

        try{
            $data = ProductSubSubCategory::find($this->hidden_id);
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

            session()->flash('success', 'Product sub sub category updated successfully !!');
            return $this->redirect('/admin/product-sub-subcategory',navigate: true);
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }
}
