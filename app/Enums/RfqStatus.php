<?php

namespace App\Enums;

use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;

/**
 * The one RFQ vocabulary (QA plan, Phase 2), read the same way by the buyer,
 * the seller and the admin:
 *
 *   rfq_open · quoted · closed_no_quote · accepted · lost (seller only)
 *   · expired · cancelled
 *
 * Derived from what actually happened — is the auction live, has anyone
 * priced, was an order placed and from whose quote — rather than from the
 * free-text status column, which still holds legacy strings ('Enquiry Sent To
 * Seller', 'Seller Replied', 'Mark For Sell'…). That is how an RFQ with quotes
 * could be listed as "Seller Replied" and still say "No rates were received"
 * on its detail page (B27).
 */
enum RfqStatus: string
{
    case Open          = 'rfq_open';
    case Quoted        = 'quoted';
    case ClosedNoQuote = 'closed_no_quote';
    case Accepted      = 'accepted';
    case Lost          = 'lost';
    case Expired       = 'expired';
    case Cancelled     = 'cancelled';

    private const CANCELLED = ['cancel', 'cancelled', 'canceled'];
    private const NO_SELLER = ['no seller available'];

    /** A bid counts as a quote once it carries a price, by app or by admin. */
    public static function isPriced(SellerProductEnquiry $bid): bool
    {
        return $bid->for_price !== null
            || (float) $bid->base_price > 0
            || in_array(strtolower((string) $bid->status), ['replied', 'mark for sell', 'ordered'], true);
    }

    /** The buyer's (and admin's) view of an RFQ. `$quotes` = number of priced bids. */
    public static function forBuyer(ProductEnquiry $rfq, int $quotes): self
    {
        $raw = strtolower(trim((string) $rfq->status));

        if ($raw === 'ordered') {
            return self::Accepted;
        }
        if (in_array($raw, self::CANCELLED, true)) {
            return self::Cancelled;
        }
        if ($quotes > 0) {
            return self::Quoted;
        }
        if (in_array($raw, self::NO_SELLER, true) || self::biddingOver($rfq)) {
            return self::ClosedNoQuote;
        }

        return self::Open;
    }

    /** One seller's view of an RFQ through their own bid. */
    public static function forSeller(SellerProductEnquiry $bid, ?ProductEnquiry $rfq = null): self
    {
        $rfq ??= $bid->getProductEnquiry;
        $rfqRaw = strtolower(trim((string) $rfq?->status));

        if (strtolower((string) $bid->status) === 'ordered') {
            return self::Accepted;
        }
        if ($rfqRaw === 'ordered') {
            return self::Lost;
        }
        if (in_array($rfqRaw, self::CANCELLED, true)) {
            return self::Cancelled;
        }
        if (self::isPriced($bid)) {
            return self::Quoted;
        }
        if ($rfq && self::biddingOver($rfq)) {
            return self::ClosedNoQuote;
        }

        return self::Open;
    }

    private static function biddingOver(ProductEnquiry $rfq): bool
    {
        return $rfq->bidding_status === 'closed'
            || ($rfq->bidding_ends_at && $rfq->bidding_ends_at->isPast());
    }

    /** "Quotes received (3)" for the buyer, "Rate submitted" for the seller. */
    public function label(string $audience = 'buyer', int $quotes = 0, ?string $locale = null): string
    {
        $key = "status.rfq.{$audience}.{$this->value}";

        return __($key, ['count' => $quotes], $locale);
    }
}
