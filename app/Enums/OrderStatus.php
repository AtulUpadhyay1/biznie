<?php

namespace App\Enums;

use App\Models\CommodityProductOrder;
use Illuminate\Database\Eloquent\Builder;

/**
 * The one order lifecycle every surface speaks (QA plan, Phase 2):
 *
 *   booking_pending → booked (token paid) → seller_confirmed → loading →
 *   weighed_invoiced → dispatched → delivered → payment_settled → closed
 *   (+ cancelled)
 *
 * Orders are still *stored* with the legacy strings the admin panel and the
 * mobile API write ('pending', 'confirm', 'vehicle booked', 'bills
 * generated', 'complete', 'cancel'…). `of()` reads either vocabulary, and
 * `scopeFor()` filters on the canonical code, so the APIs, filters and
 * dashboards all use these codes today; rewriting the stored values is a
 * follow-up migration once every writer is switched.
 */
enum OrderStatus: string
{
    case BookingPending  = 'booking_pending';
    case Booked          = 'booked';
    case SellerConfirmed = 'seller_confirmed';
    case Loading         = 'loading';
    case WeighedInvoiced = 'weighed_invoiced';
    case Dispatched      = 'dispatched';
    case Delivered       = 'delivered';
    case PaymentSettled  = 'payment_settled';
    case Closed          = 'closed';
    case Cancelled       = 'cancelled';

    /** Stored strings that map to each canonical code (payment-dependent ones refined in of()). */
    private const LEGACY = [
        'booking_pending'  => ['pending', 'booking_pending'],
        'booked'           => ['pending', 'booked'],
        'seller_confirmed' => ['confirm', 'confirmed', 'seller_confirmed'],
        'loading'          => ['vehicle booked', 'vehicle waiting to load', 'loading'],
        'weighed_invoiced' => ['bills generated', 'weighed_invoiced'],
        'dispatched'       => ['dispatched'],
        'delivered'        => ['delivered'],
        'payment_settled'  => ['delivered', 'payment_settled'],
        'closed'           => ['complete', 'completed', 'closed'],
        'cancelled'        => ['cancel', 'cancelled', 'canceled'],
    ];

    /** Dashboard groups: every order falls in exactly one, so tiles add up to the total. */
    public const GROUPS = [
        'in_progress' => ['booking_pending', 'booked', 'seller_confirmed', 'loading', 'weighed_invoiced'],
        'dispatched'  => ['dispatched'],
        'delivered'   => ['delivered', 'payment_settled', 'closed'],
        'cancelled'   => ['cancelled'],
    ];

    public static function of(CommodityProductOrder $order): self
    {
        $raw = strtolower(trim((string) $order->status));

        if ($raw === 'pending') {
            return (float) $order->paid_amount > 0 ? self::Booked : self::BookingPending;
        }
        if ($raw === 'delivered') {
            return (float) $order->due_amount <= 0 && (float) $order->total_amount > 0 ? self::PaymentSettled : self::Delivered;
        }

        foreach (self::LEGACY as $code => $values) {
            if (in_array($raw, $values, true)) {
                return self::from($code);
            }
        }

        return self::BookingPending;
    }

    /** Restricts an order query to one canonical code. */
    public static function scopeFor(Builder $query, self $status): Builder
    {
        $values = self::LEGACY[$status->value];

        return match ($status) {
            self::BookingPending => $query->whereIn('status', $values)->where(fn ($q) => $q->whereNull('paid_amount')->orWhere('paid_amount', '<=', 0)),
            self::Booked         => $query->whereIn('status', $values)->where('paid_amount', '>', 0),
            self::Delivered      => $query->whereIn('status', $values)->where('due_amount', '>', 0),
            self::PaymentSettled => $query->whereIn('status', $values)->where('due_amount', '<=', 0),
            default              => $query->whereIn('status', $values),
        };
    }

    /** Restricts to a dashboard group (see GROUPS). */
    public static function scopeGroup(Builder $query, string $group): Builder
    {
        $values = collect(self::GROUPS[$group] ?? [])
            ->flatMap(fn ($code) => self::LEGACY[$code])
            ->unique()
            ->values()
            ->all();

        return $query->whereIn('status', $values);
    }

    public function label(?string $locale = null): string
    {
        return __('status.order.' . $this->value, [], $locale);
    }

    /**
     * The buyer's phone/email go to the seller and transporter only once the
     * seller has confirmed (4.11); never on a booking or a cancelled order.
     */
    public function revealsBuyerContact(): bool
    {
        return ! in_array($this, [self::BookingPending, self::Booked, self::Cancelled], true);
    }

    /** Next steps a seller may take from here (transport steps belong to the transporter/admin). */
    public function sellerNext(): array
    {
        return match ($this) {
            self::BookingPending, self::Booked => [self::SellerConfirmed, self::Cancelled],
            self::SellerConfirmed => [self::Loading, self::Cancelled],
            self::Loading => [self::WeighedInvoiced],
            self::WeighedInvoiced => [self::Dispatched],
            default => [],
        };
    }

    /**
     * The stored value to write for a canonical code, kept in the legacy
     * vocabulary until the admin panel writes canonical codes too.
     */
    public function storageValue(): string
    {
        return match ($this) {
            self::BookingPending, self::Booked => 'pending',
            self::SellerConfirmed => 'confirm',
            self::Loading => 'loading',
            self::WeighedInvoiced => 'bills generated',
            self::Dispatched => 'dispatched',
            self::Delivered, self::PaymentSettled => 'delivered',
            self::Closed => 'complete',
            self::Cancelled => 'cancel',
        };
    }
}
