<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\OrderResource;
use App\Http\Resources\V2\EnquiryResource;
use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\DashboardStats;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardStats $stats): JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        $orderQ   = CommodityProductOrder::where('customer_user_id', $ownerId);
        $enquiryQ = ProductEnquiry::where('user_id', $ownerId);

        return response()->json([
            'success' => true,
            'data'    => [
                'wallet' => [
                    'cash_balance'   => (float) ($user->cash_balance ?? 0),
                    'credit_balance' => (float) ($user->credit_balance ?? 0),
                ],
                // Tiles from the canonical status model; each set adds up to
                // its total (B25).
                'tiles' => [
                    'orders' => $orderTiles = $stats->orderTiles(clone $orderQ),
                    'rfqs'   => $rfqTiles = $stats->buyerRfqTiles(clone $enquiryQ),
                ],
                // Kept for older clients.
                'counts' => [
                    'orders_total'    => $orderTiles['total'],
                    'orders_pending'  => $orderTiles['in_progress'],
                    'orders_complete' => $orderTiles['delivered'],
                    'enquiries_total'   => $rfqTiles['total'],
                    'enquiries_pending' => $rfqTiles['open'],
                ],
                'recent_orders'    => OrderResource::collection(
                    (clone $orderQ)
                        ->with([
                            'getBrand:id,name',
                            'getCommodityProduct:id,name,slug,thumbnail,unit_id',
                            'getCommodityProduct.getUnit:id,name',
                            'getProductEnquiry:id,unique_id',
                        ])
                        ->latest()
                        ->limit(5)
                        ->get()
                )->resolve(),
                'recent_enquiries' => EnquiryResource::collection(
                    (clone $enquiryQ)
                        ->withQuoteCount()
                        ->with([
                            'getBrand:id,name',
                            'getCommodityProduct:id,name,slug,thumbnail,category_id',
                            'getCommodityProduct.getCategory:id,name',
                            'getMarkedSellerProductEnquiry:id,product_enquiries_id',
                            'getCommodityProductOrder:id,product_enquiries_id,order_id',
                        ])
                        ->latest()
                        ->limit(5)
                        ->get()
                )->resolve(),
            ],
        ]);
    }
}
