<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\PackagingType;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;

class Edit extends Component
{
    public $page_title = "Edit Commodity Product";
    use WithFileUploads;

    public $name, $category_id, $sub_category_id, $sub_sub_category_id, $brand_id, $unit_id, $base_price, $loading_charge, $insurance_charge, $quality_charge, $gst, $tcs, $description, $thumbnail, $images, $video_url, $meta_title, $meta_description, $meta_image, $specification_notes;

    public $charge_name=[], $charge_price=[], $operator=[];
    public $charge = 0, $charge_inputs = [];

    public $size=[], $size_price=[], $dimension=[], $dimension_price=[], $specification=[];
    public $variation = 0, $variation_inputs = [];

    public $is_quality = 0, $quality=[], $quality_price=[];
    public $quality_field = 0, $quality_inputs = [];

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $packaging_type = [], $packaging_type_name = [], $packaging_type_price = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = CommodityProduct::find($id);

        $this->name             = $data->name;
        $this->description      = $data->description;
        $this->specification    = $data->specification;
        

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
}
