<?php

namespace App\Support;

/**
 * One shape for every postal address an order or RFQ carries.
 *
 * Billing, delivery, consignee and loading addresses were stored as whatever
 * the writing screen had to hand: a UserAddress row (`pincode`, `gst`), a
 * legacy row (`pin_code`, `gst_number`), a one-element list (loading address),
 * or a JSON string. The API passed them through untouched and the buyer's order
 * page printed the JSON (B28). Resources call `normalize()` so every client
 * receives the same keys, or null when there is nothing to show.
 */
final class AddressFormatter
{
    /**
     * @return array{company_name: ?string, gstin: ?string, address_line_one: ?string, address_line_two: ?string, city: ?string, state: ?string, pincode: ?string, country: ?string, phone: ?string}|null
     */
    public static function normalize(mixed $raw): ?array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : ['address_line_one' => $raw];
        }

        if (! is_array($raw) || $raw === []) {
            return null;
        }

        // A list of addresses (loading points): the first one is the address.
        if (array_is_list($raw)) {
            $raw = is_array($raw[0] ?? null) ? $raw[0] : [];
        }

        $pick = function (string ...$keys) use ($raw): ?string {
            foreach ($keys as $key) {
                $value = $raw[$key] ?? null;
                if (is_scalar($value) && trim((string) $value) !== '') {
                    return trim((string) $value);
                }
            }

            return null;
        };

        $address = [
            'company_name'     => $pick('company_name', 'name'),
            'gstin'            => ($gstin = $pick('gst', 'gst_number', 'gstin')) ? strtoupper($gstin) : null,
            'address_line_one' => $pick('address_line_one', 'address', 'address_line_1'),
            'address_line_two' => $pick('address_line_two', 'address_line_2'),
            'city'             => ($city = $pick('city')) ? self::titleCase($city) : null,
            'state'            => $pick('state'),
            'pincode'          => $pick('pincode', 'pin_code', 'postal_code'),
            'country'          => $pick('country'),
            'phone'            => $pick('phone', 'mobile', 'contact_number'),
        ];

        $hasLocation = $address['address_line_one'] || $address['city'] || $address['pincode'];

        return $hasLocation ? $address : null;
    }

    /** The first address that resolves to something printable. */
    public static function first(mixed ...$candidates): ?array
    {
        foreach ($candidates as $candidate) {
            if ($address = self::normalize($candidate)) {
                return $address;
            }
        }

        return null;
    }

    /** `Plot 1, Maldahiya, Varanasi, Uttar Pradesh - 221002` for PDFs and notifications. */
    public static function oneLine(?array $address): string
    {
        if (! $address) {
            return '';
        }

        $line = implode(', ', array_filter([
            $address['address_line_one'],
            $address['address_line_two'],
            $address['city'],
            $address['state'],
        ]));

        return $address['pincode'] ? trim($line . ' - ' . $address['pincode'], ' -') : $line;
    }

    /**
     * City names arrive as typed (`VARANASI`, `east_godavari`); display them in
     * proper case without touching already-mixed values like `McLeod Ganj`.
     */
    public static function titleCase(string $city): string
    {
        $city = str_replace('_', ' ', trim($city));

        return ($city === strtoupper($city) || $city === strtolower($city))
            ? ucwords(strtolower($city))
            : $city;
    }
}
