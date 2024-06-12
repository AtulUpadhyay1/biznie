<?php

namespace App\Livewire\Admin\SellerProduct;

use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;
use App\Models\CommodityProductState;

class Add extends Component
{
    public $page_title = 'Add Product';
    public $user_id, $product_list = [], $brand_list = [];
    public $category_id, $product_id, $brand_id;

    public function mount($user_id)
    {
        $this->user_id      = $user_id;
    }

    public function render()
    {
        $category_list = ProductCategory::active()->with('getSubCategory')->get();
        return view('admin.seller_product.add', compact('category_list'));
    }

    public function setProductList()
    {
        $this->product_list = CommodityProduct::where('category_id', $this->category_id)->get();
    }

    public function setBrandList()
    {
        $brand_ids = CommodityProductState::where('commodity_product_id', $this->product_id)->pluck('brand_id')->unique()->toArray();
        $this->brand_list = Brand::whereIn('id', $brand_ids)->where('status', '1')->get();
    }
}
