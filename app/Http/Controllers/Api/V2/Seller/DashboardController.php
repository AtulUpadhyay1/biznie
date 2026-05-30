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

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        $orderQ = CommodityProductOrder::where('seller_user_id', $ownerId);
        $repliedIds = SellerProductEnquiry::where('user_id', $ownerId)
            ->whereNotNull('product_enquiries_id')
            ->pluck('product_enquiries_id');

        $rfqRepliedCount = $repliedIds->count();
        $rfqPendingCount = ProductEnquiry::where('status', 'pending')
            ->whereNotIn('id', $repliedIds)
            ->count();

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

        $recentRfqs = ProductEnquiry::where('status', 'pending')
            ->whereNotIn('id', $repliedIds)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getUser:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'wallet' => [
                    'cash_balance'   => (float) ($user->cash_balance ?? 0),
                    'credit_balance' => (float) ($user->credit_balance ?? 0),
                ],
                'counts' => [
                    'orders_total'    => (clone $orderQ)->count(),
                    'orders_pending'  => (clone $orderQ)->where('status', 'pending')->count(),
                    'orders_complete' => (clone $orderQ)->where('status', 'complete')->count(),
                    'rfqs_pending'    => $rfqPendingCount,
                    'rfqs_replied'    => $rfqRepliedCount,
                    'products_total'  => (clone $productQ)->count(),
                    'products_active' => (clone $productQ)->where('status', 'active')->count(),
                ],
                'recent_orders' => SellerOrderResource::collection($recentOrders)->resolve(),
                'recent_rfqs'   => SellerQuotationResource::collection($recentRfqs)->resolve(),
            ],
        ]);
    }
}
