<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Brand;
use App\Models\Banner;
use App\Models\Product;
use Livewire\Component;
use App\Models\Business;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;

class Edit extends Component
{
    public $page_title = "Add Banner";

    use WithFileUploads;

    public $hidden_id, $photo, $showPhoto, $banner_type="Main Banner", $url, $resource_type, $resource_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = Banner::find($this->hidden_id);
        $this->showPhoto = imageUrl($data->photo);
        $this->banner_type = $data->banner_type;
        $this->url = $data->url;
        $this->resource_type = $data->resource_type;
        $this->resource_id = $data->resource_id;
    }

    public function render()
    {
        $product_list   = Product::where('request_status', 'approved')->get();
        $category_list  = ProductCategory::active()->orderBy('name', 'asc')->get();
        $busienss_list  = Business::active()->orderBy('name', 'asc')->get();
        $brand_list     = Brand::active()->orderBy('name', 'asc')->get();
        return view('admin.banner.form', compact('product_list','category_list','busienss_list','brand_list'));
    }

    public function update()
    {
        $this->validate([
            'photo'         => 'nullable|image|mimes:jpg,png,jpeg',
            'banner_type'   => 'required',
            'url'           => 'nullable|url',
        ]);
        if($this->resource_type != null){
            $this->validate([
                'resource_id'=> 'required'
            ]);
        }

        try {

            $data = Banner::findOrFail($this->hidden_id);
            $data->photo = $this->photo ? imageUpload($this->photo, 'banner', $data->photo) : $data->photo;
            $data->banner_type = $this->banner_type;
            $data->url = $this->url;
            $data->resource_type = $this->resource_type;
            $data->resource_id = $this->resource_id;
            $data->save();
            session()->flash('success', 'Banner updated successfully !!');
            return $this->redirectRoute('admin.banner.index',navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong',
            );
        }
    }
}
