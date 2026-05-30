<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerProductResource;
use App\Models\SellerCommodityProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = SellerCommodityProduct::where('user_id', $ownerId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->with([
                'getCommodityProduct:id,name',
                'getBrand:id,name',
                'getCategory:id,name',
                'getUnit:id,name',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => SellerProductResource::collection($list)->resolve(),
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
        $product = SellerCommodityProduct::where('user_id', $ownerId)
            ->with([
                'getCommodityProduct:id,name',
                'getBrand:id,name',
                'getCategory:id,name',
                'getUnit:id,name',
            ])
            ->find($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new SellerProductResource($product),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $ownerId = $this->ownerId($request);
        $product = SellerCommodityProduct::where('user_id', $ownerId)->find($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $product->status = $data['status'];
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product status updated.',
            'data'    => new SellerProductResource(
                $product->fresh(['getCommodityProduct', 'getBrand', 'getCategory', 'getUnit'])
            ),
        ]);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
