<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerOrderResource;
use App\Http\Resources\V2\Seller\SellerQuotationResource;
use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\DashboardStats;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardStats $stats): JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        $orderQ = CommodityProductOrder::where('seller_user_id', $ownerId);
        $productQ = SellerCommodityProduct::where('user_id', $ownerId);

        $recentOrders = (clone $orderQ)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,unit_id',
                'getCommodityProduct.getUnit:id,name',
                'getCustomer:id,name,phone,email',
                'getProductEnquiry:id,unique_id',
            ])
            ->latest()
            ->limit(5)
            ->get();

        // This seller's own open invitations. It used to list platform-wide
        // 'pending' RFQs the seller was never invited to, whose links 403'd.
        $recentRfqs = $stats->sellerNewRfqs($ownerId);
        $myBids = SellerProductEnquiry::where('user_id', $ownerId)
            ->whereIn('product_enquiries_id', $recentRfqs->pluck('id'))
            ->get()
            ->keyBy('product_enquiries_id');
        foreach ($recentRfqs as $rfq) {
            $rfq->setRelation('getMySellerProductEnquiry', $myBids->get($rfq->id));
        }
        $orderTiles = $stats->orderTiles(clone $orderQ);
        $rfqTiles = $stats->sellerRfqTiles($ownerId);

        return response()->json([
            'success' => true,
            'data'    => [
                'wallet' => [
                    'cash_balance'   => (float) ($user->cash_balance ?? 0),
                    'credit_balance' => (float) ($user->credit_balance ?? 0),
                ],
                'tiles' => [
                    'orders' => $orderTiles,
                    'rfqs'   => $rfqTiles,
                ],
                'counts' => [
                    'orders_total'    => $orderTiles['total'],
                    'orders_pending'  => $orderTiles['in_progress'],
                    'orders_complete' => $orderTiles['delivered'],
                    'rfqs_pending'    => $rfqTiles['new'],
                    'rfqs_replied'    => $rfqTiles['quoted'] + $rfqTiles['won'] + $rfqTiles['lost'],
                    // Live listings only — wizard drafts are not products yet.
                    'products_total'  => (clone $productQ)->where(fn ($q) => $q->whereNull('request_status')->orWhere('request_status', 'approved'))->count(),
                    'products_active' => (clone $productQ)->where('status', 'active')->count(),
                ],
                'recent_orders' => SellerOrderResource::collection($recentOrders)->resolve(),
                'recent_rfqs'   => SellerQuotationResource::collection($recentRfqs)->resolve(),
            ],
        ]);
    }
}
