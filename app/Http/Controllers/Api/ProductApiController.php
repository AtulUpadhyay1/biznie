<?php

namespace App\Http\Controllers\Api;

use App\Models\HomeProduct;
use Illuminate\Http\Request;
use App\Models\BookmarkProduct;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\CommodityProduct;
use App\Http\Controllers\Controller;
use App\Models\SellerCommodityProduct;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\AllSellerCommodityProductResource;
use App\Http\Resources\SellerListByCommodityProductResource;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        try {

            $q = CommodityProduct::active()->select('id', 'name', 'thumbnail')->latest();
            if ($request->search) {
                $searchTerm = $request->search;
                $q->where('name', 'like', '%' . $searchTerm . '%');
            }
            $list = $q->paginate(getPaginate());
            foreach ($list as $data) {
                $data->thumbnail = imageUrl($data->thumbnail);
            }

            return $list;

            $q = HomeProduct::with('getUser', 'getCommodityProduct', 'getSellerCommodityProduct', 'getBrand');

            if ($request->search) {
                $searchTerm = $request->search;
                $q->where(function ($query) use ($searchTerm) {
                    $query->whereHas('getCommodityProduct', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%');
                    })->orWhereHas('getBrand', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%');
                    });
                });
            }

            $list = $q->groupBy('commodity_product_id')->orderBy('base_price', 'desc')->paginate(getPaginate());
            return ProductResource::collection($list);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function commodityProductList(Request $request)
    {
        try {

            $list = CommodityProduct::active()->latest()->get(['id', 'name', 'slug', 'thumbnail']);
            foreach ($list as $data) {
                $data->thumbnail = imageUrl($data->thumbnail);
            }
            return response([
                'success'        => true,
                'products_list'  => $list
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function show($id)
    {
        // try {
            // $data = HomeProduct::with('getCommodityProduct', 'getSellerCommodityProduct', 'getBrand', 'getSellerStatePrice')->findOrFail($id);
            $data = SellerCommodityProduct::with('getCommodityProduct', 'getBrand', 'getStatePrice')->findOrFail($id);
            $seller_product_list = SellerCommodityProduct::where('id', '!=', $id)->where('brand_id', $data->brand_id)->where('user_id', $data->user_id)->with('getBrand')->latest()->get();
            return response([
                'success'        => true,
                'products_data'  => new ProductDetailResource($data),
                'seller_product_list' => SellerListByCommodityProductResource::collection($seller_product_list)
                // 'seller_product_list' => AllSellerCommodityProductResource::collection($seller_product_list)
            ],200);

        // } catch (\Throwable $th) {
        //     return response([
        //         'success'   => false,
        //         'message'   => 'Something went wrong. Please try again.',
        //         'error'     => $th->getMessage()
        //     ],500);
        // }
    }

    public function allSellerCommodityProductList()
    {
        $list = SellerCommodityProduct::get();
        return response([
            'success'        => true,
            'products_data'  => AllSellerCommodityProductResource::collection($list)
        ],200);
    }

    public function sellerListByCommodityProduct(Request $request, $commodity_product_id)
    {
        $q = SellerCommodityProduct::where('commodity_product_id', $commodity_product_id)
            ->where('user_id', 1);

        $searchTerm = $request->search;
        if ($request->search) {
            $q->where(function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('getBrand', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $list = $q->with('getCommodityProduct')->paginate(getPaginate());
        return SellerListByCommodityProductResource::collection($list);
    }

    public function bookmarkList()
    {
        $product_ids = BookmarkProduct::where('user_id', auth()->id())->latest()->pluck('seller_commodity_product_id')->toArray();
        $list = SellerCommodityProduct::whereIn('id', $product_ids)
            ->with('getCommodityProduct')
            ->paginate(getPaginate());
        return SellerListByCommodityProductResource::collection($list);
    }

    public function bookmark(Request $request)
    {
        $request->validate([
            'commodity_product_id'          => 'required|integer',
            'seller_commodity_product_id'   => 'required|integer',
        ]);
        try {
            $bookmark = BookmarkProduct::where('commodity_product_id', $request->commodity_product_id)
                ->where('seller_commodity_product_id', $request->seller_commodity_product_id)
                ->where('user_id', auth()->id())
                ->first();
            if ($bookmark) {
                $bookmark->delete();
                return response([
                    'success'   => true,
                    'message'   => 'Product has been removed from bookmark.'
                ],200);
            } else {
                BookmarkProduct::create([
                    'seller_commodity_product_id' => $request->seller_commodity_product_id,
                    'commodity_product_id' => $request->commodity_product_id,
                    'user_id' => auth()->id()
                ]);
                return response([
                    'success'   => true,
                    'message'   => 'Product has been added to bookmark.'
                ],200);
            }
        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);
        }
    }

    public function businessCategoryProductList(Request $request)
    {
        $request->validate([
            'business_category_id' => 'nullable|exists:business_categories,id'
        ]);
        try {
            $business_categories = BusinessCategory::active()
                ->where('featured', 1)
                ->when($request->business_category_id, function($q) use ($request) {
                    $q->where('id', $request->business_category_id);
                })
                ->select('id', 'name')
                ->get();

            $response = [];

            foreach ($business_categories as $category) {
                $product_categories = ProductCategory::active()
                    ->where('business_category_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                $q = CommodityProduct::active()
                    ->whereIn('category_id', $product_categories)
                    ->select('id', 'name', 'thumbnail')
                    ->latest();

                if ($request->search) {
                    $searchTerm = $request->search;
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                }

                if ($request->business_category_id) {
                    $products = $q->get();
                } else {
                    $products = $q->take(8)->get();
                }

                foreach ($products as $product) {
                    $product->thumbnail = imageUrl($product->thumbnail);
                }

                $response[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'products' => $products
                ];
            }

            return response([
                'success'   => true,
                'message'   => 'Business category product list retrieved successfully.',
                'list'      => $response
            ], 200);

        } catch (\Throwable $th) {
            return response([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
