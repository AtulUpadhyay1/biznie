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

    public $sub_category_list = [];
    public $sub_sub_category_list = [];

    public function render()
    {
        $category_list = ProductCategory::active()->orderBy('name', 'asc')->get();
        $brand_list = Brand::active()->orderBy('name', 'asc')->get();
        $product_list = CommodityProduct::active()->latest()->with('getCategory')->paginate(getPaginate());
        return view('seller.commodity_product.form', compact('product_list', 'category_list', 'brand_list'))->layout('seller.layouts.app');
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

    public function save()
    {
        $this->validate([
            'product_id'            => 'required',
        ]);

        try {

            $commodity_product = CommodityProduct::find($this->product_id);
            if(!$commodity_product){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'Invalid product selected.',
                );
                return true;
            }

            $data = new SellerCommodityProduct;
            $data->user_id              = auth()->id();
            $data->commodity_product_id = $commodity_product->id;
            $data->name                 = $commodity_product->name;
            $data->slug                 = $commodity_product->slug;
            $data->category_id          = $commodity_product->category_id;
            $data->sub_category_id      = $commodity_product->sub_category_id;
            $data->sub_sub_category_id  = $commodity_product->sub_sub_category_id;
            $data->brand_id             = $commodity_product->brand_id;
            $data->unit_id              = $commodity_product->unit_id;
            $data->description          = $commodity_product->description;
            $data->packaging_type       = $commodity_product->packaging_type;
            $data->packaging_type_price = $commodity_product->packaging_type_price;
            $data->base_price           = $commodity_product->base_price;
            $data->loading_charge       = $commodity_product->loading_charge;
            $data->insurance_charge     = $commodity_product->insurance_charge;
            $data->quality_charge       = $commodity_product->quality_charge;
            $data->gst                  = $commodity_product->gst;
            $data->tcs                  = $commodity_product->tcs;
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
            $data->video_url            = $commodity_product->video_url;
            $data->meta_title           = $commodity_product->meta_title;
            $data->meta_description     = $commodity_product->meta_description;
            $data->meta_image           = $commodity_product->meta_image;
            $data->status               = $commodity_product->status;
            $data->save();

            $data_history               = new SellerCommodityProductHistory;
            $data_history->user_id      = auth()->id();
            $data_history->commodity_product_id = $commodity_product->id;
            $data_history->seller_commodity_product_id = $data->id;
            $data_history->seller_commodity_product_detail = $data;
            $data_history->save();

            session()->flash('success', 'Product created successfully !!');
            return $this->redirect('/seller/commodity-product',navigate: true);

        } catch (\Throwable $th) {

            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong !!',
            );
            return true;
        }

    }

}
