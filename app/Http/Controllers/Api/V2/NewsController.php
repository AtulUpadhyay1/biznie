<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\NewsResource;
use App\Models\MarketNews;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = MarketNews::active()->latest();
        if ($search = $request->string('search')->toString()) {
            $q->search($search);
        }
        $list = $q->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data'    => NewsResource::collection($list),
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
        $news = MarketNews::active()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new NewsResource($news),
        ]);
    }
}
