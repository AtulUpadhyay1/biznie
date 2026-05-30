<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\BiddingResource;
use App\Http\Resources\V2\EnquiryDetailResource;
use App\Http\Resources\V2\EnquiryResource;
use App\Models\CashWalletTransaction;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderLedger;
use App\Models\CreditWalletTransaction;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function update(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);
        $enquiry = ProductEnquiry::where('user_id', $userId)->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }
        if (strtolower((string) $enquiry->status) !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Enquiry can no longer be edited.'], 422);
        }

        $data = $request->validate([
            'origin_city'      => ['nullable', 'string', 'max:120'],
            'variation'        => ['nullable', 'array'],
            'billing_address'  => ['nullable'],
            'delivery_address' => ['nullable'],
            'consignee_detail' => ['nullable'],
            'quality'          => ['nullable'],
            'packaging_charge' => ['nullable'],
            'purpose'          => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'price'            => ['nullable'],
            'payment_mode'     => ['nullable', 'string', 'max:60'],
            'credit_day'       => ['nullable', 'integer'],
        ]);

        foreach ($data as $key => $value) {
            $enquiry->{$key} = $value;
        }
        $enquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Enquiry updated successfully.',
            'data'    => new EnquiryDetailResource($enquiry->load('getBrand', 'getCommodityProduct.getCategory')),
        ]);
    }

    public function biddingList(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);
        $enquiry = ProductEnquiry::where('user_id', $userId)->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $bids = SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)
            ->whereIn('status', ['replied', 'ordered'])
            ->with('getUser:id,name,company_name,city,state')
            ->orderBy('base_price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => BiddingResource::collection($bids)->resolve(),
        ]);
    }

    public function markSeller(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);
        $enquiry = ProductEnquiry::where('user_id', $userId)->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $data = $request->validate([
            'seller_quotation_id' => ['required', 'integer'],
        ]);

        $bid = SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)
            ->where('id', $data['seller_quotation_id'])
            ->first();
        if (! $bid) {
            return response()->json(['success' => false, 'message' => 'Quotation not found.'], 404);
        }

        DB::transaction(function () use ($enquiry, $bid) {
            SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)->update(['is_mark' => 0]);
            $bid->is_mark = 1;
            $bid->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation marked as preferred.',
        ]);
    }

    public function acceptQuotation(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);
        $enquiry = ProductEnquiry::where('user_id', $userId)
            ->with('getMarkedSellerProductEnquiry')
            ->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $data = $request->validate([
            'seller_quotation_id' => ['nullable', 'integer'],
            'token_amount'        => ['required', 'numeric', 'min:1'],
            'total_amount'        => ['required', 'numeric', 'min:1'],
            'selected_wallet'     => ['required', 'in:cash_balance,credit_balance'],
        ]);

        $bid = null;
        if (! empty($data['seller_quotation_id'])) {
            $bid = SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)
                ->where('id', $data['seller_quotation_id'])
                ->first();
        } else {
            $bid = $enquiry->getMarkedSellerProductEnquiry;
        }

        if (! $bid) {
            return response()->json(['success' => false, 'message' => 'No quotation selected to accept.'], 422);
        }

        $customer = $request->user();
        $owner = $customer->is_staff ? $customer->added_by : $customer->id;
        $balance = $data['selected_wallet'] === 'cash_balance'
            ? (float) ($customer->cash_balance ?? 0)
            : (float) ($customer->credit_balance ?? 0);

        if ($data['token_amount'] > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance to confirm this order.',
            ], 422);
        }

        $order = DB::transaction(function () use ($enquiry, $bid, $data, $customer, $owner) {
            // Mark this quotation as accepted (and unmark others)
            SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)->update(['is_mark' => 0]);
            $bid->is_mark = 1;
            $bid->status = 'ordered';
            $bidHistory = $bid->history ?? [];
            $bidHistory[] = ['status' => 'Ordered', 'created_at' => Carbon::now()->toIso8601String()];
            $bid->history = $bidHistory;
            $bid->save();

            $enquiry->status = 'ordered';
            $enquiryHistory = $enquiry->history ?? [];
            $enquiryHistory[] = ['status' => 'Ordered', 'created_at' => Carbon::now()->toIso8601String()];
            $enquiry->history = $enquiryHistory;
            $enquiry->save();

            $order = new CommodityProductOrder();
            $order->seller_user_id              = $bid->user_id;
            $order->customer_user_id            = $owner;
            $order->product_enquiries_id        = $bid->product_enquiries_id;
            $order->seller_product_enquiries_id = $bid->id;
            $order->commodity_product_id        = $bid->commodity_product_id;
            $order->brand_id                    = $bid->brand_id;
            $order->unique_id                   = $bid->unique_id;
            $order->order_id                    = 'OID-'.date('Ymd').'-'.rand(1111, 9999);
            $order->origin_city                 = $bid->origin_city;
            $order->value                       = $bid->value;
            $order->billing_address             = $bid->billing_address;
            $order->delivery_address            = $bid->delivery_address;
            $order->purpose                     = $bid->purpose;
            $order->description                 = $bid->description;
            $order->message                     = $bid->message;
            $order->price                       = $bid->price;
            $order->base_price                  = $bid->base_price;
            $order->token_amount                = $data['token_amount'];
            $order->transport_price             = $bid->transport_price;
            $order->commission                  = $bid->commission;
            $order->total_amount                = $data['total_amount'];
            $order->paid_amount                 = $data['token_amount'];
            $order->due_amount                  = $data['total_amount'] - $data['token_amount'];
            $order->loading_address             = $bid->loading_address;
            $order->delivery_by                 = $bid->delivery_by;
            $order->consignee_detail            = $bid->consignee_detail;
            $order->status                      = 'pending';
            $order->history                     = [['status' => 'Order Confirmed By Customer', 'created_at' => Carbon::now()->toIso8601String()]];
            $order->save();

            if ($data['token_amount'] > 0) {
                $ledger = new CommodityProductOrderLedger();
                $ledger->order_id          = $order->id;
                $ledger->transaction_id    = 'TNX-'.time().'-'.rand(1111, 9999);
                $ledger->type              = 'credit';
                $ledger->amount            = $data['token_amount'];
                $ledger->remaining_balance = $data['total_amount'] - $data['token_amount'];
                $ledger->description       = 'Token amount paid for Order '.$order->order_id;
                $ledger->save();
            }

            if ($data['selected_wallet'] === 'cash_balance') {
                $customer->cash_balance = (float) $customer->cash_balance - $data['token_amount'];
                $customer->save();

                $tx = new CashWalletTransaction();
                $tx->user_id                    = $customer->id;
                $tx->commodity_product_order_id = $order->id;
                $tx->amount                     = $data['token_amount'];
                $tx->description                = 'Amount debited for Order '.$order->order_id;
                $tx->mode                       = 'online';
                $tx->status                     = 'debit';
                $tx->transaction_status         = 'Amount debited';
                $tx->save();
                $tx->transaction_id = 'TX-'.date('Ymd').$tx->id.$customer->id.rand(111, 999);
                $tx->save();
            } else {
                $customer->credit_balance = (float) $customer->credit_balance - $data['token_amount'];
                $customer->save();

                $tx = new CreditWalletTransaction();
                $tx->user_id                    = $customer->id;
                $tx->commodity_product_order_id = $order->id;
                $tx->amount                     = $data['token_amount'];
                $tx->description                = 'Amount debited for Order '.$order->order_id;
                $tx->mode                       = 'online';
                $tx->status                     = 'debit';
                $tx->transaction_status         = 'Amount debited';
                $tx->save();
                $tx->transaction_id = 'TX-'.date('Ymd').$tx->id.$customer->id.rand(111, 999);
                $tx->save();
            }

            return $order;
        });

        return response()->json([
            'success'  => true,
            'message'  => 'Quotation accepted and order placed.',
            'order_id' => $order->order_id,
            'data'     => [
                'id'          => $order->id,
                'order_id'    => $order->order_id,
                'total'       => (float) $order->total_amount,
                'paid'        => (float) $order->paid_amount,
                'due'         => (float) $order->due_amount,
            ],
        ], 201);
    }
}
