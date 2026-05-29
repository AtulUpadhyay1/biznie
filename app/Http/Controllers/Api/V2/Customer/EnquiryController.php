<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\EnquiryDetailResource;
use App\Http\Resources\V2\EnquiryResource;
use App\Models\ProductEnquiry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = ProductEnquiry::where('user_id', $userId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getMarkedSellerProductEnquiry:id,product_enquiries_id',
                'getCommodityProductOrder:id,product_enquiries_id,order_id',
            ])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => EnquiryResource::collection($list)->resolve(),
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
        $userId = $this->ownerId($request);

        $enquiry = ProductEnquiry::where('user_id', $userId)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getMarkedSellerProductEnquiry',
                'getCommodityProductOrder:id,product_enquiries_id,order_id',
            ])
            ->find($id);

        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new EnquiryDetailResource($enquiry),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'commodity_product_id' => ['required', 'integer', 'exists:commodity_products,id'],
            'brand_id'             => ['nullable', 'integer', 'exists:brands,id'],
            'origin_city'          => ['nullable', 'string', 'max:120'],
            'variation'            => ['nullable', 'array'],
            'billing_address'      => ['nullable'],
            'delivery_address'     => ['nullable'],
            'consignee_detail'     => ['nullable'],
            'quality'              => ['nullable'],
            'packaging_charge'     => ['nullable'],
            'purpose'              => ['nullable', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:2000'],
            'price'                => ['nullable'],
            'payment_mode'         => ['nullable', 'string', 'max:60'],
            'credit_day'           => ['nullable', 'integer'],
            'quantity'             => ['nullable', 'string', 'max:120'],
        ]);

        $user = $request->user();

        $description = $data['description'] ?? null;
        if (! empty($data['quantity'])) {
            $description = trim(($description ? $description."\n" : '').'Quantity: '.$data['quantity']);
        }

        $enquiry = new ProductEnquiry();
        $enquiry->user_id              = $user->is_staff ? $user->added_by : $user->id;
        $enquiry->commodity_product_id = $data['commodity_product_id'];
        $enquiry->brand_id             = $data['brand_id'] ?? null;
        $enquiry->unique_id            = 'PE-'.date('Ymd').'-'.rand(1111, 9999);
        $enquiry->origin_city          = $data['origin_city'] ?? null;
        $enquiry->variation            = $data['variation'] ?? [];
        $enquiry->billing_address      = $data['billing_address'] ?? null;
        $enquiry->delivery_address     = $data['delivery_address'] ?? null;
        $enquiry->consignee_detail     = $data['consignee_detail'] ?? null;
        $enquiry->quality              = $data['quality'] ?? null;
        $enquiry->packaging_charge     = $data['packaging_charge'] ?? null;
        $enquiry->purpose              = $data['purpose'] ?? null;
        $enquiry->description          = $description;
        $enquiry->price                = $data['price'] ?? null;
        $enquiry->payment_mode         = $data['payment_mode'] ?? null;
        $enquiry->credit_day           = $data['credit_day'] ?? null;
        $enquiry->status               = 'pending';
        $enquiry->history              = [['status' => 'pending', 'created_at' => Carbon::now()->toIso8601String()]];
        $enquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted successfully.',
            'data'    => new EnquiryDetailResource($enquiry->load('getBrand', 'getCommodityProduct.getCategory')),
        ], 201);
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
