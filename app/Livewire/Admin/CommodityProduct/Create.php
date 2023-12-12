<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use App\Models\Color;
use Livewire\Component;
use App\Models\Attribute;
use App\Models\ProductUnit;
use App\Models\ProductCategory;

class Create extends Component
{
    public $page_title = "Add Commodity Product";

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $name, $category_id, $sub_category_id, $sub_sub_category_id, $brand_id, $unit_id, $min_qty, $refundable, $images, $color_image, $thumbnail, $featured, $flash_deal, $video_url, $colors="", $variant_product, $attribute=[], $choice_options=[], $variation, $published, $unit_price, $purchase_price, $tax=0, $tax_type='percent', $tax_model='include', $discount=0, $discount_type='flat', $current_stock=0, $minimum_order_qty=1, $details, $free_shipping, $attachment, $status, $featured_status, $shipping_cost=0, $multiply_qty, $code, $meta_title, $meta_description, $meta_image, $search_tags, $request_status, $request_status_updated_by;

    public $i = 0, $inputs = [];


    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $colors_list = Color::orderBy('name', 'asc')->get();
        $attributes_list = Attribute::orderBy('name', 'asc')->get();
        return view('admin.commodity_product.create', compact('category_list', 'brand_list', 'unit_list', 'colors_list', 'attributes_list'));
    }

    public function addOtherChargesField($i)
    {
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
    }

    public function removeOtherChargesField($i)
    {
        unset($this->inputs[$i]);
    }
}
