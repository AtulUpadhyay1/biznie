<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\OrderLedgerResource;
use App\Http\Resources\V2\Seller\SellerOrderDetailResource;
use App\Http\Resources\V2\Seller\SellerOrderResource;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductSellerOrderLedger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = CommodityProductOrder::where('seller_user_id', $ownerId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,unit_id',
                'getCommodityProduct.getUnit:id,name',
                'getCustomer:id,name,phone,email',
                'getProductEnquiry:id,unique_id',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => SellerOrderResource::collection($list)->resolve(),
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
        $order = CommodityProductOrder::where('seller_user_id', $ownerId)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct',
                'getCommodityProduct.getUnit:id,name',
                'getCommodityProduct.getCategory:id,name',
                'getCustomer:id,name,phone,email',
                'getProductEnquiry:id,unique_id',
                'getDrivers',
            ])
            ->find($id);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new SellerOrderDetailResource($order),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:confirm,loading,dispatched,delivered,complete,cancel'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $ownerId = $this->ownerId($request);
        $order = CommodityProductOrder::where('seller_user_id', $ownerId)->find($id);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $order->status = $data['status'];
        $history = is_array($order->history) ? $order->history : [];
        $history[] = [
            'status'     => $data['status'],
            'note'       => $data['note'] ?? null,
            'by'         => $request->user()->id,
            'created_at' => Carbon::now()->toIso8601String(),
        ];
        $order->history = $history;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
            'data'    => new SellerOrderDetailResource($order->fresh([
                'getBrand', 'getCommodityProduct.getUnit', 'getCommodityProduct.getCategory',
                'getCustomer', 'getProductEnquiry',
            ])),
        ]);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }

    public function ledger(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $order = CommodityProductOrder::where('seller_user_id', $ownerId)->find($id);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $entries = CommodityProductSellerOrderLedger::where('order_id', $order->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => OrderLedgerResource::collection($entries)->resolve(),
            'order'   => [
                'id'           => $order->id,
                'order_id'     => $order->order_id,
                'unique_id'    => $order->unique_id,
                'total_amount' => (float) ($order->total_amount ?? 0),
                'paid_amount'  => (float) ($order->paid_amount ?? 0),
                'due_amount'   => (float) ($order->due_amount ?? 0),
                'final_amount' => (float) ($order->final_amount ?? 0),
            ],
        ]);
    }
}
