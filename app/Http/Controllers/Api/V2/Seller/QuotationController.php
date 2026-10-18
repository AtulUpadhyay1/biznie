<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerQuotationDetailResource;
use App\Http\Resources\V2\Seller\SellerQuotationResource;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use App\Services\Rfq\BidPriceCalculator;
use App\Services\Rfq\LiveBiddingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct(private readonly LiveBiddingService $bidding)
    {
    }

    /**
     * The seller's RFQ inbox.
     *
     * Scoped to auctions this seller was actually invited into. It used to list
     * every pending enquiry on the platform, including ones for products the
     * seller does not stock, which buried the handful that mattered; RFQs are
     * fanned out automatically on creation now, so an invitation row is a
     * reliable signal.
     *
     *   received - invited, not priced, and the RFQ still taking rates ("New RFQs")
     *   replied  - priced by this seller in any way, with the result ("My Bids")
     *   closed   - not priced and the RFQ is over (ordered elsewhere, closed, cancelled)
     *
     * "Priced" is ProductEnquiry::PRICED_BID_SQL — an app bid, an admin-entered
     * rate or a legacy reply. Splitting on `ex_works_price` alone left admin-
     * and legacy-priced bids under "New" as "Selected · Rate not submitted",
     * and RFQs ordered from someone else stayed there for ever (B39/B40).
     */
    public function index(Request $request): JsonResponse
    {
        $ownerId = $this->ownerId($request);
        $perPage = min((int) $request->get('per_page', 15), 50);
        $tab     = in_array($request->get('tab'), ['received', 'replied', 'closed'], true) ? $request->get('tab') : 'received';

        $invitations = SellerProductEnquiry::where('user_id', $ownerId)
            ->whereNotNull('product_enquiries_id')
            ->when(
                $tab === 'replied',
                fn ($q) => $q->whereRaw(ProductEnquiry::PRICED_BID_SQL),
                fn ($q) => $q->whereRaw('NOT ' . ProductEnquiry::PRICED_BID_SQL)
            )
            ->pluck('product_enquiries_id');

        $stillOpen = fn ($q) => $q->openForBids();

        $list = ProductEnquiry::query()
            ->whereIn('id', $invitations)
            ->when($tab === 'received', $stillOpen)
            ->when($tab === 'closed', fn ($q) => $q->whereNot($stillOpen))
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
                'getUser:id,name',
            ])
            // Live auctions first, then the ones closing soonest: a seller's
            // attention is worth the most on the RFQ about to expire.
            ->orderByRaw('CASE WHEN bidding_status = ? AND bidding_ends_at > ? THEN 0 ELSE 1 END', ['live', now()])
            ->orderByRaw('bidding_ends_at IS NULL, bidding_ends_at ASC')
            ->latest('id')
            ->paginate($perPage);

        // One query for every reply on the page instead of one per row.
        $replies = SellerProductEnquiry::where('user_id', $ownerId)
            ->whereIn('product_enquiries_id', $list->getCollection()->pluck('id'))
            ->get()
            ->keyBy('product_enquiries_id');

        $list->getCollection()->transform(function (ProductEnquiry $enq) use ($replies) {
            $enq->setRelation('getMySellerProductEnquiry', $replies->get($enq->id));

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

    /**
     * One RFQ, as the bidding seller sees it.
     *
     * Only sellers holding an invitation get in: the detail carries the
     * competing board, and letting an uninvited seller read it would leak the
     * shape of an auction they are not part of.
     */
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

        $reply = $this->invitation($enquiry, $ownerId);
        if (! $reply) {
            return response()->json(['success' => false, 'message' => 'This RFQ was not sent to you.'], 403);
        }

        $this->bidding->closeIfExpired($enquiry);
        $enquiry->setRelation('getMySellerProductEnquiry', $reply);

        return response()->json([
            'success' => true,
            'data'    => new SellerQuotationDetailResource($enquiry),
        ]);
    }

    /**
     * The blind leaderboard.
     *
     * Rivals come back as "Seller A", "Seller B" with no id, name or city -
     * only their price ladder and where this seller sits in it. That is enough
     * to bid against and not enough to collude with.
     */
    public function leaderboard(Request $request, int $id): JsonResponse
    {
        $ownerId = $this->ownerId($request);

        $enquiry = ProductEnquiry::find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        if (! $this->invitation($enquiry, $ownerId)) {
            return response()->json(['success' => false, 'message' => 'This RFQ was not sent to you.'], 403);
        }

        $this->bidding->closeIfExpired($enquiry);

        $rows = $this->bidding->leaderboard($enquiry, $ownerId, false)->values();
        $mine = $rows->firstWhere('is_me', true);

        return response()->json([
            'success' => true,
            'data'    => $rows->all(),
            'meta'    => [
                'my_rank'      => $mine['rank'] ?? null,
                'my_for_price' => $mine['for_price'] ?? null,
                'total_bids'   => $rows->count(),
                'is_live'      => $this->bidding->isLive($enquiry),
                'ends_at'      => optional($enquiry->bidding_ends_at)->toIso8601String(),
                'seconds_left' => $this->bidding->secondsLeft($enquiry),
            ],
        ]);
    }

    /**
     * Submits or improves this seller's rate.
     *
     * The seller bids a base price, as in the legacy app; their listing's
     * charges, the buyer's quality and packaging, each size's gauge difference
     * and GST turn it into the Ex price (BidPriceCalculator). Freight and other
     * charges are the platform's, which is what the bid screen tells them —
     * letting a seller send them would let them win the F.O.R ranking by
     * under-declaring freight.
     *
     * `ex_works_price` on its own is the older contract (the Ex price typed
     * directly) and is still accepted from clients that send it.
     */
    public function quote(Request $request, int $id, BidPriceCalculator $calculator): JsonResponse
    {
        $data = $request->validate([
            'base_price'     => ['required_without:ex_works_price', 'nullable', 'numeric', 'gt:0', 'max:99999999'],
            'ex_works_price' => ['nullable', 'numeric', 'min:0'],
            'remarks'        => ['nullable', 'string', 'max:500'],
        ], [
            'base_price.required_without' => 'Enter your base price.',
            'base_price.gt'               => 'Enter a base price above zero.',
        ]);

        $ownerId = $this->ownerId($request);

        $enquiry = ProductEnquiry::find($id);
        if (! $enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.'], 404);
        }

        $bid = $this->invitation($enquiry, $ownerId);
        if (! $bid) {
            return response()->json(['success' => false, 'message' => 'This RFQ was not sent to you.'], 403);
        }

        $this->bidding->closeIfExpired($enquiry);

        if (! $this->bidding->isLive($enquiry)) {
            return response()->json([
                'success' => false,
                'message' => 'Bidding has closed for this RFQ.',
            ], 422);
        }

        if (strtolower((string) $enquiry->status) === 'ordered') {
            return response()->json([
                'success' => false,
                'message' => 'This RFQ has already been converted to an order.',
            ], 422);
        }

        $quote = isset($data['base_price'])
            ? $calculator->quote($calculator->terms($bid, $enquiry), (float) $data['base_price'])
            : null;

        $bid = $this->bidding->recordPrice(
            bid: $bid,
            exWorks: $quote ? $quote['ex_price'] : (float) $data['ex_works_price'],
            source: 'app',
            remarks: $data['remarks'] ?? null,
            quote: $quote
        );

        $enquiry->refresh()->setRelation('getMySellerProductEnquiry', $bid);

        return response()->json([
            'success' => true,
            'message' => 'Your rate has been submitted.',
            'data'    => new SellerQuotationDetailResource(
                $enquiry->load(['getBrand', 'getCommodityProduct.getCategory', 'getUser'])
            ),
            'meta'    => [
                'my_rank'    => $this->bidding->rankFor($enquiry, $ownerId),
                'base_price' => $quote['base_price'] ?? null,
                'ex_price'   => (float) $bid->ex_works_price,
                'for_price'  => (float) $bid->for_price,
            ],
        ], 201);
    }

    /**
     * Legacy quotation endpoint, kept for the older mobile clients.
     *
     * Their `base_price` is a base price, exactly as it is on quote(), so it
     * goes through the same calculation and lands on the leaderboard with a
     * proper Ex and F.O.R price.
     */
    public function respond(Request $request, int $id, BidPriceCalculator $calculator): JsonResponse
    {
        $data = $request->validate([
            'base_price'      => ['required', 'numeric', 'gt:0'],
            'transport_price' => ['nullable', 'numeric', 'min:0'],
            'note'            => ['nullable', 'string', 'max:500'],
        ]);

        $request->merge(['remarks' => $data['note'] ?? null]);

        return $this->quote($request, $id, $calculator);
    }

    /** This seller's invitation row for an RFQ, or null if they were not invited. */
    private function invitation(ProductEnquiry $enquiry, int $ownerId): ?SellerProductEnquiry
    {
        return SellerProductEnquiry::where('product_enquiries_id', $enquiry->id)
            ->where('user_id', $ownerId)
            ->first();
    }

    private function ownerId(Request $request): int
    {
        $user = $request->user();

        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }
}
