<?php

namespace App\Http\Controllers\Api\V2\Transporter;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Transporter\TransporterOrderDetailResource;
use App\Http\Resources\V2\Transporter\TransporterOrderResource;
use App\Models\CommodityProductOrder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = CommodityProductOrder::where('transporter_user_id', $ownerId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,unit_id',
                'getCommodityProduct.getUnit:id,name',
                'getCustomer:id,name,phone',
                'getSeller:id,name,phone',
                'getProductEnquiry:id,unique_id',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => TransporterOrderResource::collection($list)->resolve(),
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
        $order = CommodityProductOrder::where('transporter_user_id', $ownerId)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct',
                'getCommodityProduct.getCategory:id,name',
                'getCommodityProduct.getUnit:id,name',
                'getCustomer:id,name,phone,email',
                'getSeller:id,name,phone,email',
                'getProductEnquiry:id,unique_id',
            ])
            ->find($id);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Trip not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new TransporterOrderDetailResource($order),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:loading,dispatched,delivered,complete'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $ownerId = $this->ownerId($request);
        $order = CommodityProductOrder::where('transporter_user_id', $ownerId)->find($id);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Trip not found.'], 404);
        }

        $order->status = $data['status'];
        $history = is_array($order->history) ? $order->history : [];
        $history[] = [
            'status'     => $data['status'],
            'note'       => $data['note'] ?? null,
            'by'         => $request->user()->id,
            'role'       => 'transporter',
            'created_at' => Carbon::now()->toIso8601String(),
        ];
        $order->history = $history;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Trip status updated.',
            'data'    => new TransporterOrderDetailResource($order->fresh([
                'getBrand', 'getCommodityProduct.getCategory', 'getCommodityProduct.getUnit',
                'getCustomer', 'getSeller', 'getProductEnquiry',
            ])),
        ]);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
