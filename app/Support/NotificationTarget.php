<?php

namespace App\Support;

use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use App\Models\TransporterProductEnquiry;

/**
 * Who a notification is for and where tapping it goes (B33).
 *
 * Notifications used to carry only an RFQ reference, with no role and no
 * link, so a user who both buys and sells got seller prompts mixed into their
 * buyer inbox and nothing opened the RFQ or order they were about. Every
 * sender now passes one of these payloads, and the storefront filters on
 * `role` and opens `link`.
 */
final class NotificationTarget
{
    public const BUYER = 'buyer';
    public const SELLER = 'seller';
    public const TRANSPORTER = 'transporter';

    /** Payload for a notification about an RFQ, as seen by `$role`. */
    public static function rfq(ProductEnquiry $rfq, string $role, ?int $userId = null): array
    {
        return [
            'unique_id' => $rfq->unique_id,
            'rfq_id'    => $rfq->id,
            'role'      => $role,
            'link'      => self::rfqLink($rfq, $role, $userId),
        ];
    }

    /** Payload for a notification about an order, as seen by `$role`. */
    public static function order(CommodityProductOrder $order, string $role): array
    {
        return [
            'unique_id' => $order->unique_id,
            'order_id'  => $order->id,
            'role'      => $role,
            'link'      => match ($role) {
                self::SELLER      => "/seller/orders/{$order->id}",
                self::TRANSPORTER => "/transporter/orders/{$order->id}",
                default           => "/orders/{$order->id}",
            },
        ];
    }

    public static function rfqLink(ProductEnquiry $rfq, string $role, ?int $userId = null): string
    {
        if ($role === self::SELLER) {
            return "/seller/quotations/{$rfq->id}";
        }
        if ($role === self::TRANSPORTER) {
            $id = $userId
                ? TransporterProductEnquiry::where('product_enquiries_id', $rfq->id)->where('user_id', $userId)->value('id')
                : null;

            return $id ? "/transporter/enquiries/{$id}" : '/transporter/enquiries';
        }

        return "/rfq/{$rfq->id}";
    }

    /**
     * Role and link for a notification saved before roles existed, inferred
     * from how its user relates to the RFQ it names. Used by the backfill.
     *
     * @return array{role: ?string, link: ?string}
     */
    public static function infer(int $userId, ?string $uniqueId, string $text = ''): array
    {
        // Older rows lost their payload (a bug stored the notification itself
        // in `data`), but the RFQ reference is still in the title or body.
        if (! $uniqueId && preg_match('/\b(PE-\d{8}-\d{4}|BZN-RFQ-[\d-]+)\b/', $text, $m)) {
            $uniqueId = $m[1];
        }
        $rfq = $uniqueId ? ProductEnquiry::where('unique_id', $uniqueId)->first() : null;
        if (! $rfq) {
            return ['role' => null, 'link' => null];
        }

        $role = match (true) {
            (int) $rfq->user_id === $userId => self::BUYER,
            SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->where('user_id', $userId)->exists() => self::SELLER,
            TransporterProductEnquiry::where('product_enquiries_id', $rfq->id)->where('user_id', $userId)->exists() => self::TRANSPORTER,
            default => null,
        };

        return ['role' => $role, 'link' => $role ? self::rfqLink($rfq, $role, $userId) : null];
    }

    /**
     * Last resort for old rows that name no RFQ: the role from the message
     * kind and the user's account type, linking to the matching list.
     *
     * @return array{role: ?string, link: ?string}
     */
    public static function inferFromTitle(string $title, ?string $userType): array
    {
        $title = strtolower(trim($title));

        if (str_contains($title, 'received new product enquiry') || $userType === 'transporter') {
            return ['role' => self::TRANSPORTER, 'link' => '/transporter/enquiries'];
        }
        // "New Product Enquiry … please fill your price" is a seller invite —
        // unless it reached a buyer through the old wrong-recipient bug.
        if (str_contains($title, 'new product enquiry') && $userType === 'seller') {
            return ['role' => self::SELLER, 'link' => '/seller/quotations'];
        }
        if (str_contains($title, 'product enquiry') || str_contains($title, 'enquiry')) {
            return ['role' => self::BUYER, 'link' => '/rfq'];
        }

        return ['role' => null, 'link' => null];
    }
}
