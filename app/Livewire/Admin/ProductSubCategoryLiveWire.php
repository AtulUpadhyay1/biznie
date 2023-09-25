<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;

class ProductSubCategoryLiveWire extends Component
{
    use WithFileUploads;
    public $formMode = false;
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

    public function create()
    {
        $this->formMode = true;
        $this->business_category_list=BusinessCategory::where('status',1)->get();
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
            'icon' => 'required',
            'business_category_id' => 'required',
            'product_category_id' => 'required',
        ]);

        try
        {
            $data = new ProductSubCategory;
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
                $data->thumbnail = $this->thumbnail->storeAs('product_subcategory', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('product_subcategory', $banner_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Product sub category created successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }

    }

    public function edit($id)
    {
        $this->hidden_id = $id;
        $data = ProductSubCategory::find($id);
        $this->business_category_list=BusinessCategory::where('status',1)->get();
        $this->product_category_list=ProductCategory::where('business_category_id',$data->business_category_id)->get();
        $this->name = $data->name;
        $this->product_category_id = $data->product_category_id;
        $this->business_category_id = $data->business_category_id;
        $this->icon = $data->icon;
        $this->showThumbnail = $data->thumbnail;
        $this->showBanner = $data->banner;
        $this->meta_title= $data->meta_title;
        $this->meta_keywords = $data->meta_keywords;
        $this->meta_description = $data->meta_description;
        $this->formMode = true;
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
        ]);

        try
        {
            $data = ProductSubCategory::find($this->hidden_id);
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
                $data->thumbnail = $this->thumbnail->storeAs('product_subcategory', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('product_subcategory', $banner_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Product sub category updated successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }

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
