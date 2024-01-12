<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductUnit;
use Illuminate\Support\Str;
use App\Models\PackagingType;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Edit extends Component
{
    public $page_title = "Edit Commodity Product";
    use WithFileUploads;

    public $hidden_id, $name, $category_id, $sub_category_id, $sub_sub_category_id, $brand_id, $unit_id, $description, $thumbnail, $show_thumbnail, $images, $show_image, $video_url, $meta_title, $meta_description, $meta_image, $show_meta_image, $specification_notes;

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $packaging_type = [], $packaging_type_name = [], $packaging_type_price = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = CommodityProduct::find($this->hidden_id);

        $this->name                 = $data->name;
        $this->description          = $data->description;
        $this->specification_notes  = $data->specification_notes;
        $this->category_id          = $data->category_id;
        $this->sub_category_id      = $data->sub_category_id;
        $this->sub_sub_category_id  = $data->sub_sub_category_id;
        $this->brand_id             = $data->brand_id;
        $this->unit_id              = $data->unit_id;
        $this->packaging_type       = $data->packaging_type;
        $this->packaging_type_price = $data->packaging_type_price;
        $this->show_image           = $data->images && count($data->images) > 0 ? imageUrl($data->images[0]): '';
        $this->show_thumbnail       = imageUrl($data->thumbnail);
        $this->video_url            = $data->video_url;
        $this->meta_title           = $data->meta_title;
        $this->meta_description     = $data->meta_description;
        $this->show_meta_image      = imageUrl($data->meta_image);

        $this->setSubCategoryList();
        $this->setSubSubCategoryList();
    }

    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $packaging_type_list = PackagingType::active()->orderBy('name', 'asc')->get();

        $this->packaging_type_name = PackagingType::whereIn('id', $this->packaging_type)->pluck('name');

        return view('admin.commodity_product.form', compact('category_list', 'brand_list', 'unit_list', 'packaging_type_list'));
    }

    public function setSubCategoryList()
    {
        $this->sub_category_list = ProductSubCategory::active()->where('product_category_id', $this->category_id)->get();
        $this->sub_sub_category_list = [];
    }

    public function setSubSubCategoryList()
    {
        $this->sub_sub_category_list = ProductSubSubCategory::active()->where('product_sub_category_id', $this->sub_category_id)->get();
    }

    public function save()
    {
        $this->validate([
            'name'              => 'required',
            'category_id'       => 'required',
            'brand_id'          => 'required',
            'unit_id'           => 'required',
            'packaging_type'    => 'required',
            'thumbnail'         => 'nullable|image',
        ]);

        $data = CommodityProduct::find($this->hidden_id);
        $data->name             = $this->name;
        $data->slug             = Str::slug($this->name);
        $data->category_id      = $this->category_id;
        $data->sub_category_id  = $this->sub_category_id;
        $data->sub_sub_category_id  = $this->sub_sub_category_id;
        $data->brand_id         = $this->brand_id;
        $data->unit_id          = $this->unit_id;
        $data->packaging_type   = $this->packaging_type;
        $data->packaging_type_price = $this->packaging_type_price;
        $data->description      = $this->description;
        $data->specification_notes  = $this->specification_notes;
        $data->thumbnail        = $this->thumbnail ? imageUpload($this->thumbnail, 'product_thumbnail') : $data->thumbnail;
        $data->images           = $this->images ? [imageUpload($this->images, 'product_images')] : $data->images;
        $data->video_url        = $this->video_url;
        $data->meta_title       = $this->meta_title;
        $data->meta_description = $this->meta_description;
        $data->meta_image       = $this->meta_image ? imageUpload($this->meta_image, 'product_meta') : $data->meta_image;
        $data->save();
        session()->flash('success', 'Product updated successfully !!');
        return $this->redirect('/admin/commodity-product',navigate: true);
    }
}
