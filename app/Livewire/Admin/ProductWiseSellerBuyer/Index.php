<?php

namespace App\Livewire\Admin\ProductWiseSellerBuyer;

use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\CommodityProduct;
use App\Models\CommodityProductOrder;
use App\Models\SellerCommodityProduct;

class Index extends Component
{
    public $page_title = 'Product Wise Seller & Buyer';

    public $product_id, $brand_id, $brand_list, $seller_list, $customer_list;
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
        if($this->brand_id){
            $seller_list = $seller_list->where('brand_id', $this->brand_id);
        }
        $this->seller_list = $seller_list->with('getStatePrice', 'getBrand', 'getUser', 'getUser.getBusiness')->get();

        $customer_ids = ProductEnquiry::where('commodity_product_id', $this->product_id);
        if($this->brand_id){
            $customer_ids = $customer_ids->where('brand_id', $this->brand_id);
        }
        $customer_ids = $customer_ids->pluck('user_id')->unique()->toArray();
        $this->customer_list = User::whereIn('id', $customer_ids)->with('getUserDetail')->get();

        foreach ($this->customer_list as $customer_data) {

            $total_enquiry = ProductEnquiry::where('user_id', $customer_data->id)
            ->where('commodity_product_id', $this->product_id);
            if($this->brand_id){
                $total_enquiry = $total_enquiry->where('brand_id', $this->brand_id);
            }
            $total_enquiry = $total_enquiry->count();

            $customer_data->total_enquiry = $total_enquiry;

            $total_order = ProductEnquiry::where('user_id', $customer_data->id)
                ->where('commodity_product_id', $this->product_id);
            if($this->brand_id){
                $total_order = $total_order->where('brand_id', $this->brand_id);
            }
            $total_order = $total_order->where('status', 'ordered')->count();
            $customer_data->total_order = $total_order;

            $total_dispatched_order = CommodityProductOrder::where('customer_user_id', $customer_data->id)
                ->where('commodity_product_id', $this->product_id);
            if($this->brand_id){
                $total_dispatched_order = $total_dispatched_order->where('brand_id', $this->brand_id);
            }
            $total_dispatched_order = $total_dispatched_order->where('status', 'dispatched')->count();
            $customer_data->total_dispatched_order = $total_dispatched_order;

        }
        $this->brand_list = Brand::whereIn('id', $brand_ids)->get();
    }
}
