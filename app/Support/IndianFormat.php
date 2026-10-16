<?php

namespace App\Support;

/**
 * The single source of truth for Indian business identifiers.
 *
 * Every form that takes a mobile, pincode, GSTIN, PAN, HSN or GST rate —
 * storefront leads, RFQs, registration, profile, addresses, staff, the seller
 * product wizard and the admin approval screens — validates against these
 * constants, through the rules in App\Rules. The Next.js storefront mirrors
 * them in src/lib/validation; the two must change together.
 *
 * Kept as pure static functions (no container, no DB) so the rules, one-off
 * audit commands and tests can all call them directly.
 */
final class IndianFormat
{
    /** 10 digits, first digit 6–9 (TRAI mobile numbering). */
    public const MOBILE_REGEX = '/^[6-9]\d{9}$/';

    /** 6 digits, first digit 1–9 (India Post never issues a leading zero). */
    public const PINCODE_REGEX = '/^[1-9]\d{5}$/';

    public const PAN_REGEX = '/^[A-Z]{5}\d{4}[A-Z]$/';

    /** State code, PAN, entity number, the literal Z, then the checksum. */
    public const GSTIN_REGEX = '/^\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/';

    /** HSN is published at 4, 6 or 8 digits — anything else is a typo. */
    public const HSN_REGEX = '/^(\d{4}|\d{6}|\d{8})$/';

    /** GST slabs a goods listing can carry. */
    public const GST_RATES = [0, 5, 12, 18, 28];

    private const GSTIN_CHARSET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function isMobile(?string $value): bool
    {
        return $value !== null && preg_match(self::MOBILE_REGEX, trim($value)) === 1;
    }

    public static function isPincode(?string $value): bool
    {
        return $value !== null && preg_match(self::PINCODE_REGEX, trim($value)) === 1;
    }

    public static function isPan(?string $value): bool
    {
        return $value !== null && preg_match(self::PAN_REGEX, strtoupper(trim($value))) === 1;
    }

    public static function isHsn(?string $value): bool
    {
        return $value !== null && preg_match(self::HSN_REGEX, trim($value)) === 1;
    }

    public static function isGstRate(mixed $value): bool
    {
        if (! is_numeric($value)) {
            return false;
        }

        return in_array((float) $value, array_map('floatval', self::GST_RATES), true);
    }

    /**
     * Format, state code and checksum. A GSTIN that only matches the regex can
     * still be a typo (the QA run used ZYABCDE1234F1ZE): the 15th character is
     * a mod-36 check digit over the first 14, which catches any single-character
     * slip and most transpositions.
     */
    public static function isGstin(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $gstin = strtoupper(trim($value));

        if (preg_match(self::GSTIN_REGEX, $gstin) !== 1) {
            return false;
        }

        $state = (int) substr($gstin, 0, 2);
        // 01–38 are the states/UTs; 97 is "Other Territory", 99 Centre Jurisdiction.
        if (! (($state >= 1 && $state <= 38) || $state === 97 || $state === 99)) {
            return false;
        }

        return self::gstinCheckDigit(substr($gstin, 0, 14)) === $gstin[14];
    }

    /** The PAN embedded at positions 3–12 of a GSTIN. */
    public static function panFromGstin(string $gstin): string
    {
        return substr(strtoupper(trim($gstin)), 2, 10);
    }

    public static function gstinMatchesPan(?string $gstin, ?string $pan): bool
    {
        if (! $gstin || ! $pan) {
            return false;
        }

        return self::panFromGstin($gstin) === strtoupper(trim($pan));
    }

    /**
     * GSTN check digit: alternate weights 1 and 2 over the base-36 value of each
     * character, fold each product back into base 36, and take the complement.
     */
    public static function gstinCheckDigit(string $first14): string
    {
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $code = strpos(self::GSTIN_CHARSET, $first14[$i]);
            if ($code === false) {
                return '';
            }
            $product = $code * (($i % 2) + 1);
            $sum += intdiv($product, 36) + ($product % 36);
        }

        return self::GSTIN_CHARSET[(36 - ($sum % 36)) % 36];
    }

    /**
     * `XXXX5300` — enough for the account holder to recognise their own account,
     * not enough to be useful to anyone reading over their shoulder.
     */
    public static function maskAccountNumber(?string $number): string
    {
        $digits = preg_replace('/\D/', '', (string) $number);

        if ($digits === '') {
            return '';
        }

        return 'XXXX' . substr($digits, -4);
    }

    /**
     * Masks every run of 9–18 digits (the length range of an Indian bank
     * account number) in a free-text remark, keeping the last four.
     *
     * Admins type wallet and ledger notes by hand — "RTGS from A/c 50100123455300"
     * — and those notes are shown to the customer verbatim. Order ids
     * (OID-20260514-1230), dates and amounts are shorter than nine digits per
     * run, so they pass through untouched. A 10-digit phone number in a note is
     * masked too, which is the right outcome for a customer-facing remark.
     */
    public static function maskAccountNumbersIn(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        return preg_replace_callback(
            '/(?<!\d)\d{9,18}(?!\d)/',
            fn (array $m) => self::maskAccountNumber($m[0]),
            $text
        );
    }
}
