<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\BiddingResource;
use App\Http\Resources\V2\Seller\SellerQuotationDetailResource;
use App\Http\Resources\V2\Seller\SellerQuotationResource;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);
        $tab = $request->get('tab', 'received'); // received | replied

        $repliedIds = SellerProductEnquiry::where('user_id', $ownerId)
            ->whereNotNull('product_enquiries_id')
            ->pluck('product_enquiries_id');

        $query = ProductEnquiry::query()
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getUser:id,name',
            ]);

        if ($tab === 'replied') {
            $query->whereIn('id', $repliedIds);
        } else {
            $query->where('status', 'pending')->whereNotIn('id', $repliedIds);
        }

        $list = $query->latest()->paginate($perPage);

        $list->getCollection()->transform(function (ProductEnquiry $enq) use ($ownerId) {
            $reply = SellerProductEnquiry::where('product_enquiries_id', $enq->id)
                ->where('user_id', $ownerId)
                ->first();
            $enq->setRelation('getMySellerProductEnquiry', $reply);
            return $enq;
        });

        return response()->json([
            'success' => true,
            'data'    => SellerQuotationResource::collection($list)->resolve(),
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
        $enquiry = ProductEnquiry::with([
            'getBrand:id,name',
            'getCommodityProduct:id,name,slug,thumbnail,category_id',
            'getCommodityProduct.getCategory:id,name',
            'getUser:id,name',
        ])->find($id);

        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $reply = SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)
            ->where('user_id', $ownerId)
            ->first();
        $enquiry->setRelation('getMySellerProductEnquiry', $reply);

        return response()->json([
            'success' => true,
            'data'    => new SellerQuotationDetailResource($enquiry),
        ]);
    }

    public function biddingList(Request $request, int $id): JsonResponse
    {
        $list = SellerProductEnquiry::where('product_enquiries_id', $id)
            ->with('getUser:id,name')
            ->orderBy('base_price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => BiddingResource::collection($list)->resolve(),
        ]);
    }

    public function respond(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'base_price'      => ['required', 'numeric', 'min:0'],
            'transport_price' => ['nullable', 'numeric', 'min:0'],
            'commission'      => ['nullable', 'numeric', 'min:0'],
            'commission_type' => ['nullable', 'string', 'in:percent,flat'],
            'price'           => ['nullable'],
            'variation'       => ['nullable'],
            'note'            => ['nullable', 'string', 'max:500'],
        ]);

        $enquiry = ProductEnquiry::find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }
        if ($enquiry->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Enquiry is no longer accepting quotations.'], 422);
        }

        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        $reply = SellerProductEnquiry::firstOrNew([
            'product_enquiries_id' => $enquiry->id,
            'user_id'              => $ownerId,
        ]);

        $reply->commodity_product_id = $enquiry->commodity_product_id;
        $reply->brand_id             = $enquiry->brand_id;
        $reply->customer_user_id     = $enquiry->user_id;
        $reply->base_price           = $data['base_price'];
        $reply->transport_price      = $data['transport_price'] ?? 0;
        $reply->commission           = $data['commission'] ?? null;
        $reply->commission_type      = $data['commission_type'] ?? null;
        $reply->price                = $data['price'] ?? $reply->price;
        if (array_key_exists('variation', $data)) {
            $reply->variation = $data['variation'];
        }
        $reply->status = 'replied';

        $history = is_array($reply->history) ? $reply->history : [];
        $history[] = [
            'status'     => 'replied',
            'note'       => $data['note'] ?? null,
            'by'         => $user->id,
            'created_at' => Carbon::now()->toIso8601String(),
        ];
        $reply->history = $history;
        $reply->save();

        $enquiry->setRelation('getMySellerProductEnquiry', $reply);

        return response()->json([
            'success' => true,
            'message' => 'Quotation submitted successfully.',
            'data'    => new SellerQuotationDetailResource(
                $enquiry->load(['getBrand', 'getCommodityProduct.getCategory', 'getUser'])
            ),
        ], 201);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
