<?php

namespace App\Livewire\Admin\CommodityProduct;

use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductUnit;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;

class Edit extends Component
{
    public $page_title = "Edit Commodity Product";
    use WithFileUploads;

    public $name, $category_id, $brand_id, $unit_id, $base_price, $loading_charge, $insurance_charge, $quantity_charge, $description, $thumbnail, $images, $video_url, $meta_title, $meta_description, $meta_image;

    public $charge_name=[], $charge_price=[], $operator=[];
    public $charge = 0, $charge_inputs = [];

    public $size=[], $size_price=[], $dimension=[], $dimension_price=[];
    public $variation = 0, $variation_inputs = [];

    public $is_quality = 0, $quality=[], $quality_price=[];
    public $quality_field = 0, $quality_inputs = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = CommodityProduct::find($id);


    }

    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        return view('admin.commodity_product.edit', compact('category_list', 'brand_list', 'unit_list'));
    }
}
