<?php

namespace App\Livewire\Admin\SellerProduct;

use App\Models\Brand;
use Livewire\Component;
use App\Models\HomeProduct;
use Illuminate\Support\Str;
use App\Models\PackagingType;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;
use App\Models\CommodityProductState;
use App\Models\SellerCommodityProduct;
use App\Models\CommodityProductStatePrice;
use App\Models\SellerCommodityProductHistory;
use App\Models\SellerCommodityProductStatePrice;

class Add extends Component
{
    public $page_title = 'Add Product';
    public $user_id, $product_list = [], $brand_list = [], $state_list = [], $city_list = [];
    public $category_id, $product_id, $brand_id, $state, $city;

    public $packaging_type = [], $packaging_type_name = [], $packaging_type_price = [];
    public $loading_address = [];

    public function mount($user_id)
    {
        $this->user_id      = $user_id;
    }

    public function render()
    {
        $category_list = ProductCategory::active()->with('getSubCategory')->get();
        $product_data = CommodityProduct::find($this->product_id ?? 0);
        if($product_data){
            $this->packaging_type       = $product_data->packaging_type;
            $this->packaging_type_price = $product_data->packaging_type_price;
            $this->packaging_type_name = PackagingType::whereIn('id', $this->packaging_type)->pluck('name');
        }
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

    public function setStateList()
    {
        $this->state_list = CommodityProductState::where('commodity_product_id', $this->product_id)->where('brand_id', $this->brand_id)->pluck('state')->unique()->toArray();
    }

    public function setCityList()
    {
        $this->city_list = CommodityProductState::where('commodity_product_id', $this->product_id)->where('brand_id', $this->brand_id)->where('state', $this->state)->pluck('city')->unique()->toArray();
    }

    public function save()
    {
        $this->validate([
            'product_id'    => 'required',
            'brand_id'      => 'required',
            'state'         => 'required',
            'city'          => 'required',
        ]);

        // try {

            $commodity_product = CommodityProduct::find($this->product_id);
            if(!$commodity_product){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'Product not found.',
                );
                return true;
            }

            $state_prices = CommodityProductStatePrice::where('commodity_product_id', $this->product_id)->where('brand_id', $this->brand_id)->where('state', $this->state)->where('city', $this->city)->get();
            if(!$state_prices){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'State price not set for this product.',
                );
                return true;
            }

            $checkData = SellerCommodityProductStatePrice::where('user_id', $this->user_id)->where('commodity_product_id', $commodity_product->id)->where('brand_id', $this->brand_id)->where('state', $this->state)->where('city', $this->city)->exists();

            if($checkData){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'This product combination already taken by this seller.',
                );
                return true;
            }
            
            $data = new SellerCommodityProduct;
            $data->user_id              = $this->user_id;
            $data->commodity_product_id = $commodity_product->id;
            $data->name                 = isset($this->name) && $this->name ? $this->name : $commodity_product->name;
            $data->slug                 = isset($this->name) && $this->name ? Str::slug($this->name) : $commodity_product->slug;
            $data->category_id          = $commodity_product->category_id;
            $data->sub_category_id      = $commodity_product->sub_category_id;
            $data->sub_sub_category_id  = $commodity_product->sub_sub_category_id;
            $data->brand_id             = $this->brand_id;
            $data->unit_id              = $commodity_product->unit_id;
            $data->description          = $commodity_product->description;
            $data->packaging_type       = $commodity_product->packaging_type;
            $data->packaging_type_price = $commodity_product->packaging_type_price;
            $data->base_price           = isset($this->base_price) && $this->base_price ? $this->base_price : $commodity_product->base_price;
            $data->loading_charge       = isset($this->loading_charge) && $this->loading_charge ? $this->loading_charge : $commodity_product->loading_charge;
            $data->insurance_charge     = isset($this->insurance_charge) && $this->insurance_charge ? $this->insurance_charge : $commodity_product->insurance_charge;
            $data->quality_charge       = isset($this->quality_charge) && $this->quality_charge ? $this->quality_charge : $commodity_product->quality_charge;
            $data->gst                  = isset($this->gst) && $this->gst ? $this->gst : $commodity_product->gst;
            $data->tcs                  = isset($this->tcs) && $this->tcs ? $this->tcs : $commodity_product->tcs;
            $data->charge_name          = $commodity_product->charge_name;
            $data->charge_price         = $commodity_product->charge_price;
            $data->operator             = $commodity_product->operator;
            $data->unit                 = $commodity_product->unit;
            $data->attributes           = $commodity_product->attributes;
            $data->variation            = $commodity_product->variation;
            $data->size                 = $commodity_product->size;
            $data->size_price           = $commodity_product->size_price;
            $data->dimension            = $commodity_product->dimension;
            $data->dimension_price      = $commodity_product->dimension_price;
            $data->specification        = $commodity_product->specification;
            $data->is_quality           = $commodity_product->is_quality;
            $data->quality              = $commodity_product->quality;
            $data->quality_price        = $commodity_product->quality_price;
            $data->specification_notes  = $commodity_product->specification_notes;
            $data->thumbnail            = $commodity_product->thumbnail;
            $data->images               = $commodity_product->images;
            $data->loading_address      = [$this->loading_address] ?? [];
            $data->video_url            = $commodity_product->video_url;
            $data->meta_title           = $commodity_product->meta_title;
            $data->meta_description     = $commodity_product->meta_description;
            $data->meta_image           = $commodity_product->meta_image;
            $data->status               = $commodity_product->status;
            $data->save();

            foreach ($state_prices as $state_price) {
                $data_price                                     = new SellerCommodityProductStatePrice;
                $data_price->user_id                            = $this->user_id;
                $data_price->commodity_product_id               = $state_price->commodity_product_id;
                $data_price->commodity_product_variation_id     = $state_price->commodity_product_variation_id;
                $data_price->commodity_product_state_id         = $state_price->commodity_product_state_id;
                $data_price->brand_id                           = $state_price->brand_id;
                $data_price->seller_commodity_product_id        = $data->id;
                $data_price->state                              = $this->state;
                $data_price->city                               = $this->city;
                $data_price->value                              = $state_price->value;
                $data_price->price                              = $state_price->price;
                $data_price->is_selected                        = 1;
                $data_price->save();
            }

            $data_history               = new SellerCommodityProductHistory;
            $data_history->user_id      = $this->user_id;
            $data_history->commodity_product_id = $commodity_product->id;
            $data_history->seller_commodity_product_id = $data->id;
            $data_history->seller_commodity_product_detail = $data;
            $data_history->save();

            foreach($data->loading_address as $loading_address){
                $home_product = HomeProduct::where('commodity_product_id', $commodity_product->id)->where('brand_id', $this->brand_id)->where('city', $this->city)->first();
                if($home_product && $home_product->base_price > $data->base_price){
                    $home_product->user_id      = $this->user_id;
                    $home_product->seller_commodity_product_id = $data->id;
                    $home_product->base_price   = $data->base_price;
                    $home_product->save();
                }else{
                    $home_product = new HomeProduct;
                    $home_product->user_id      = $this->user_id;
                    $home_product->commodity_product_id = $commodity_product->id;
                    $home_product->seller_commodity_product_id = $data->id;
                    $home_product->brand_id     = $this->brand_id;
                    $home_product->city         = $this->city;
                    $home_product->base_price   = $data->base_price;
                    $home_product->save();
                }
            }

            session()->flash('success', 'Product added successfully.');
            return $this->redirectRoute('admin.seller-product.index', $this->user_id, navigate: true);

        // } catch (\Throwable $th) {
        //     $this->dispatch('alert',
        //         type : 'error',
        //         message : 'Something went wrong. Please try again later.',
        //     );

        // }
    }
}
