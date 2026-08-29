<?php

namespace App\Services\Rfq;

use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductEnquiry;
use App\Models\TransporterAddressPrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The one implementation of the live RFQ auction.
 *
 * Buyer, seller and admin all read the same numbers off this class rather than
 * each recomputing them: the buyer sees only the winning F.O.R price, the
 * seller sees their own rank against anonymised rivals, and the admin sees the
 * whole board plus who keyed each price in. Three views, one ranking.
 */
class LiveBiddingService
{
    /** Rank labels handed to everyone except the seller looking at their own row. */
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /* ------------------------------------------------------------------ */
    /* Pricing                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * F.O.R (doorstep) = what the seller wants at their gate + what it costs to
     * put it on the buyer's doorstep. This is the only figure anything ranks on.
     */
    public function forPrice(float $exWorks, float $freight, float $other = 0.0): float
    {
        return round($exWorks + $freight + $other, 2);
    }

    /**
     * Freight for one seller's lane, per unit.
     *
     * Transporters publish rates keyed on the *destination*, so the lookup is by
     * the RFQ's delivery city; `min_price` is used because the buyer is quoted
     * the cheapest way to move the load, not the average. The seller's own city
     * is carried through so the admin can see the lane and override the number
     * when no transporter has published one.
     */
    public function resolveFreight(ProductEnquiry $rfq, ?string $fromCity = null, ?string $fromState = null): float
    {
        $city  = trim((string) ($rfq->delivery_city ?? ''));
        $state = trim((string) ($rfq->delivery_state ?? ''));

        if ($city !== '') {
            $rate = TransporterAddressPrice::query()
                ->where('city', $city)
                ->when($state !== '', fn ($q) => $q->where('state', $state))
                ->whereNotNull('min_price')
                ->get()
                // `min_price` is a string column, so ordering has to happen in
                // PHP or "900" would sort above "1200".
                ->map(fn ($row) => (float) $row->min_price)
                ->filter(fn (float $p) => $p > 0)
                ->min();

            if ($rate) {
                return round((float) $rate, 2);
            }
        }

        return round((float) config('biznie.rfq.default_freight_per_unit', 0), 2);
    }

    /* ------------------------------------------------------------------ */
    /* Auction lifecycle                                                   */
    /* ------------------------------------------------------------------ */

    /** Opens the bidding window. Idempotent: re-opening a live RFQ is a no-op. */
    public function openBidding(ProductEnquiry $rfq, ?int $minutes = null): ProductEnquiry
    {
        if ($rfq->bidding_status === 'live' && $rfq->bidding_ends_at && $rfq->bidding_ends_at->isFuture()) {
            return $rfq;
        }

        $minutes = $minutes ?: (int) config('biznie.rfq.bidding_minutes', 15);

        $rfq->bidding_started_at = Carbon::now();
        $rfq->bidding_ends_at    = Carbon::now()->addMinutes(max(1, $minutes));
        $rfq->bidding_status     = 'live';
        $rfq->save();

        return $rfq;
    }

    /** Adds time to a running auction and tells every invited seller about it. */
    public function extend(ProductEnquiry $rfq, ?int $minutes = null): ProductEnquiry
    {
        $minutes = $minutes ?: (int) config('biznie.rfq.extend_minutes', 5);

        // Extending an auction that already lapsed restarts the clock from now,
        // not from the stale deadline, or "+5 minutes" could still be in the past.
        $from = $rfq->bidding_ends_at && $rfq->bidding_ends_at->isFuture()
            ? $rfq->bidding_ends_at->copy()
            : Carbon::now();

        $rfq->bidding_ends_at = $from->addMinutes(max(1, $minutes));
        $rfq->bidding_status  = 'live';
        $rfq->save();

        $this->notifyAllSellers(
            $rfq,
            'RFQ ' . $rfq->unique_id . ' extended',
            'Bidding for ' . $rfq->unique_id . ' has been extended. Improve your rate to win the order.'
        );

        return $rfq;
    }

    /** Flips a lapsed auction to `closed` so the UIs stop showing a live badge. */
    public function closeIfExpired(ProductEnquiry $rfq): ProductEnquiry
    {
        if ($rfq->bidding_status === 'live' && $rfq->bidding_ends_at && $rfq->bidding_ends_at->isPast()) {
            $rfq->bidding_status = 'closed';
            $rfq->save();
        }

        return $rfq;
    }

    public function isLive(ProductEnquiry $rfq): bool
    {
        return $rfq->bidding_status === 'live'
            && $rfq->bidding_ends_at !== null
            && $rfq->bidding_ends_at->isFuture();
    }

    public function secondsLeft(ProductEnquiry $rfq): int
    {
        if (! $rfq->bidding_ends_at) {
            return 0;
        }

        return max(0, (int) Carbon::now()->diffInSeconds($rfq->bidding_ends_at, false));
    }

    /* ------------------------------------------------------------------ */
    /* Dispatch                                                            */
    /* ------------------------------------------------------------------ */

    /**
     * Fans the RFQ out to every seller who lists the product.
     *
     * This replaces the admin hand-picking sellers before anything could
     * happen: a buyer's RFQ is worthless until sellers can see it, and a human
     * in that loop meant an RFQ raised at 11pm sat idle until morning. The admin
     * screen keeps its manual "add this seller too" path on top of the fan-out.
     *
     * Existing rows are reused, so a re-dispatch never wipes a price a seller
     * already submitted.
     */
    public function dispatchToSellers(ProductEnquiry $rfq): int
    {
        $sellerIds = $this->matchSellerIds($rfq);
        if (empty($sellerIds)) {
            return 0;
        }

        $sent = 0;
        foreach ($sellerIds as $sellerId) {
            if ($this->attachSeller($rfq, (int) $sellerId)) {
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->pushStatus($rfq, 'Enquiry Sent To Seller');
        }

        return $sent;
    }

    /**
     * Creates (or refreshes) one seller's invitation to the auction.
     *
     * Returns null when the seller is already on the board, so callers can tell
     * "invited someone new" from "they were already here".
     */
    public function attachSeller(ProductEnquiry $rfq, int $sellerId, ?SellerCommodityProduct $listing = null): ?SellerProductEnquiry
    {
        $listing ??= SellerCommodityProduct::where('user_id', $sellerId)
            ->where('commodity_product_id', $rfq->commodity_product_id)
            ->when($rfq->brand_id, fn ($q) => $q->where('brand_id', $rfq->brand_id))
            ->first();

        $loading   = $listing?->loading_address;
        $fromCity  = is_array($loading) && isset($loading[0]['city']) ? $loading[0]['city'] : null;
        $fromState = is_array($loading) && isset($loading[0]['state']) ? $loading[0]['state'] : null;

        $existing = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->where('user_id', $sellerId)
            ->first();

        if ($existing) {
            // Keep the lane fresh (the seller may have moved their loading
            // point) but never touch a submitted price.
            if (! $existing->ex_works_city && $fromCity) {
                $existing->ex_works_city  = $fromCity;
                $existing->ex_works_state = $fromState;
                $existing->save();
            }

            return null;
        }

        $bid = new SellerProductEnquiry();
        $bid->user_id              = $sellerId;
        $bid->product_enquiries_id = $rfq->id;
        $bid->customer_user_id     = $rfq->user_id;
        $bid->commodity_product_id = $rfq->commodity_product_id;
        $bid->brand_id             = $rfq->brand_id;
        $bid->unique_id            = $rfq->unique_id;
        $bid->origin_city          = $rfq->origin_city;
        $bid->value                = $rfq->variation ?: [];
        $bid->billing_address      = $rfq->billing_address;
        $bid->delivery_address     = $rfq->delivery_address;
        $bid->consignee_detail     = $rfq->consignee_detail;
        $bid->purpose              = $rfq->purpose;
        $bid->description          = $rfq->description;
        $bid->quality              = $rfq->quality;
        $bid->packaging_charge     = $rfq->packaging_charge;
        $bid->loading_address      = $loading;
        $bid->ex_works_city        = $fromCity;
        $bid->ex_works_state       = $fromState;
        $bid->freight_type         = 'doorstep';
        // Resolved at invitation time, not on first bid: the seller's rate form
        // shows freight and the calculated F.O.R price *before* they type an
        // ex-works figure, so the number has to exist by then.
        $bid->freight_charges      = $this->resolveFreight($rfq, $fromCity, $fromState);
        $bid->other_charges        = 0;
        $bid->status               = 'pending';
        $bid->history              = [['status' => 'New Enquiry', 'created_at' => Carbon::now()->toIso8601String()]];
        $bid->save();

        $this->notifySeller(
            $sellerId,
            'New RFQ ' . $rfq->unique_id,
            'A new RFQ is live. Submit your ex-works rate before the timer runs out.',
            $rfq
        );

        return $bid;
    }

    /**
     * Sellers who can actually fulfil this RFQ.
     *
     * Matching is on product (and brand, when the buyer named one) against
     * active listings from active seller accounts. A tighter variation-level
     * match is what the legacy admin screen used, but a Rate Finder RFQ carries
     * only a free-text size, and an unanswered RFQ is worse for the buyer than
     * one extra seller notification.
     */
    private function matchSellerIds(ProductEnquiry $rfq): array
    {
        $max = max(1, (int) config('biznie.rfq.max_sellers', 25));

        $listingUserIds = SellerCommodityProduct::query()
            ->where('commodity_product_id', $rfq->commodity_product_id)
            ->when($rfq->brand_id, fn ($q) => $q->where('brand_id', $rfq->brand_id))
            ->where('user_id', '!=', $rfq->user_id)
            ->where('status', 'active')
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($listingUserIds->isEmpty()) {
            return [];
        }

        // Only sellers whose account is live should be pulled into an auction.
        return User::whereIn('id', $listingUserIds)
            ->where('type', 'seller')
            ->where('status', 'active')
            ->limit($max)
            ->pluck('id')
            ->all();
    }

    /* ------------------------------------------------------------------ */
    /* Price entry                                                         */
    /* ------------------------------------------------------------------ */

    /**
     * Records a price against one bid, from either surface.
     *
     * The seller app only sends `ex_works_price` — freight and other charges
     * are the platform's to set, which is exactly what the seller screen
     * promises ("Biznie will add freight and other charges"). The admin screen
     * sends all three, because keying a price in on a seller's behalf is also
     * the moment you fix a freight figure no transporter published.
     *
     * `base_price` / `transport_price` are kept in step so order conversion,
     * which predates the auction, keeps reading the right numbers.
     */
    public function recordPrice(
        SellerProductEnquiry $bid,
        float $exWorks,
        ?float $freight = null,
        ?float $other = null,
        string $source = 'app',
        ?int $adminId = null,
        ?string $remarks = null,
        ?string $exWorksCity = null,
        ?string $exWorksState = null,
        ?string $freightType = null
    ): SellerProductEnquiry {
        $rfq = $bid->getProductEnquiry ?: ProductEnquiry::find($bid->product_enquiries_id);

        if ($exWorksCity !== null) {
            $bid->ex_works_city = $exWorksCity;
        }
        if ($exWorksState !== null) {
            $bid->ex_works_state = $exWorksState;
        }
        if ($freightType !== null) {
            $bid->freight_type = $freightType;
        }

        // A caller that does not set freight keeps whatever the bid already
        // carries, and only falls back to the lane rate on the first price.
        $resolvedFreight = $freight
            ?? ($bid->freight_charges !== null ? (float) $bid->freight_charges : null)
            ?? ($rfq ? $this->resolveFreight($rfq, $bid->ex_works_city, $bid->ex_works_state) : 0.0);

        $resolvedOther = $other ?? (float) ($bid->other_charges ?? 0);

        $bid->ex_works_price   = round($exWorks, 2);
        $bid->freight_charges  = round((float) $resolvedFreight, 2);
        $bid->other_charges    = round((float) $resolvedOther, 2);
        $bid->for_price        = $this->forPrice(
            (float) $bid->ex_works_price,
            (float) $bid->freight_charges,
            (float) $bid->other_charges
        );
        $bid->price_source     = $source;
        $bid->price_updated_at = Carbon::now();

        if ($source === 'manual') {
            $bid->entered_by_admin_id = $adminId;
        }
        if ($remarks !== null) {
            $bid->remarks = $remarks;
        }

        // Legacy columns the order pipeline still reads.
        $bid->base_price      = $bid->ex_works_price;
        $bid->transport_price = $bid->freight_charges;

        if ($bid->status !== 'ordered') {
            $bid->status = 'replied';
        }

        $history   = is_array($bid->history) ? $bid->history : [];
        $history[] = [
            'status'     => 'Price Submitted',
            'source'     => $source,
            'for_price'  => (float) $bid->for_price,
            'created_at' => Carbon::now()->toIso8601String(),
        ];
        $bid->history = $history;
        $bid->save();

        if ($rfq) {
            $this->refreshBestPrice($rfq);
        }

        return $bid;
    }

    /** Caches the winning F.O.R price so lists can show it without a join. */
    public function refreshBestPrice(ProductEnquiry $rfq): void
    {
        $best = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->whereNotNull('for_price')
            ->min('for_price');

        $status = $rfq->status;
        // "Ordered" and an admin's explicit "Seller Marked" outrank an
        // automatic status bump; everything else moves to Seller Replied.
        if ($best !== null && ! in_array($status, ['ordered', 'Seller Marked'], true)) {
            $status = 'Seller Replied';
        }

        // A targeted update, not a model save: this runs on every bid and must
        // not write back a stale copy of the rest of the row.
        ProductEnquiry::whereKey($rfq->id)->update([
            'best_for_price' => $best,
            'status'         => $status,
        ]);

        $rfq->best_for_price = $best;
        $rfq->status         = $status;
    }

    /* ------------------------------------------------------------------ */
    /* Leaderboard                                                         */
    /* ------------------------------------------------------------------ */

    /**
     * Every priced bid, cheapest F.O.R first.
     *
     * `viewerSellerId` is what makes one board serve two audiences: the seller
     * who owns a row sees "You (Current)" where everyone else sees "Seller C".
     * Identities are never in the payload — the label and the city are all any
     * bidder gets, which is what keeps the auction blind.
     */
    public function leaderboard(ProductEnquiry $rfq, ?int $viewerSellerId = null, bool $revealCity = true): Collection
    {
        $bids = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->whereNotNull('for_price')
            ->orderBy('for_price', 'asc')
            // Ties break on who got there first, so a refresh cannot reshuffle
            // two identical prices.
            ->orderBy('price_updated_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $letter = 0;

        return $bids->values()->map(function (SellerProductEnquiry $bid, int $index) use ($viewerSellerId, $revealCity, &$letter) {
            $isMine = $viewerSellerId !== null && (int) $bid->user_id === (int) $viewerSellerId;

            if ($isMine) {
                $label = 'You (Current)';
            } else {
                $label = 'Seller ' . (self::ALPHABET[$letter] ?? '#' . ($letter + 1));
                $letter++;
            }

            return [
                'id'              => $bid->id,
                'rank'            => $index + 1,
                'label'           => $label,
                'is_me'           => $isMine,
                'seller_city'     => $revealCity ? $bid->ex_works_city : null,
                'ex_works_price'  => (float) $bid->ex_works_price,
                'freight_charges' => (float) $bid->freight_charges,
                'other_charges'   => (float) $bid->other_charges,
                'for_price'       => (float) $bid->for_price,
                'source'          => $bid->price_source ?: 'app',
                'is_marked'       => (bool) $bid->is_mark,
                'updated_at'      => optional($bid->price_updated_at ?: $bid->updated_at)->toIso8601String(),
            ];
        });
    }

    /** The seller's own position, or null while they have not priced yet. */
    public function rankFor(ProductEnquiry $rfq, int $sellerId): ?int
    {
        $row = $this->leaderboard($rfq, $sellerId)->firstWhere('is_me', true);

        return $row['rank'] ?? null;
    }

    public function bestBid(ProductEnquiry $rfq): ?SellerProductEnquiry
    {
        return SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->whereNotNull('for_price')
            ->orderBy('for_price', 'asc')
            ->orderBy('price_updated_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    /** Best / average / spread — the three tiles under the admin board. */
    public function stats(ProductEnquiry $rfq): array
    {
        $row = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->whereNotNull('for_price')
            ->selectRaw('COUNT(*) as responses, MIN(for_price) as best, MAX(for_price) as worst, AVG(for_price) as average')
            ->first();

        $invited = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->count();
        $best    = $row && $row->best !== null ? (float) $row->best : null;
        $worst   = $row && $row->worst !== null ? (float) $row->worst : null;

        return [
            'invited'   => $invited,
            'responses' => (int) ($row->responses ?? 0),
            'best'      => $best,
            'average'   => $row && $row->average !== null ? round((float) $row->average, 2) : null,
            'spread'    => $best !== null && $worst !== null ? round($worst - $best, 2) : null,
        ];
    }

    /**
     * Which of the buyer's four progress dots is lit.
     *
     * `offer_ready` means the auction is over and a price is locked in;
     * `best_found` is the same price still being undercut.
     */
    public function stage(ProductEnquiry $rfq): string
    {
        if (strtolower((string) $rfq->status) === 'ordered') {
            return 'ordered';
        }

        $hasBid = $rfq->best_for_price !== null;

        if (! $this->isLive($rfq)) {
            return $hasBid ? 'offer_ready' : 'closed';
        }

        if ($hasBid) {
            return 'best_found';
        }

        return SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->exists()
            ? 'bidding'
            : 'sent';
    }

    /* ------------------------------------------------------------------ */
    /* Notifications                                                       */
    /* ------------------------------------------------------------------ */

    public function notifyAllSellers(ProductEnquiry $rfq, ?string $title = null, ?string $body = null): int
    {
        $sellerIds = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->pluck('user_id')
            ->unique();

        $title ??= 'RFQ ' . $rfq->unique_id . ' is live';
        $body  ??= 'Bidding is open. Submit or improve your rate to win this order.';

        foreach ($sellerIds as $sellerId) {
            $this->notifySeller((int) $sellerId, $title, $body, $rfq);
        }

        return $sellerIds->count();
    }

    private function notifySeller(int $sellerId, string $title, string $body, ProductEnquiry $rfq): void
    {
        $user = User::find($sellerId);
        if (! $user) {
            return;
        }

        // A failed push must never roll back the bid that triggered it.
        try {
            sendNotification($user, $title, $body, 'product_enquiry', ['unique_id' => $rfq->unique_id], true);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Appends to the RFQ timeline without clobbering a concurrent write. */
    private function pushStatus(ProductEnquiry $rfq, string $status): void
    {
        DB::transaction(function () use ($rfq, $status) {
            $fresh = ProductEnquiry::lockForUpdate()->find($rfq->id);
            if (! $fresh) {
                return;
            }

            $history   = is_array($fresh->history) ? $fresh->history : [];
            $history[] = ['status' => $status, 'created_at' => Carbon::now()->toIso8601String()];

            $fresh->history = $history;
            $fresh->status  = $status;
            $fresh->save();

            $rfq->history = $fresh->history;
            $rfq->status  = $fresh->status;
        });
    }
}
