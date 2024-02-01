<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Brand;
use App\Models\Banner;
use App\Models\Product;
use Livewire\Component;
use App\Models\Business;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;

class Create extends Component
{
    public $page_title = "Add Banner";

    use WithFileUploads;

    public $photo, $showPhoto, $banner_type="Main Banner", $url, $resource_type, $resource_id;
    public function render()
    {
        $product_list   = Product::where('request_status', 'approved')->get();
        $category_list  = ProductCategory::active()->orderBy('name', 'asc')->get();
        $busienss_list  = Business::active()->orderBy('name', 'asc')->get();
        $brand_list     = Brand::active()->orderBy('name', 'asc')->get();
        return view('admin.banner.form', compact('product_list','category_list','busienss_list','brand_list'));
    }

    public function save()
    {
        $this->validate([
            'photo'         => 'required|image|mimes:jpg,png,jpeg',
            'banner_type'   => 'required',
            'url'           => 'nullable|url',
        ]);
        if($this->resource_type != null){
            $this->validate([
                'resource_id'=> 'required'
            ]);
        }

        try {

            $data = new Banner;
            $data->photo = imageUpload($this->photo, 'banner');
            $data->banner_type = $this->banner_type;
            $data->published = 0;
            $data->url = $this->url;
            $data->resource_type = $this->resource_type;
            $data->resource_id = $this->resource_id;
            $data->save();
            session()->flash('success', 'Banner added successfully !!');
            return $this->redirect('/admin/banner',navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong',
            );
        }
    }
}
