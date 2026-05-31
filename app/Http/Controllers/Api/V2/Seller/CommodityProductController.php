<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerCommodityProductResource;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommodityProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = SellerCommodityProduct::where('user_id', $ownerId)
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', (int) $c))
            ->when($request->brand_id, fn ($q, $b) => $q->where('brand_id', (int) $b))
            ->when($request->state, fn ($q, $s) => $q->where('state', $s))
            ->when($request->city, fn ($q, $c) => $q->where('city', $c))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->with([
                'getCategory:id,name',
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail',
                'getUnit:id,name',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => SellerCommodityProductResource::collection($list)->resolve(),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $item = SellerCommodityProduct::where('user_id', $ownerId)
            ->with([
                'getCategory:id,name',
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail',
                'getUnit:id,name',
            ])
            ->find($id);

        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Commodity product not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new SellerCommodityProductResource($item),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $ownerId = $this->ownerId($request);
        $item = SellerCommodityProduct::where('user_id', $ownerId)->find($id);

        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Commodity product not found.'], 404);
        }

        $item->status = $data['status'];
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated.',
            'data'    => new SellerCommodityProductResource($item->fresh([
                'getCategory', 'getBrand', 'getCommodityProduct', 'getUnit',
            ])),
        ]);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
