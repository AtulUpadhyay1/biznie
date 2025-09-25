<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\HomeProduct;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CommodityProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\CommodityProductState;
use App\Models\SellerCommodityProduct;
use App\Models\CommodityProductStatePrice;
use App\Models\SellerCommodityProductHistory;
use App\Http\Resources\CommodityProductResource;
use App\Models\SellerCommodityProductStatePrice;
use App\Http\Resources\Seller\MyCommodityProductResource;
use App\Http\Resources\Seller\MyCommodityProductPriceResource;
use App\Http\Resources\Seller\MyCommodityProductVariationResource;
use App\Http\Resources\Seller\MyCommodityProductVariationStockResource;

class CommodityProductApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $list = CommodityProduct::active()->latest();
            if($request->category_id){
                $list = $list->where('category_id', $request->category_id);
            }
            if($request->sub_category_id){
                $list = $list->where('sub_category_id', $request->sub_category_id);
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

    public function getBrand(Request $request)
    {
        $request->validate([
            'commodity_product_id'  => 'required'
        ]);
        $brand_ids = CommodityProductState::where('commodity_product_id', $request->commodity_product_id)->pluck('brand_id')->unique()->toArray();
        $brand_list = Brand::whereIn('id', $brand_ids)->where('status', '1')->get();
        return response([
            'success'           => true,
            'brand_list'        => BrandResource::collection($brand_list)
        ],200);
    }

    public function getState(Request $request)
    {
        $request->validate([
            'commodity_product_id'  => 'required',
            'brand_id'              => 'required',
        ]);
        $state_list = CommodityProductState::where('commodity_product_id', $request->commodity_product_id)->where('brand_id', $request->brand_id)->pluck('state')->unique()->toArray();
        return response([
            'success'           => true,
            'state_list'        => array_values($state_list),
        ],200);
    }

    public function getCity(Request $request)
    {
        $request->validate([
            'commodity_product_id'  => 'required',
            'brand_id'              => 'required',
            'state'                 => 'required',
        ]);

        $city_list = CommodityProductState::where('commodity_product_id', $request->commodity_product_id)->where('brand_id', $request->brand_id)->where('state', $request->state)->pluck('city')->unique()->toArray();
        return response([
            'success'           => true,
            'city_list'        => array_values($city_list),
        ],200);
    }

    public function myCommodityProductList()
    {
        try {

            $list = SellerCommodityProduct::where('user_id', auth()->id())
                ->with('getBrand', 'getCommodityProduct')
                ->orderBy('name', 'asc')
                ->paginate(getPaginate());
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
            'brand_id'      => 'required|numeric',
            'state'         => 'required',
            'city'          => 'required',
        ]);

        try {

            $commodity_product = CommodityProduct::find($request->product_id);
            if(!$commodity_product){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            $commodity_product_state = CommodityProductState::where('commodity_product_id', $request->product_id)->where('brand_id', $request->brand_id)->where('state', $request->state)->where('city', $request->city)->first();
            $state_prices = CommodityProductStatePrice::where('commodity_product_id', $request->product_id)->where('brand_id', $request->brand_id)->where('state', $request->state)->where('city', $request->city)->get();
            if(!$state_prices){
                return response([
                    'success'   => false,
                    'message'   => 'State price not set for this product.',
                ],400);
            }

            $checkData = SellerCommodityProductStatePrice::where('user_id', auth()->id())->where('commodity_product_id', $commodity_product->id)->where('brand_id', $request->brand_id)->where('state', $request->state)->where('city', $request->city)->exists();

            if($checkData){
                return response([
                    'success'   => false,
                    'message'   => 'This product combination already taken by you.',
                ],400);
            }

            $loading_address = [
                'address_line_one'   => $commodity_product_state->address_line_one,
                'address_line_two'   => $commodity_product_state->address_line_two,
                'pin_code'           => $commodity_product_state->pin_code,
                'city'               => $commodity_product_state->city,
                'state'              => $commodity_product_state->state,
            ];

            $data = new SellerCommodityProduct;
            $data->user_id              = auth()->id();
            $data->commodity_product_id = $commodity_product->id;
            $data->name                 = isset($request->name) && $request->name ? $request->name : $commodity_product->name;
            $data->slug                 = isset($request->name) && $request->name ? Str::slug($request->name) : $commodity_product->slug;
            $data->category_id          = $commodity_product->category_id;
            $data->sub_category_id      = $commodity_product->sub_category_id;
            $data->sub_sub_category_id  = $commodity_product->sub_sub_category_id;
            $data->brand_id             = $request->brand_id;
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
            $data->loading_address      = [$loading_address] ?? [];
            $data->video_url            = $commodity_product->video_url;
            $data->meta_title           = $commodity_product->meta_title;
            $data->meta_description     = $commodity_product->meta_description;
            $data->meta_image           = $commodity_product->meta_image;
            $data->status               = $commodity_product->status;
            $data->save();

            foreach ($state_prices as $state_price) {
                $data_price                                     = new SellerCommodityProductStatePrice;
                $data_price->user_id                            = auth()->id();
                $data_price->commodity_product_id               = $state_price->commodity_product_id;
                $data_price->commodity_product_variation_id     = $state_price->commodity_product_variation_id;
                $data_price->commodity_product_state_id         = $state_price->commodity_product_state_id;
                $data_price->brand_id                           = $state_price->brand_id;
                $data_price->seller_commodity_product_id        = $data->id;
                $data_price->state                              = $request->state;
                $data_price->city                               = $request->city;
                $data_price->value                              = $state_price->value;
                $data_price->price                              = $state_price->price;
                $data_price->is_selected                        = 1;
                $data_price->is_brand_selling                   = $state_price->is_brand_selling;
                $data_price->save();
            }

            $data_history               = new SellerCommodityProductHistory;
            $data_history->user_id      = auth()->id();
            $data_history->commodity_product_id = $commodity_product->id;
            $data_history->seller_commodity_product_id = $data->id;
            $data_history->seller_commodity_product_detail = $data;
            $data_history->save();

            foreach($data->loading_address as $loading_address){
                $home_product = HomeProduct::where('commodity_product_id', $commodity_product->id)->where('brand_id', $request->brand_id)->where('city', $loading_address['city'])->first();
                if($home_product && $home_product->base_price > $data->base_price){
                    $home_product->user_id      = auth()->id();
                    $home_product->seller_commodity_product_id = $data->id;
                    $home_product->base_price   = $data->base_price;
                    $home_product->save();
                }else{
                    $home_product = new HomeProduct;
                    $home_product->user_id      = auth()->id();
                    $home_product->commodity_product_id = $commodity_product->id;
                    $home_product->seller_commodity_product_id = $data->id;
                    $home_product->brand_id     = $request->brand_id;
                    $home_product->city         = $loading_address['city'];
                    $home_product->base_price   = $data->base_price;
                    $home_product->save();
                }
            }


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
            $data->loading_address = $request->loading_address ?? [];
            $data->save();

            return response([
                'success'   => true,
                'message'   => 'Product updated successfully.',
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
            $data->quantity         = $request->quantity;
            $data->price_validity   = Carbon::parse($request->price_validity)->format('Y-m-d h:i A');
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

            foreach($data->loading_address as $loading_address){
                $home_product = HomeProduct::where('commodity_product_id', $data->commodity_product_id)->where('brand_id', $data->brand_id)->where('city', $loading_address['city'])->first();
                if($home_product){
                    $base_price = $home_product->base_price ?? 0;
                    if($base_price > 0 && $base_price > $data->base_price){
                        $home_product->user_id      = auth()->id();
                        $home_product->seller_commodity_product_id = $data->id;
                        $home_product->base_price   = $data->base_price ?? 0;
                    }
                    $home_product->save();
                }else{
                    $home_product = new HomeProduct;
                    $home_product->user_id      = auth()->id();
                    $home_product->commodity_product_id = $data->commodity_product_id;
                    $home_product->seller_commodity_product_id = $data->id;
                    $home_product->brand_id     = $data->brand_id[0];
                    $home_product->city         = $loading_address['city'];
                    $home_product->base_price   = $data->base_price ?? 0;
                    $home_product->save();
                }
            }

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
            $data = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $id)->get();
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            return response([
                'success'   => true,
                'data'      => MyCommodityProductVariationResource::collection($data)
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
            'id'            => 'required|array',
            'price'         => 'required|array',
            'is_selected'   => 'required|array',
        ]);
        try {
            foreach ($request->id as $key => $id) {
                $data = SellerCommodityProductStatePrice::find($id);
                $data->price        = $request->price[$key];
                $data->is_selected  = $request->is_selected[$key];
                $data->save();
            }

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

    public function getVariationStock($id)
    {
        try {
            $data = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $id)->get();
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            return response([
                'success'   => true,
                'data'      => MyCommodityProductVariationStockResource::collection($data)
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function updateVariationStock(Request $request, $id)
    {
        $this->validate($request, [
            'id'            => 'required|array',
            'stock'         => 'required|array',
        ]);
        try {
            foreach ($request->id as $key => $id) {
                $data = SellerCommodityProductStatePrice::find($id);
                $data->stock        = $request->stock[$key];
                $data->save();
            }

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

    public function loadingPosition(Request $request, $id)
    {
        $this->validate($request, [
            'loading_position' =>'required',
        ]);
        try {

            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }
            $data->loading_position = $request->loading_position;
            $data->save();

            return response([
                'success'   => true,
                'message'   => 'Loading position updated successfully.',
            ],200);

        } catch (\Throwable $th) {

            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function getQuality($id)
    {
        try {
            $data = SellerCommodityProduct::with('getCommodityProduct')->find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }

            return response([
                'success'                   => true,
                'quality'                   => $data->getCommodityProduct->quality,
                'quality_price'             => $data->getCommodityProduct->quality_price,
                'selected_quality'          => $data->quality,
                'selected_quality_price'    => $data->quality_price,
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function updateQuality(Request $request, $id)
    {
        $this->validate($request, [
            'quality'       => 'required|array',
            'quality_price' => 'required|array',
        ]);
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }
            $data->quality = $request->quality;
            $data->quality_price = $request->quality_price;
            $data->save();

            return response([
                'success'   => true,
                'message'   => 'Product quality updated successfully.',
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $data = SellerCommodityProduct::find($id);
            if(!$data){
                return response([
                    'success'   => false,
                    'message'   => 'Product not found.',
                ],400);
            }
            $data->status = $data->status == 'active' ? 'inactive' : 'active';
            $data->save();

            return response([
                'success'   => true,
                'message'   => 'Product status updated successfully.',
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
