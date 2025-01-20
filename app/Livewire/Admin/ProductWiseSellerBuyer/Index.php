<?php

namespace App\Livewire\Admin\ProductWiseSellerBuyer;

use App\Models\Brand;
use Livewire\Component;
use App\Models\CommodityProduct;
use App\Models\SellerCommodityProduct;

class Index extends Component
{
    public $page_title = 'Product Wise Seller & Buyer';

    public $product_id, $brand_id, $brand_list, $seller_list;
    protected $queryString = [
        'product_id'    => ['except' => ''],
        'brand_id'      => ['except' => '']
    ];

    public function mount()
    {
        if ($this->product_id || $this->brand_id){
            $this->search();
        }
    }

    public function render()
    {
        $product_list = CommodityProduct::latest()->with('getCategory')->get();
        return view('admin.product_wise_seller_buyer.index', compact('product_list'));
    }

    public function search()
    {
        $seller_list = SellerCommodityProduct::where('commodity_product_id', $this->product_id);
        $brand_ids = $seller_list->pluck('brand_id')->unique()->toArray();
        $this->seller_list = $seller_list->with('getStatePrice', 'getBrand', 'getUser', 'getUser.getBusiness')->get();

        $this->brand_list = Brand::whereIn('id', $brand_ids)->get();
    }
}
