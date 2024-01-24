<?php

namespace App\Livewire\Admin\ProductCategory;

use Livewire\Component;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $business_category_id, $name, $attribute=[], $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;

    public function render()
    {
        $business_category_list =BusinessCategory::active()->get();
        $attribute_list = Attribute::active()->get();
        return view('admin.product_category.form', compact('business_category_list', 'attribute_list'), ['page_title' => 'Create Product Category']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'attribute' => 'required|array',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
            'icon' => 'required',
            'business_category_id' => 'required',
        ]);

        try{

            $data = new ProductCategory;
            $data->business_category_id = $this->business_category_id;
            $data->name = $this->name;
            $data->attributes = $this->attribute;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            $data->thumbnail = imageUpload($this->thumbnail, 'product_category');
            $data->banner = imageUpload($this->banner, 'product_category');
            $data->save();
            session()->flash('success', 'Product category created successfully !!');
            return $this->redirect('/admin/product-category',navigate: true);

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }
    }

}
