<?php

namespace App\Livewire\Seller\Product;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use Livewire\Component;
use App\Models\Attribute;
use App\Models\ProductUnit;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Create extends Component
{
    use WithFileUploads;

    public $page_title = "Product Add";

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $name, $category_id, $sub_category_id, $sub_sub_category_id, $brand_id, $unit_id, $min_qty, $refundable, $images, $color_image, $thumbnail, $featured, $flash_deal, $video_url, $colors="", $variant_product, $attribute=[], $choice_options=[], $variation, $published, $unit_price, $purchase_price, $tax=0, $tax_type='percent', $tax_model='include', $discount=0, $discount_type='flat', $current_stock=0, $minimum_order_qty=1, $details, $free_shipping, $attachment, $status, $featured_status, $shipping_cost=0, $multiply_qty, $code, $meta_title, $meta_description, $meta_image, $search_tags, $request_status, $request_status_updated_by;

    public function render()
    {
        $category_list = ProductCategory::getSellerCategory()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $colors_list = Color::orderBy('name', 'asc')->get();
        $attributes_list = Attribute::orderBy('name', 'asc')->get();
        return view('seller.product.create', compact('category_list', 'brand_list', 'unit_list', 'colors_list', 'attributes_list'))->layout('seller.layouts.app');
    }

    // public function hydrate()
    // {
    //     $this->dispatch('render-select2');
    // }

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
            'thumbnail'         => 'required',
            'unit_price'        => 'required',
            'purchase_price'    => 'required',
        ]);
        $data = new Product;
        $data->added_by = auth()->id();
        $data->user_id = auth()->user()->getBusiness->user_id;
        $data->name = $this->name;
        $data->slug = Str::slug($this->name);
        $data->category_id = $this->category_id;
        $data->sub_category_id = $this->sub_category_id;
        $data->sub_sub_category_id = $this->sub_sub_category_id;
        $data->brand_id = $this->brand_id;
        $data->unit_id = $this->unit_id;
        $data->min_qty = 0;
        $data->refundable = 1;
        $data->images = $this->images ? [imageUpload($this->images, 'product_images')] : '';
        $data->color_image = $this->color_image ? [imageUpload($this->color_image, 'product_color')] : '';
        $data->thumbnail = imageUpload($this->thumbnail, 'product_thumbnail');
        $data->video_url = $this->video_url;
        $data->colors = [];
        $data->attributes = $this->attribute;
        $data->choice_options = $this->choice_options;
        $data->variation = [];
        $data->unit_price = $this->unit_price;
        $data->purchase_price = $this->purchase_price;
        $data->tax = $this->tax;
        $data->tax_type = $this->tax_type;
        $data->tax_model = $this->tax_model;
        $data->discount = $this->discount;
        $data->discount_type = $this->discount_type;
        $data->current_stock = $this->current_stock;
        $data->minimum_order_qty = $this->minimum_order_qty;
        $data->details = $this->details;
        $data->shipping_cost = $this->shipping_cost;
        $data->code = rand(1111, 9999);
        $data->meta_title = $this->meta_title;
        $data->meta_description = $this->meta_description;
        $data->meta_image = $this->meta_image ? imageUpload($this->meta_image, 'product_meta') : '';
        $data->save();
        session()->flash('success', 'Product created successfully !!');
        return $this->redirect('/seller/product',navigate: true);
    }
}
