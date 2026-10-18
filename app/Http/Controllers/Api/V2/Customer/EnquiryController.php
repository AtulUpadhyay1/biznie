<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Enums\PriceOption;
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
use App\Rules\OrderQuantity;
use App\Services\Orders\OrderSummary;
use App\Services\Rfq\EnquiryCreator;
use App\Services\Rfq\LiveBiddingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function __construct(
        private readonly LiveBiddingService $bidding,
        private readonly EnquiryCreator $creator,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);

        $list = ProductEnquiry::where('user_id', $userId)
            ->rfqStatusFilter($request->string('status')->toString())
            ->withQuoteCount()
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
     * Raises an RFQ and immediately opens bidding on it. Creation itself lives
     * in EnquiryCreator, which the chat assistant shares.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'commodity_product_id' => ['required', 'integer', 'exists:commodity_products,id'],
            'brand_id'             => ['nullable', 'integer', 'exists:brands,id'],
            'origin_city'          => ['nullable', 'string', 'max:120'],
            'variation'            => ['nullable', 'array'],
            // Each size row is listed field by field: with a rule on
            // `variation.*.quantity` alone, validated() kept only the quantity
            // and the seller saw "Variant 1, 2, 3" instead of the sizes.
            'variation.*.id'            => ['nullable', 'integer'],
            'variation.*.value'         => ['nullable', 'array'],
            'variation.*.price'         => ['nullable', 'numeric'],
            // Every size row a real quantity: > 0, at most 2 decimals.
            'variation.*.quantity'      => ['nullable', new OrderQuantity],
            'variation.*.ex_price'      => ['nullable', 'numeric'],
            'variation.*.line_total'    => ['nullable', 'numeric'],
            'variation.*.stock'         => ['nullable'],
            'variation.*.user_selected' => ['nullable'],
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
            'quantity'             => ['nullable', new OrderQuantity],
            'unit_id'              => ['nullable', 'integer', 'exists:product_units,id'],
            'unit_label'           => ['nullable', 'string', 'max:30'],
            'size_label'           => ['nullable', 'string', 'max:150'],
            // The Price Option chip the buyer had selected on the product page.
            'price_option'         => ['nullable', Rule::enum(PriceOption::class)],
            'delivery_city'        => ['nullable', 'string', 'max:120'],
            'delivery_state'       => ['nullable', 'string', 'max:120'],
            'required_by'          => ['nullable', 'string', 'max:120'],
        ]);

        // The total against the product's MOQ, once the rows themselves pass.
        $validator->after(function ($v) use ($request) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            if ($message = EnquiryCreator::minimumOrderError($request->all())) {
                $v->errors()->add($request->filled('quantity') ? 'quantity' : 'variation', $message);
            }
        });

        $data = $validator->validate();

        $enquiry = $this->creator->create($request->user(), $data);

        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted successfully.',
            'data'    => new EnquiryDetailResource(
                $enquiry->fresh()->load('getBrand', 'getCommodityProduct.getCategory')
            ),
        ], 201);
    }

    /** `OID-20261016-4821`, unique — `rand()` alone could repeat within a day. */
    private function nextOrderId(): string
    {
        do {
            $id = 'OID-'.date('Ymd').'-'.random_int(1000, 9999);
        } while (CommodityProductOrder::where('order_id', $id)->exists());

        return $id;
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
        // Every priced bid, auction or admin-entered — the same count the list
        // page shows, so the detail page can never say "no rates" over quotes (B27).
        $quotes = $enquiry->getSellerProductEnquiries()->whereRaw(ProductEnquiry::PRICED_BID_SQL)->count();
        $rfqStatus = \App\Enums\RfqStatus::forBuyer($enquiry, $quotes);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $enquiry->id,
                'unique_id' => $enquiry->unique_id,
                'status'    => $enquiry->status,
                'status_code'  => $rfqStatus->value,
                'status_label' => $rfqStatus->label('buyer', $quotes),
                'quotes_count' => $quotes,
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
            // The buyer saw and agreed to the booking / cancellation rule (4.1).
            'accept_terms'        => ['accepted'],
        ], [
            'accept_terms.accepted' => 'Please agree to the booking and cancellation terms',
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

        // One order per RFQ: a double click or a second tab used to place two.
        if ($enquiry->status === 'ordered' || CommodityProductOrder::where('product_enquiries_id', $enquiry->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'An order has already been placed for this RFQ.'], 409);
        }

        // The order total comes from the accepted quote, priced by the same
        // OrderSummary the buyer, seller and admin order pages read — not from
        // the number the browser posts (B43). The posted total is only a
        // fallback for legacy quotes that carry no per-unit price at all.
        $figures = OrderSummary::forBid($bid, $enquiry);
        $total = $figures['order_total'] > 0 ? $figures['order_total'] : (float) $data['total_amount'];
        if ($data['token_amount'] > $total) {
            return response()->json([
                'success' => false,
                'message' => 'Token amount cannot be more than the order total.',
                'errors'  => ['token_amount' => ['Token amount cannot be more than the order total.']],
            ], 422);
        }
        $data['total_amount'] = $total;

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

        $order = DB::transaction(function () use ($enquiry, $bid, $data, $customer, $owner, $figures) {
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
            $order->order_id                    = $this->nextOrderId();
            $order->origin_city                 = $bid->origin_city;
            $order->value                       = $bid->value;
            // The quote's addresses, else the RFQ's own (B26: Rate Finder RFQs
            // collect them on the RFQ).
            $order->billing_address             = $bid->billing_address ?: $enquiry->billing_address;
            $order->delivery_address            = $bid->delivery_address ?: $enquiry->delivery_address;
            $order->purpose                     = $bid->purpose;
            $order->description                 = $bid->description;
            $order->message                     = $bid->message;
            $order->price                       = $bid->price;
            $order->base_price                  = $bid->base_price;
            $order->token_amount                = $data['token_amount'];
            $order->transport_price             = $bid->transport_price;
            $order->commission                  = $bid->commission;
            $order->commission_type             = $bid->commission_type;
            // Copied from the quote so every view prices the order the same way.
            $order->gst                         = $figures['gst_rate'];
            $order->total_ex_factory_amount     = $figures['ex_works_amount'];
            $order->total_freight_amount        = $figures['freight_amount'];
            $order->total_amount                = $data['total_amount'];
            $order->paid_amount                 = $data['token_amount'];
            $order->due_amount                  = $data['total_amount'] - $data['token_amount'];
            $order->loading_address             = $bid->loading_address;
            $order->delivery_by                 = $bid->delivery_by;
            $order->consignee_detail            = $bid->consignee_detail ?: $enquiry->consignee_detail;
            $order->status                      = 'pending';
            $order->history                     = [['status' => 'Order Confirmed By Customer', 'created_at' => Carbon::now()->toIso8601String()]];
            $order->save();
            // Quantity, dispatch and credit days, as agreed (B37).
            OrderSummary::snapshot($order);

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
