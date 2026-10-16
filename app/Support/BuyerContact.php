<?php

namespace App\Support;

use App\Models\CommodityProductOrder;

/**
 * The buyer's phone and email reach the seller (and transporter) only once
 * the seller has confirmed the order (4.11). Before that the platform owns
 * the conversation: the counterparty sees the buyer's name, never a number
 * to take the deal offline with.
 */
final class BuyerContact
{
    /** Keys that carry a way to reach someone, in any address/consignee blob. */
    private const CONTACT_KEYS = '/^(phone|mobile|mobile_no|mobile_number|contact|contact_no|contact_number|whatsapp|email|email_id|alternate_phone|alt_phone)$/i';

    public static function shared(CommodityProductOrder $order): bool
    {
        return $order->statusCode()->revealsBuyerContact();
    }

    /** The buyer block for a seller/transporter view of the order. */
    public static function customer(CommodityProductOrder $order, bool $withEmail = true): ?array
    {
        $buyer = $order->getCustomer;
        if (! $buyer) {
            return null;
        }

        $shared = self::shared($order);

        $out = [
            'id'    => $buyer->id,
            'name'  => $buyer->name,
            'phone' => $shared ? $buyer->phone : null,
        ];
        if ($withEmail) {
            $out['email'] = $shared ? $buyer->email : null;
        }
        $out['contact_shared'] = $shared;

        return $out;
    }

    /** An address or consignee blob with its phone/email removed until shared. */
    public static function scrub(mixed $value, bool $shared): mixed
    {
        if ($shared || ! is_array($value)) {
            return $value;
        }

        $out = [];
        foreach ($value as $key => $item) {
            if (is_string($key) && preg_match(self::CONTACT_KEYS, $key)) {
                $out[$key] = null;
                continue;
            }
            $out[$key] = is_array($item) ? self::scrub($item, false) : $item;
        }

        return $out;
    }
}
