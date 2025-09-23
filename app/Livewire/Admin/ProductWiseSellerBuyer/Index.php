<?php

namespace App\Livewire\Admin\ProductWiseSellerBuyer;

use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\CommodityProduct;
use App\Models\CommodityProductOrder;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;

class Index extends Component
{
    public $page_title = 'Product Wise Seller & Buyer';

    public $product_id, $brand_id, $city, $order_by, $price_validity;
    public $brand_list, $seller_list, $customer_list;
    protected $queryString = [
        'product_id'    => ['except' => ''],
        'brand_id'      => ['except' => ''],
        'city'          => ['except' => ''],
        'order_by'      => ['except' => ''],
        'price_validity' => ['except' => ''],
    ];

    public function mount()
    {
        if ($this->product_id || $this->brand_id || $this->city || $this->order_by || $this->price_validity){
            $this->search();
        }
    }

    public function render()
    {
        $product_list = CommodityProduct::latest()->with('getCategory')->get();
        $seller_commodity_product_state = SellerCommodityProductStatePrice::groupBy('city')->get();
        return view('admin.product_wise_seller_buyer.index', compact('product_list', 'seller_commodity_product_state'));
    }

    public function search()
    {
        $seller_list = SellerCommodityProduct::where('commodity_product_id', $this->product_id);
        $brand_ids = $seller_list->pluck('brand_id')->unique()->toArray();
        if($this->brand_id){
            $seller_list = $seller_list->where('brand_id', $this->brand_id);
        }

        if($this->city){
            $state_price_data = SellerCommodityProductStatePrice::where('id', $this->city)->first();
            $seller_commodity_product_ids = SellerCommodityProductStatePrice::where('commodity_product_id', $this->product_id)
                ->where('state', $state_price_data->state)
                ->where('city', $state_price_data->city)
                ->pluck('seller_commodity_product_id')->unique()
                ->toArray();

            $seller_list = $seller_list->whereIn('id', $seller_commodity_product_ids);
        }

        if($this->order_by == 'price_validity_asc'){
            $seller_list = $seller_list->orderBy('price_validity', 'ASC');
        } elseif($this->order_by == 'price_validity_desc'){
            $seller_list = $seller_list->orderBy('price_validity', 'DESC');
        } else {
            $seller_list = $seller_list->orderBy('id', 'DESC');
        }

        if($this->price_validity == 'valid'){
            $seller_list = $seller_list->where('price_validity', '>=', now());
        } elseif($this->price_validity == 'expired'){
            $seller_list = $seller_list->where('price_validity', '<', now());
        }


        $this->seller_list = $seller_list->with('getStatePrice', 'getBrand', 'getUser', 'getCommodityProduct', 'getUser.getBusiness', 'getUser.getUserDetail')->get();

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
