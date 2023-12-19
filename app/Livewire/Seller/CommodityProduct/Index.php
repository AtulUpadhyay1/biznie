<?php

namespace App\Livewire\Seller\CommodityProduct;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $page_title = "Add Product";

    public $category_id, $product_id, $brand_list, $quality = [], $quality_price = [], $size=[], $size_price=[], $dimension=[], $dimension_price=[], $charge_name=[], $charge_price=[], $operator=[];
    public $quality_arr = [], $variant_arr =[], $charge_arr = [];

    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $product_list = CommodityProduct::active()->latest()->with('getCategory')->paginate(getPaginate());
        return view('seller.commodity_product.index', compact('product_list', 'category_list', 'brand_list'))->layout('seller.layouts.app');
    }

    public function setProductData()
    {
        $product_data = CommodityProduct::find($this->product_id);

        $this->quality_arr = [];
        $this->quality = [];
        if($product_data && $product_data->is_quality == 1){
            foreach ($product_data->quality as $quality_key => $quality_value) {
                $this->quality_arr[$quality_value] = $product_data->quality_price[$quality_key];
            }
        }

        $this->variant_arr  = [];
        $this->size         = [];
        $this->size_price   = [];
        $this->dimension    = [];

        if($product_data && count($product_data->size) > 0){
            $this->size             = $product_data->size;
            $this->size_price       = $product_data->size_price;
            $this->dimension        = $product_data->dimension;
            $this->dimension_price  = $product_data->dimension_price;

            foreach($product_data->size as $size_key => $size_value){
                $variant_arr_data['size'] = $size_value;

                $variant_arr_data['dimension'] = $product_data->dimension[$size_key];

                $this->variant_arr[] = $variant_arr_data;
            }

        }

        $this->charge_arr   = [];
        $this->charge_name  = [];
        $this->charge_price = [];
        $this->operator     = [];

        if($product_data && count($product_data->charge_name) > 0){
            $this->charge_name      = $product_data->charge_name;
            $this->charge_price     = $product_data->charge_price;
            $this->operator         = $product_data->operator;

            foreach($product_data->charge_name as $charge_key => $charge_value){
                $charge_arr_data['charge_name'] = $charge_value;
                $this->charge_arr[]  = $charge_arr_data;
            }

        }
    }

}
