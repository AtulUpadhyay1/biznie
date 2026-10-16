<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\IngotPriceResource;
use App\Models\IngotPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    /**
     * The prices board: the latest rate for every product × market, with the
     * move against the previous rate.
     *
     * The page used to list the newest 50 rows, which were 50 ticks of the one
     * market that happened to be updated most (B17); a board shows each market
     * once, at its current rate.
     */
    public function index(Request $request): JsonResponse
    {
        $groups = IngotPrice::query()
            ->select('product', 'location')
            ->whereNotNull('location')
            ->when($request->string('product')->toString(), fn ($q, $p) => $q->where('product', $p))
            ->when($request->string('location')->toString(), fn ($q, $l) => $q->where('location', 'like', "%{$l}%"))
            ->groupBy('product', 'location')
            ->get();

        $rows = $groups->map(function ($group) {
            [$latest, $previous] = array_pad(
                IngotPrice::where('product', $group->product)
                    ->where('location', $group->location)
                    ->orderByDesc('date_time')
                    ->orderByDesc('id')
                    ->limit(2)
                    ->get()
                    ->all(),
                2,
                null
            );
            $latest->setAttribute('previous_price', $previous?->price);

            return $latest;
        })
            ->sortBy(fn ($row) => [$row->product, IngotPrice::marketLabel($row->location)])
            ->values();

        return response()->json([
            'success' => true,
            'data'    => IngotPriceResource::collection($rows),
            'meta'    => [
                'products' => $rows->pluck('product')->filter()->unique()->values(),
                'markets'  => $rows->count(),
                'total'    => $rows->count(),
            ],
        ]);
    }

    /**
     * Rate history for one product at one market, oldest first, for the chart
     * and the table under it.
     */
    public function history(int $id, Request $request): JsonResponse
    {
        $price = IngotPrice::findOrFail($id);
        $limit = min(max($request->integer('limit', 90), 5), 365);

        $history = IngotPrice::where('location', $price->location)
            ->where('product', $price->product)
            ->orderByDesc('date_time')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'data'    => IngotPriceResource::collection($history),
            'meta'    => [
                'product' => $price->product,
                'market'  => IngotPrice::marketLabel($price->location),
            ],
        ]);
    }
}
