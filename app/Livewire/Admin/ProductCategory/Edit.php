<?php

namespace App\Livewire\Admin\ProductCategory;

use Livewire\Component;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id,$business_category_id, $name, $attribute=[], $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;

    public function render()
    {
        $business_category_list=BusinessCategory::active()->get();
        $attribute_list = Attribute::active()->get();
        return view('admin.product_category.form', compact('business_category_list', 'attribute_list'), ['page_title' => 'Edit Product Category']);
    }

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = ProductCategory::find($id);
        $this->business_category_list=BusinessCategory::where('status',1)->get();
        $this->name = $data->name;
        $this->attribute = $data->attributes;
        $this->business_category_id = $data->business_category_id;
        $this->icon = $data->icon;
        $this->showThumbnail = imageUrl($data->thumbnail);
        $this->showBanner = imageUrl($data->banner);
        $this->meta_title= $data->meta_title;
        $this->meta_keywords = $data->meta_keywords;
        $this->meta_description = $data->meta_description;
    }

    public function update()
    {
        $this->validate([
            'name'      => 'required',
            // 'attribute' => 'required|array',
            'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg',
            'banner'    => 'nullable|image|mimes:jpg,png,jpeg',
            // 'icon'      => 'required',
            'business_category_id' => 'required',
        ]);

        try{
            $data = ProductCategory::find($this->hidden_id);
            $data->name = $this->name;
            $data->attributes = $this->attribute;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            $data->thumbnail = $this->thumbnail ? imageUpload($this->thumbnail, 'product_category') : $data->thumbnail;
            $data->banner = $this->banner ? imageUpload($this->banner, 'product_category') : $data->banner;
            $data->save();
            session()->flash('success', 'Product category created successfully !!');
            return $this->redirectRoute('admin.product-category',navigate: true);

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }

    }
}
