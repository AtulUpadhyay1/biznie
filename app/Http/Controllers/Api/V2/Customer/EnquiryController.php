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
use App\Models\ProductUnit;
use App\Models\SellerProductEnquiry;
use App\Services\Rfq\LiveBiddingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnquiryController extends Controller
{
    public function __construct(private readonly LiveBiddingService $bidding)
    {
    }

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

    /**
     * Raises an RFQ and immediately opens bidding on it.
     *
     * Two things changed here. Quantity, unit, size and delivery city used to
     * be squashed into `description` as free text ("Quantity: 30 Tons"), which
     * left nothing to price freight against and nothing to rank on; they are
     * columns now. And the RFQ is fanned out to matching sellers in the same
     * request rather than waiting for an admin to hand-pick them, which is what
     * turns the wait from hours into the countdown the buyer is watching.
     *
     * Dispatch failures are swallowed on purpose: the buyer's RFQ is saved
     * either way, and the admin screen can re-notify.
     */
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
            'quantity'             => ['nullable', 'numeric', 'min:0.001'],
            'unit_id'              => ['nullable', 'integer', 'exists:product_units,id'],
            'unit_label'           => ['nullable', 'string', 'max:30'],
            'size_label'           => ['nullable', 'string', 'max:150'],
            'delivery_city'        => ['nullable', 'string', 'max:120'],
            'delivery_state'       => ['nullable', 'string', 'max:120'],
            'required_by'          => ['nullable', 'string', 'max:120'],
        ]);

        $user = $request->user();

        $enquiry = new ProductEnquiry();
        $enquiry->user_id              = $user->is_staff ? $user->added_by : $user->id;
        $enquiry->commodity_product_id = $data['commodity_product_id'];
        $enquiry->brand_id             = $data['brand_id'] ?? null;
        $enquiry->unique_id            = $this->nextUniqueId();
        $enquiry->origin_city          = $data['origin_city'] ?? null;
        $enquiry->variation            = $data['variation'] ?? [];
        $enquiry->billing_address      = $data['billing_address'] ?? null;
        $enquiry->delivery_address     = $data['delivery_address'] ?? null;
        $enquiry->consignee_detail     = $data['consignee_detail'] ?? null;
        $enquiry->quality              = $data['quality'] ?? null;
        $enquiry->packaging_charge     = $data['packaging_charge'] ?? null;
        $enquiry->purpose              = $data['purpose'] ?? null;
        $enquiry->description          = $data['description'] ?? null;
        $enquiry->price                = $data['price'] ?? null;
        $enquiry->payment_mode         = $data['payment_mode'] ?? null;
        $enquiry->credit_day           = $data['credit_day'] ?? null;
        $enquiry->quantity             = $data['quantity'] ?? null;
        $enquiry->unit_id              = $data['unit_id'] ?? null;
        $enquiry->unit_label           = $this->unitLabel($data);
        $enquiry->size_label           = $data['size_label'] ?? null;
        $enquiry->required_by          = $data['required_by'] ?? null;
        $enquiry->status               = 'pending';
        $enquiry->history              = [['status' => 'pending', 'created_at' => Carbon::now()->toIso8601String()]];

        // Sellers are only ever shown the city, so it is resolved once here
        // rather than dug out of the address blob on every read.
        [$city, $state] = $this->resolveDeliveryPlace($data);
        $enquiry->delivery_city  = $city;
        $enquiry->delivery_state = $state;

        $enquiry->save();

        try {
            $this->bidding->openBidding($enquiry);
            $this->bidding->dispatchToSellers($enquiry);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted successfully.',
            'data'    => new EnquiryDetailResource(
                $enquiry->fresh()->load('getBrand', 'getCommodityProduct.getCategory')
            ),
        ], 201);
    }

    /**
     * Collision-resistant RFQ reference.
     *
     * The old `rand(1111, 9999)` suffix gave a real collision chance per day,
     * and `unique_id` is what the seller app, the bid rows and every
     * notification key on - two RFQs sharing one is not a cosmetic problem. A
     * daily sequence cannot repeat.
     */
    private function nextUniqueId(): string
    {
        $prefix = 'BZN-RFQ-' . date('ymd') . '-';

        $last = ProductEnquiry::withTrashed()
            ->where('unique_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('unique_id');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /** Falls back to the unit's short name when the client sent no label. */
    private function unitLabel(array $data): ?string
    {
        if (! empty($data['unit_label'])) {
            return $data['unit_label'];
        }

        if (! empty($data['unit_id'])) {
            return ProductUnit::whereKey($data['unit_id'])->value('short_name');
        }

        return null;
    }

    /**
     * Where the load has to land, as a (city, state) pair.
     *
     * An explicit `delivery_city` from the Rate Finder wins; otherwise it comes
     * out of whichever address the detailed form filled in, so both entry
     * points end up with a city that freight can be priced against.
     */
    private function resolveDeliveryPlace(array $data): array
    {
        if (! empty($data['delivery_city'])) {
            return [$data['delivery_city'], $data['delivery_state'] ?? null];
        }

        foreach (['delivery_address', 'consignee_detail', 'billing_address'] as $key) {
            $address = $data[$key] ?? null;
            if (is_array($address)) {
                $address = array_is_list($address) ? ($address[0] ?? null) : $address;
            }
            if (is_array($address) && ! empty($address['city'])) {
                return [$address['city'], $address['state'] ?? null];
            }
        }

        return [null, null];
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

    /**
     * Every offer on the buyer's RFQ, cheapest doorstep price first.
     *
     * Sellers are not named. The auction is blind in both directions - the
     * seller bids against "Seller A", and the buyer picks between "Offer A" and
     * "Offer B" on price, breakup and loading city. Identity is revealed once
     * the order is confirmed and the two sides actually have to transact; until
     * then, naming them would let either side go around the platform.
     *
     * `getUser` is deliberately not loaded, so `BiddingResource` omits the
     * seller block entirely rather than relying on the frontend to hide it.
     * (The old select also named `company_name`, `city` and `state` as columns
     * on `users`, where none of them exist - the query threw before returning a
     * single bid.)
     */
    public function biddingList(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);
        $enquiry = ProductEnquiry::where('user_id', $userId)->find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $bids = SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)
            ->whereIn('status', ['replied', 'ordered', 'Mark For Sell'])
            // Bids priced before the auction existed have no `for_price`, so
            // they fall back to `base_price` rather than sorting to the top.
            ->orderByRaw('COALESCE(for_price, base_price) ASC')
            ->orderBy('id', 'asc')
            ->get();

        $alphabet = range('A', 'Z');
        $bids->values()->each(function (SellerProductEnquiry $bid, int $i) use ($alphabet) {
            $bid->rank         = $i + 1;
            $bid->seller_label = 'Offer ' . ($alphabet[$i] ?? '#' . ($i + 1));
        });

        return response()->json([
            'success' => true,
            'data'    => BiddingResource::collection($bids)->resolve(),
        ]);
    }

    /**
     * Everything the "Finding Best Price" / "Best Price Found" panel renders.
     *
     * Deliberately identity-free: the buyer is told the winning price and its
     * breakup, never who quoted it. That is the whole point of running the
     * auction blind - the buyer cannot go around the platform, and the seller
     * cannot be leaned on.
     */
    public function live(Request $request, int $id): JsonResponse
    {
        $userId = $this->ownerId($request);

        $enquiry = ProductEnquiry::where('user_id', $userId)
            ->with(['getBrand:id,name', 'getCommodityProduct:id,name,slug,thumbnail'])
            ->find($id);

        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $this->bidding->closeIfExpired($enquiry);

        $stats = $this->bidding->stats($enquiry);
        $best  = $this->bidding->bestBid($enquiry);
        $qty   = $enquiry->quantity !== null ? (float) $enquiry->quantity : null;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $enquiry->id,
                'unique_id' => $enquiry->unique_id,
                'status'    => $enquiry->status,
                'stage'     => $this->bidding->stage($enquiry),
                'bidding'   => [
                    'status'       => $enquiry->bidding_status,
                    'is_live'      => $this->bidding->isLive($enquiry),
                    'started_at'   => optional($enquiry->bidding_started_at)->toIso8601String(),
                    'ends_at'      => optional($enquiry->bidding_ends_at)->toIso8601String(),
                    'seconds_left' => $this->bidding->secondsLeft($enquiry),
                ],
                'sellers_invited' => $stats['invited'],
                'responses'       => $stats['responses'],
                'best_offer'      => $best ? [
                    'quotation_id'    => $best->id,
                    'ex_works_price'  => (float) $best->ex_works_price,
                    'freight_charges' => (float) $best->freight_charges,
                    'other_charges'   => (float) $best->other_charges,
                    'for_price'       => (float) $best->for_price,
                    'ex_works_city'   => $best->ex_works_city,
                    'total_amount'    => $qty ? round((float) $best->for_price * $qty, 2) : null,
                    'updated_at'      => optional($best->price_updated_at ?: $best->updated_at)->toIso8601String(),
                ] : null,
                'summary' => [
                    'product'        => optional($enquiry->getCommodityProduct)->name,
                    'brand'          => optional($enquiry->getBrand)->name,
                    'size_label'     => $enquiry->size_label,
                    'quantity'       => $qty,
                    'unit_label'     => $enquiry->unit_label,
                    'delivery_city'  => $enquiry->delivery_city,
                    'delivery_state' => $enquiry->delivery_state,
                    'required_by'    => $enquiry->required_by,
                ],
            ],
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
            'accept_best'         => ['nullable', 'boolean'],
            'token_amount'        => ['required', 'numeric', 'min:1'],
            'total_amount'        => ['required', 'numeric', 'min:1'],
            'selected_wallet'     => ['required', 'in:cash_balance,credit_balance'],
        ]);

        // "Proceed to Order" on the Best Price panel names no seller - the
        // buyer never saw one. Resolving the winner server-side also closes the
        // window where a cheaper bid lands between render and click.
        if (! empty($data['accept_best'])) {
            $bid = $this->bidding->bestBid($enquiry);
        } elseif (! empty($data['seller_quotation_id'])) {
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
