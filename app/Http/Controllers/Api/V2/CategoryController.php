<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\CategoryResource;
use App\Models\BusinessCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = BusinessCategory::active()->latest();
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
        $category = BusinessCategory::active()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new CategoryResource($category),
        ]);
    }
}
