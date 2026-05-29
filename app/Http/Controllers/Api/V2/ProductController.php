<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProductResource;
use App\Models\CommodityProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = CommodityProduct::active()
            ->with(['getCategory', 'getSubCategory', 'getUnit'])
            ->latest();

        if ($search = $request->string('search')->toString()) {
            $q->search($search);
        }
        if ($categoryId = $request->integer('category_id')) {
            $q->where('category_id', $categoryId);
        }
        if ($subCategoryId = $request->integer('sub_category_id')) {
            $q->where('sub_category_id', $subCategoryId);
        }

        $list = $q->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($list),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = CommodityProduct::active()
            ->with(['getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product),
        ]);
    }
}
