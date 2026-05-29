<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\OrderDetailResource;
use App\Http\Resources\V2\OrderResource;
use App\Models\CommodityProductOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = CommodityProductOrder::where('customer_user_id', $request->user()->id)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,unit_id,category_id',
                'getCommodityProduct.getUnit:id,name',
                'getProductEnquiry:id,unique_id',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($list)->resolve(),
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
        $order = CommodityProductOrder::where('customer_user_id', $request->user()->id)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct',
                'getCommodityProduct.getUnit:id,name',
                'getCommodityProduct.getCategory:id,name',
                'getProductEnquiry:id,unique_id',
                'getDrivers',
            ])
            ->find($id);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new OrderDetailResource($order),
        ]);
    }
}
