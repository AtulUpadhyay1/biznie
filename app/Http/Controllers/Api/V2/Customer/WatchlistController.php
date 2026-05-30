<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\WatchlistResource;
use App\Models\BookmarkProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 20), 50);

        $list = BookmarkProduct::where('user_id', $userId)
            ->with([
                'getCommodityProduct:id,name,slug,thumbnail,unit_id,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getCommodityProduct.getUnit:id,name',
                'getSellerCommodityProduct',
                'getSellerCommodityProduct.getBrand:id,name',
                'getSellerCommodityProduct.getUser:id,name',
                'getSellerCommodityProduct.getUser.getUserDetail',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => WatchlistResource::collection($list)->resolve(),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'commodity_product_id'        => ['required', 'integer', 'exists:commodity_products,id'],
            'seller_commodity_product_id' => ['required', 'integer', 'exists:seller_commodity_products,id'],
        ]);

        $userId = $this->ownerId($request);

        // Toggle: if exists, remove it; else create.
        $existing = BookmarkProduct::where('user_id', $userId)
            ->where('commodity_product_id', $data['commodity_product_id'])
            ->where('seller_commodity_product_id', $data['seller_commodity_product_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success'    => true,
                'message'    => 'Removed from watchlist.',
                'bookmarked' => false,
            ]);
        }

        $bookmark = BookmarkProduct::create([
            'user_id'                     => $userId,
            'commodity_product_id'        => $data['commodity_product_id'],
            'seller_commodity_product_id' => $data['seller_commodity_product_id'],
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Added to watchlist.',
            'bookmarked' => true,
            'data'       => new WatchlistResource($bookmark),
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);

        $bookmark = BookmarkProduct::where('user_id', $userId)->find($id);

        if (! $bookmark) {
            return response()->json(['success' => false, 'message' => 'Bookmark not found.'], 404);
        }

        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from watchlist.',
        ]);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
