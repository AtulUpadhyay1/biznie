<?php

namespace App\Livewire\Seller\Product;

use App\Models\Brand;
use App\Models\Color;
use Livewire\Component;
use App\Models\Attribute;
use App\Models\ProductUnit;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Create extends Component
{
    public $page_title = "Product Add";

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $name, $category_id, $sub_category_id, $sub_sub_category_id, $brand_id, $unit_id, $min_qty, $refundable, $images, $color_image, $thumbnail, $featured, $flash_deal, $video_url, $colors, $variant_product, $attribute, $choice_options, $variation, $published, $unit_price, $tax, $tax_type, $tax_model, $discount, $discount_type, $current_stock, $minimum_order_qty, $details, $free_shipping, $attachment, $status, $featured_status, $shipping_cost, $multiply_qty, $code, $meta_title, $meta_description, $meta_image, $search_tags, $request_status, $request_status_updated_by;

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
    }

    public function setSubSubCategoryList()
    {
        $this->sub_sub_category_list = ProductSubSubCategory::active()->where('product_sub_category_id', $this->sub_category_id)->get();
    }

    public function save()
    {
        dd(123);
    }
}
