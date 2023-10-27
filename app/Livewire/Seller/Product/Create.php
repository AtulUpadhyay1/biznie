<?php

namespace App\Livewire\Seller\Product;

use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductUnit;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;

class Create extends Component
{
    public $page_title = "Product add";

    public $category_id, $sub_category_id;
    public $sub_category_list = [];
    public $sub_sub_category_list = [];


    public function render()
    {
        $category_list = ProductCategory::getSellerCategory()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $unit_list = ProductUnit::active()->orderBy('name', 'asc')->get();
        return view('seller.product.create', compact('category_list', 'brand_list', 'unit_list'))->layout('seller.layouts.app');
    }

    public function setSubCategoryList()
    {
        $this->sub_category_list = ProductSubCategory::active()->where('product_category_id', $this->category_id)->get();
    }

    public function setSubSubCategoryList()
    {
        $this->sub_sub_category_list = ProductSubSubCategory::active()->where('product_sub_category_id', $this->sub_category_id)->get();

    }
}
