<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\CategoryResource;
use App\Http\Resources\V2\SubCategoryResource;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = ProductCategory::active()->latest();
        if ($request->boolean('featured')) {
            $q->where('featured', 1);
        }
        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%{$search}%");
        }
        $list = $q->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => CategoryResource::collection($list),
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
        $category = ProductCategory::active()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new CategoryResource($category),
        ]);
    }

    public function subCategories(Request $request, int $categoryId): JsonResponse
    {
        $list = ProductSubCategory::active()
            ->where('product_category_id', $categoryId)
            ->when($request->string('search')->toString(), function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => SubCategoryResource::collection($list),
        ]);
    }
}
