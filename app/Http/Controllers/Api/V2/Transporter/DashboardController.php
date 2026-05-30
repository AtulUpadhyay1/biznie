<?php

namespace App\Http\Controllers\Api\V2\Transporter;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Transporter\TransporterEnquiryResource;
use App\Http\Resources\V2\Transporter\TransporterOrderResource;
use App\Models\CommodityProductOrder;
use App\Models\TransporterProductEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        $enquiryQ = TransporterProductEnquiry::where('user_id', $ownerId);
        $orderQ = CommodityProductOrder::where('transporter_user_id', $ownerId);

        $recentEnquiries = (clone $enquiryQ)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getProductEnquiry:id,unique_id,status',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $recentOrders = (clone $orderQ)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,unit_id',
                'getCommodityProduct.getUnit:id,name',
                'getCustomer:id,name,phone',
                'getSeller:id,name,phone',
                'getProductEnquiry:id,unique_id',
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
                    'enquiries_total'   => (clone $enquiryQ)->count(),
                    'enquiries_pending' => (clone $enquiryQ)->where('status', 'pending')->count(),
                    'enquiries_replied' => (clone $enquiryQ)->where('status', 'replied')->count(),
                    'trips_total'       => (clone $orderQ)->count(),
                    'trips_active'      => (clone $orderQ)->whereIn('status', ['confirm', 'loading', 'dispatched'])->count(),
                    'trips_complete'    => (clone $orderQ)->whereIn('status', ['delivered', 'complete'])->count(),
                ],
                'recent_enquiries' => TransporterEnquiryResource::collection($recentEnquiries)->resolve(),
                'recent_trips'     => TransporterOrderResource::collection($recentOrders)->resolve(),
            ],
        ]);
    }
}
