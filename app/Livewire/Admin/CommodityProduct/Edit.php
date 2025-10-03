<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use Livewire\Component;
use App\Models\Attribute;
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

    public $hidden_id, $name, $hsn_code, $category_id, $sub_category, $sub_category_id, $sub_sub_category_id, $brand_id, $unit_id, $attribute=[], $description, $thumbnail, $show_thumbnail, $images, $show_image, $video_url, $meta_title, $meta_description, $meta_image, $show_meta_image, $specification_notes, $min_order_qty, $order_amount_type = 'percent', $required_order_amount;

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $packaging_type = [], $packaging_type_name = [], $packaging_type_price = [];

    public $variation_count = 0;

    public function mount($id)
    {
        $this->authorize('commodity_product-edit');
        $this->hidden_id = $id;
        $data = CommodityProduct::with('getCommodityProductVariation')->find($this->hidden_id);
        $this->name                 = $data->name;
        $this->hsn_code             = $data->hsn_code;
        $this->description          = $data->description;
        $this->specification_notes  = $data->specification_notes;
        $this->min_order_qty        = $data->min_order_qty;
        $this->order_amount_type   = $data->order_amount_type ?? 'percent';
        $this->required_order_amount = $data->required_order_amount;
        $this->category_id          = $data->category_id;
        $this->sub_category_id      = $data->sub_category_id;
        $this->sub_sub_category_id  = $data->sub_sub_category_id;
        $this->brand_id             = $data->brand_id;
        $this->unit_id              = $data->unit_id;
        $this->attribute            = $data->attributes;
        $this->packaging_type       = $data->packaging_type;
        $this->packaging_type_price = $data->packaging_type_price;
        $this->show_image           = $data->images && count($data->images) > 0 ? imageUrl($data->images[0]): '';
        $this->show_thumbnail       = imageUrl($data->thumbnail);
        $this->video_url            = $data->video_url;
        $this->meta_title           = $data->meta_title;
        $this->meta_description     = $data->meta_description;
        $this->show_meta_image      = imageUrl($data->meta_image);
        $this->variation_count      = $data->getCommodityProductVariation->count();

        $this->setSubCategoryList();
        $this->setSubSubCategoryList();
    }

    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $packaging_type_list = PackagingType::active()->orderBy('name', 'asc')->get();
        $attribute_list = Attribute::active()->get();

        $packaging_type_ids = $this->packaging_type;

        $packagingTypes = PackagingType::whereIn('id', $packaging_type_ids)
            ->get(['id', 'name'])
            ->keyBy('id');

        $this->packaging_type_name = collect($packaging_type_ids)
            ->map(fn($packaging_type_ids) => optional($packagingTypes->get($packaging_type_ids))->name)
            ->filter();

        return view('admin.commodity_product.form', compact('category_list', 'brand_list', 'unit_list', 'packaging_type_list', 'attribute_list'));
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
            'unit_id'           => 'required',
            'packaging_type'    => 'required',
            'thumbnail'         => 'nullable|image',
            'attribute'         => 'required|array',
        ]);

        $data = CommodityProduct::find($this->hidden_id);
        $data->name             = $this->name;
        $data->hsn_code         = $this->hsn_code;
        $data->slug             = Str::slug($this->name);
        $data->category_id      = $this->category_id;
        $data->sub_category_id  = $this->sub_category_id;
        $data->sub_sub_category_id  = $this->sub_sub_category_id;
        $data->brand_id         = $this->brand_id;
        $data->unit_id          = $this->unit_id;
        $data->attributes       = $this->attribute;
        $data->packaging_type   = $this->packaging_type;
        $data->packaging_type_price = $this->packaging_type_price;
        $data->description      = $this->description;
        $data->specification_notes  = $this->specification_notes;
        $data->min_order_qty    = $this->min_order_qty;
        $data->order_amount_type   = $this->order_amount_type;
        $data->required_order_amount = $this->required_order_amount;
        $data->thumbnail        = $this->thumbnail ? imageUpload($this->thumbnail, 'product_thumbnail', $data->thumbnail) : $data->thumbnail;
        $data->images           = $this->images ? [imageUpload($this->images, 'product_images', $data->images[0])] : $data->images;
        $data->video_url        = $this->video_url;
        $data->meta_title       = $this->meta_title;
        $data->meta_description = $this->meta_description;
        $data->meta_image       = $this->meta_image ? imageUpload($this->meta_image, 'product_meta') : $data->meta_image;
        $data->save();
        session()->flash('success', 'Product updated successfully !!');
        return $this->redirectRoute('admin.commodity-product.index',navigate: true);
    }
}
