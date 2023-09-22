<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;

class ProductCategoryLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;
    public $hidden_id,$business_category_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;
    public $business_category_list=null;

    public function render()
    {
        $list = ProductCategory::latest()->get();
        return view('admin.product_category.index', compact('list'), ['page_title' => 'Product Category']);
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
        $this->business_category_id = null;
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

        try
        {
            $data = new ProductCategory;
            $data->business_category_id = $this->business_category_id;
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
                $data->thumbnail = $this->thumbnail->storeAs('product_category', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('product_category', $banner_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();
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
        $data = ProductCategory::find($id);
        $this->business_category_list=BusinessCategory::where('status',1)->get();
        $this->name = $data->name;
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
        ]);

        try
        {
            $data = ProductCategory::find($this->hidden_id);
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
                $data->thumbnail = $this->thumbnail->storeAs('product_category', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('product_category', $banner_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();
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
        $data = ProductCategory::find($id);
        $data->status = $data->status ? 0 : 1;
        $data->save();
    }

    public function updateFeatured($id)
    {
        $data = ProductCategory::find($id);
        $data->featured = $data->featured ? 0 : 1;
        $data->save();
    }

    public function delete($id)
    {
        ProductCategory::destroy($id);
    }
}
