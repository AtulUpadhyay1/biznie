<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CommodityProduct;
use App\Http\Controllers\Controller;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductHistory;
use App\Http\Resources\CommodityProductResource;
use App\Http\Resources\Seller\MyCommodityProductResource;
use App\Http\Resources\Seller\MyCommodityProductPriceResource;
use App\Http\Resources\Seller\MyCommodityProductVariationResource;

class CommodityProductApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $list = CommodityProduct::active()->latest();
            if(isset($request->category_id) && $request->category_id){
                $list = $list->where('category_id', $request->category_id);
            }
            $list = $list->with('getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit')->get();

            return response([
                'success'   => true,
                'products_list'  => CommodityProductResource::collection($list)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }

    }

    public function myCommodityProductList()
    {
        try {

            $list = SellerCommodityProduct::where('user_id', auth()->id())->paginate(getPaginate());
            return MyCommodityProductResource::collection($list);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id'    => 'required|numeric',
        ]);

        try {

            $commodity_product = CommodityProduct::find($request->product_id);
            if(!$commodity_product){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            $data = new SellerCommodityProduct;
            $data->user_id              = auth()->id();
            $data->commodity_product_id = $commodity_product->id;
            $data->name                 = isset($request->name) && $request->name ? $request->name : $commodity_product->name;
            $data->slug                 = isset($request->name) && $request->name ? Str::slug($request->name) : $commodity_product->slug;
            $data->category_id          = $commodity_product->category_id;
            $data->sub_category_id      = $commodity_product->sub_category_id;
            $data->sub_sub_category_id  = $commodity_product->sub_sub_category_id;
            $data->brand_id             = $commodity_product->brand_id;
            $data->unit_id              = $commodity_product->unit_id;
            $data->description          = $commodity_product->description;
            $data->packaging_type       = $commodity_product->packaging_type;
            $data->packaging_type_price = $commodity_product->packaging_type_price;
            $data->base_price           = isset($request->base_price) && $request->base_price ? $request->base_price : $commodity_product->base_price;
            $data->loading_charge       = isset($request->loading_charge) && $request->loading_charge ? $request->loading_charge : $commodity_product->loading_charge;
            $data->insurance_charge     = isset($request->insurance_charge) && $request->insurance_charge ? $request->insurance_charge : $commodity_product->insurance_charge;
            $data->quality_charge       = isset($request->quality_charge) && $request->quality_charge ? $request->quality_charge : $commodity_product->quality_charge;
            $data->gst                  = isset($request->gst) && $request->gst ? $request->gst : $commodity_product->gst;
            $data->tcs                  = isset($request->tcs) && $request->tcs ? $request->tcs : $commodity_product->tcs;
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

            return response([
                'success'   => true,
                'message'   => 'Product added successfully.',
                'data'      => new MyCommodityProductResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function edit($id)
    {
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            return response([
                'success'   => true,
                'data'      => new MyCommodityProductResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'  => 'required',
        ]);
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }
            $data->name = $request->name;
            $data->slug = Str::slug($request->name);
            $data->save();

            return response([
                'success'   => true,
                'data'      => new MyCommodityProductResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function getPrice($id)
    {
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            return response([
                'success'   => true,
                'data'      => new MyCommodityProductPriceResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function updatePrice(Request $request, $id)
    {
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }
            $data->base_price       = $request->base_price;
            $data->loading_charge   = $request->loading_charge;
            $data->insurance_charge = $request->insurance_charge;
            $data->quality_charge   = $request->quality_charge;
            $data->gst              = $request->gst;
            $data->tcs              = $request->tcs;
            // $data->charge_name      = $request->charge_name ?? $data->charge_name;
            // $data->charge_price     = $request->charge_price ?? $data->charge_price;
            // $data->operator         = $request->operator ?? $data->operator;
            $data->save();

            $data_history               = new SellerCommodityProductHistory;
            $data_history->user_id      = auth()->id();
            $data_history->commodity_product_id = $data->commodity_product_id;
            $data_history->seller_commodity_product_id = $data->id;
            $data_history->seller_commodity_product_detail = $data;
            $data_history->save();

            return response([
                'success'   => true,
                'message'   => 'Product price updated successfully.',
                'data'      => new MyCommodityProductPriceResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function getVariation($id)
    {
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            return response([
                'success'   => true,
                'data'      => new MyCommodityProductVariationResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function updateVariation(Request $request, $id)
    {
        $this->validate($request, [
            'Price'     => 'required|array'
        ]);
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            if(count($data->variation['Price']) != count($request->Price)){
                return response([
                    'success'   => false,
                    'message'   => 'Price array mismatch.',
                ],400);
            }

            $variation = $data->variation;
            $variation['Price'] = $request->Price;
            $data->variation = $variation;
            $data->save();

            $data_history               = new SellerCommodityProductHistory;
            $data_history->user_id      = auth()->id();
            $data_history->commodity_product_id = $data->commodity_product_id;
            $data_history->seller_commodity_product_id = $data->id;
            $data_history->seller_commodity_product_detail = $data;
            $data_history->save();

            return response([
                'success'   => true,
                'message'   => 'Variation price updated successfully.',
                'data'      => new MyCommodityProductVariationResource($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }
}
