<?php

namespace App\Enums;

/**
 * The price basis a buyer picked on the product page when raising an RFQ —
 * the "Price Option" chips. Stored on the RFQ so the buyer, the sellers and
 * the admin all read which price the buyer was comparing on.
 *
 *   basic    - the bare base price (no charges, no GST)
 *   ex_works - Ex price at the seller's gate, GST included
 *   for      - F.O.R: Ex price plus freight to the buyer's city
 */
enum PriceOption: string
{
    case Basic   = 'basic';
    case ExWorks = 'ex_works';
    case For     = 'for';

    public function label(): string
    {
        return match ($this) {
            self::Basic   => 'Basic',
            self::ExWorks => 'Ex-Works',
            self::For     => 'F.O.R',
        };
    }

    /** What it means, for a tooltip or a line under the label. */
    public function hint(): string
    {
        return match ($this) {
            self::Basic   => 'Base price, before charges and GST',
            self::ExWorks => "At the seller's gate, GST included",
            self::For     => "Delivered to the buyer's city, freight included",
        };
    }

    /** The label for a stored value, or null for an RFQ raised without one. */
    public static function labelFor(?string $value): ?string
    {
        return self::tryFrom((string) $value)?->label();
    }
}
