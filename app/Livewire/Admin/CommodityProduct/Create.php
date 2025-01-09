<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use App\Models\Color;
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

class Create extends Component
{
    public $page_title = "Add Commodity Product";
    use WithFileUploads;

    public $name, $hsn_code, $category_id, $sub_category_id, $sub_sub_category_id, $brand_id = [], $unit_id, $attribute=[], $base_price, $loading_charge, $insurance_charge, $quality_charge, $gst, $tcs, $description, $thumbnail, $show_thumbnail, $images, $show_image, $video_url, $meta_title, $meta_description, $meta_image, $show_meta_image, $specification_notes, $min_order_qty, $order_amount_type = 'percent', $required_order_amount;

    public $charge_name=[], $charge_price=[], $operator=[];
    public $charge = 0, $charge_inputs = [];

    public $size=[], $size_price=[], $dimension=[], $dimension_price=[], $specification=[];
    public $variation = 0, $variation_inputs = [];

    public $is_quality = 0, $quality=[], $quality_price=[];
    public $quality_field = 0, $quality_inputs = [];

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $packaging_type = [], $packaging_type_name = [], $packaging_type_price = [];

    public $variation_count = 0;

    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        $packaging_type_list = PackagingType::active()->orderBy('name', 'asc')->get();
        $attribute_list = Attribute::active()->get();

        $this->packaging_type_name = PackagingType::whereIn('id', $this->packaging_type)->pluck('name');

        return view('admin.commodity_product.form', compact('category_list', 'brand_list', 'unit_list', 'packaging_type_list', 'attribute_list'));
    }

    public function addOtherChargesField($charge)
    {
        $charge = $charge + 1;
        $this->charge = $charge;
        array_push($this->charge_inputs, $charge);
    }

    public function removeOtherChargesField($charge)
    {
        unset($this->charge_inputs[$charge]);
    }

    public function addVariationField($variation)
    {
        $variation = $variation + 1;
        $this->variation = $variation;
        array_push($this->variation_inputs, $variation);
    }

    public function removeVariationField($variation)
    {
        unset($this->variation_inputs[$variation]);
    }

    public function addQualityField($quality_field)
    {
        $quality_field = $quality_field + 1;
        $this->quality_field = $quality_field;
        array_push($this->quality_inputs, $quality_field);
    }

    public function removeQualityField($quality_field)
    {
        unset($this->quality_inputs[$quality_field]);
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
            'sub_category_id'   => 'required',
            // 'brand_id'          => 'required',
            'unit_id'           => 'required',
            //'base_price'        => 'required',
            'packaging_type'    => 'required',
            // 'charge_name.*'     => 'required',
            // 'charge_price.*'    => 'required',
            // 'operator.*'        => 'required',
            // 'size.*'            => 'required',
            // 'size_price.*'      => 'required',
            // 'dimension.*'       => 'required',
            // 'dimension_price.*' => 'required',
            // 'quality.*'         => 'required',
            // 'quality_price.*'   => 'required',
            'thumbnail'         => 'required',
            'attribute'         => 'required|array',
        ]);

        // $allInputData = request()->all();
        // dd($allInputData);

        $data = new CommodityProduct;
        $data->name             = $this->name;
        $data->hsn_code         = $this->hsn_code;
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
        $data->min_order_qty    = $this->min_order_qty;
        $data->order_amount_type   = $this->order_amount_type;
        $data->required_order_amount = $this->required_order_amount;
        $data->base_price       = $this->base_price;
        $data->loading_charge   = $this->loading_charge;
        $data->insurance_charge = $this->insurance_charge;
        $data->quality_charge   = $this->quality_charge;
        $data->gst              = $this->gst;
        $data->tcs              = $this->tcs;
        $data->charge_name      = $this->charge_name;
        $data->charge_price     = $this->charge_price;
        $data->operator         = $this->operator;
        $data->attributes       = $this->attribute;
        $data->size             = $this->size;
        $data->size_price       = $this->size_price;
        $data->dimension        = $this->dimension;
        $data->dimension_price  = $this->dimension_price;
        $data->specification    = $this->specification;
        $data->is_quality       = $this->is_quality;
        $data->quality          = $this->quality;
        $data->quality_price    = $this->quality_price;
        $data->thumbnail        = imageUpload($this->thumbnail, 'product_thumbnail');
        $data->images           = $this->images ? [imageUpload($this->images, 'product_images')] : '';
        $data->video_url        = $this->video_url;
        $data->meta_title       = $this->meta_title;
        $data->meta_description = $this->meta_description;
        $data->meta_image       = $this->meta_image ? imageUpload($this->meta_image, 'product_meta') : '';
        $data->save();
        session()->flash('success', 'Product created successfully !!');
        return $this->redirectRoute('admin.commodity-product.index',navigate: true);
    }
}
