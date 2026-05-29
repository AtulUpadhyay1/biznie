<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\IngotPriceResource;
use App\Models\IngotPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = IngotPrice::query()->latest();
        if ($location = $request->string('location')->toString()) {
            $q->where('location', 'like', "%{$location}%");
        }
        $list = $q->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'data'    => IngotPriceResource::collection($list),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function history(int $id): JsonResponse
    {
        $price = IngotPrice::findOrFail($id);
        $history = IngotPrice::where('location', $price->location)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => IngotPriceResource::collection($history),
        ]);
    }
}
