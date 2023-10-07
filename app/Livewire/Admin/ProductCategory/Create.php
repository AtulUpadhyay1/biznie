<?php

namespace App\Livewire\Admin\ProductCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id,$business_category_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;
    public $business_category_list=null;

    public function render()
    {
        $this->business_category_list=BusinessCategory::where('status',1)->get();
        return view('admin.product_category.form', ['page_title' => 'Create Product Category']);
    }

    public function save()
    {

        $this->validate([
            'name' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
            'icon' => 'required',
            'business_category_id' => 'required',
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
            session()->flash('success', 'Product category created successfully !!');
            return $this->redirect('/admin/product-category',navigate: true);
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

}
