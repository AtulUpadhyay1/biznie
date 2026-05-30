<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\OrderLedgerResource;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderLedger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderLedgerController extends Controller
{
    public function index(Request $request, int $orderId): JsonResponse
    {
        $userId = $this->ownerId($request);

        $order = CommodityProductOrder::where('customer_user_id', $userId)->find($orderId);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $ledger = CommodityProductOrderLedger::where('order_id', $order->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => OrderLedgerResource::collection($ledger)->resolve(),
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

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
