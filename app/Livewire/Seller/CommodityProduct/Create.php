<?php

namespace App\Livewire\Seller\CommodityProduct;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PackagingType;
use App\Models\ProductCategory;
use App\Models\CommodityProduct;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductHistory;

class Create extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $page_title = "Add Product";

    public $category_id, $sub_category_id, $sub_sub_category_id, $brand_id, $product_id, $base_price, $loading_charge, $insurance_charge, $quality_inspection_charge, $gst, $tcs;
    public $quality = [], $quality_price = [], $size=[], $size_price=[], $dimension=[], $dimension_price=[], $specification=[], $charge_name=[], $charge_price=[], $operator=[], $packaging_type_id=[], $packaging_type_price=[], $specification_notes;

    // public $category_id, $sub_category_id, $sub_sub_category_id, $product_id, $quality = [], $quality_price = [], $size=[], $size_price=[], $dimension=[], $dimension_price=[], $specification=[], $charge_name=[], $charge_price=[], $operator=[], $packaging_type_id=[], $packaging_type_price=[], $specification_notes;

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public $quality_arr = [], $variant_arr = [], $charge_arr = [], $packaging_type_arr = [];

    public $loading_address_count = 0, $loading_address_inputs = [];
    public $pincode = [] , $address = [];


    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $product_list = CommodityProduct::active()->latest()->with('getCategory')->paginate(getPaginate());
        return view('seller.commodity_product.form', compact('product_list', 'category_list', 'brand_list'))->layout('seller.layouts.app');
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
            // $this->dimension        = $product_data->dimension;
            // $this->dimension_price  = $product_data->dimension_price;
            $this->specification    = $product_data->specification;

            foreach($product_data->size as $size_key => $size_value){
                $variant_arr_data['size'] = $size_value;

                // $variant_arr_data['dimension'] = $product_data->dimension[$size_key];

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
                $charge_arr_data['operator']    = $product_data->operator[$charge_key];
                $this->charge_arr[]             = $charge_arr_data;
            }

        }

        $this->packaging_type_arr = [];

        if($product_data && count($product_data->packaging_type) > 0){
            foreach ($product_data->packaging_type as $packaging_type_key => $packaging_type_id) {
                $packaging_type = PackagingType::find($packaging_type_id);
                $packaging_type_data['id']  = $packaging_type->id;
                $packaging_type_data['name'] = $packaging_type->name;
                $packaging_type_data['price'] = $product_data->packaging_type_price[$packaging_type_key+1];

                $this->packaging_type_arr[] = $packaging_type_data;
            }
        }

        $this->specification_notes = null;
        if($product_data && $product_data->specification_notes){
            $this->specification_notes = $product_data->specification_notes;
        }

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

    public function addAddressField($loading_address_count)
    {
        $loading_address_count = $loading_address_count + 1;
        $this->loading_address_count = $loading_address_count;
        array_push($this->loading_address_inputs, $loading_address_count);
    }

    public function removeAddressField($loading_address_count)
    {
        unset($this->loading_address_inputs[$loading_address_count]);
    }

    public function save()
    {
        dd(request()->all());
        $this->validate([
            'commodity_product_id'  => 'required',
            'base_price'            => 'required',
        ]);

        $product_data = CommodityProduct::find($this->commodity_product_id);
        if(!$product_data){
            $this->dispatch('alert',
                type : 'error',
                message : 'Invalid product selected.',
            );
            return true;
        }

        $data = new SellerCommodityProduct;
        $data->user_id              = auth()->id();
        $data->commodity_product_id = $product_data->id;
        $data->name                 = $product_data->name;
        $data->slug                 = $product_data->slug;
        $data->category_id          = $product_data->category_id;
        $data->sub_category_id      = $product_data->sub_category_id;
        $data->sub_sub_category_id  = $product_data->sub_sub_category_id;
        $data->brand_id             = $product_data->brand_id;
        $data->unit_id              = $product_data->unit_id;
        $data->description          = $product_data->description;
        $data->packaging_type       = $this->packaging_type_id;
        $data->packaging_type_price = $this->packaging_type_price;
        $data->base_price           = $this->base_price;
        $data->loading_charge       = $this->loading_charge;
        $data->insurance_charge     = $this->insurance_charge;
        $data->quality_charge       = $this->quality_charge;
        $data->gst                  = $this->gst;
        $data->tcs                  = $this->tcs;
        $data->charge_name          = $this->charge_name;
        $data->charge_price         = $this->charge_price;
        $data->operator             = $this->operator;
        $data->size                 = $this->size;
        $data->size_price           = $this->size_price;
        $data->dimension            = $this->dimension;
        $data->dimension_price      = $this->dimension_price;
        $data->specification        = $this->specification;
        $data->is_quality           = count($this->quality) > 0 ? 1 : 0;
        $data->quality              = $this->quality;
        $data->quality_price        = $this->quality_price;
        $data->specification_notes  = $product_data->specification_notes;
        $data->pincode              = $this->pincode;
        $data->address              = $this->address;
        $data->thumbnail            = $product_data->thumbnail;
        $data->images               = $product_data->images;
        $data->video_url            = $product_data->video_url;
        $data->meta_title           = $product_data->product_data;
        $data->meta_description     = $product_data->product_data;
        $data->meta_image           = $product_data->meta_image;
        $data->save();

        $data_history = new SellerCommodityProductHistory;
        $data_history->user_id                          = auth()->id();
        $data_history->commodity_product_id             = $product_data->id;
        $data_history->seller_commodity_product_id      = $data->id;
        $data_history->seller_commodity_product_detail  = $data;
        $data_history->save();

        session()->flash('success', 'Product created successfully !!');
        return $this->redirect('/seller/product',navigate: true);
    }

}
