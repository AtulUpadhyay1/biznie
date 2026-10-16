<?php

namespace App\Support;

use App\Models\Address;
use App\Models\SellerCommodityProduct;

/**
 * What a seller product must satisfy before it can be published (B42).
 *
 * Used twice: the seller's "Submit for review" refuses while anything is
 * outstanding, and the admin's "Approve" refuses too — the admin screen used
 * to publish whatever it was given, including a free-text HSN, an "18%" typed
 * into an amount field, or a product with no stock or dispatch time.
 */
final class SellerProductChecks
{
    /** @return list<string> human-readable problems; empty means publishable */
    public static function problems(SellerCommodityProduct $product): array
    {
        $problems = [];

        if (! IndianFormat::isHsn((string) $product->hsn_code)) {
            $problems[] = 'HSN code must be 4, 6 or 8 digits';
        }

        $rate = $product->tax_rate ?? $product->gst;
        if ($rate === null || $rate === '' || ! IndianFormat::isGstRate($rate)) {
            $problems[] = 'GST rate must be one of ' . implode(', ', IndianFormat::GST_RATES) . '%';
        }

        if (! $product->city || ! $product->state) {
            $problems[] = 'Loading city and state are required';
        } elseif (! self::isMasterCity($product->city, $product->state)) {
            $problems[] = "Loading city \"{$product->city}\" is not in the city master for {$product->state}";
        }

        if (! is_numeric($product->moq) || (float) $product->moq <= 0) {
            $problems[] = 'Minimum order quantity must be a number greater than 0';
        }

        if (! is_numeric($product->net_weight) || (float) $product->net_weight <= 0) {
            $problems[] = 'Net weight must be a number';
        }

        if (! is_numeric($product->quantity) || (float) $product->quantity <= 0) {
            $problems[] = 'Available quantity is required';
        }

        if (! is_numeric($product->load_within) || (int) $product->load_within <= 0) {
            $problems[] = 'Dispatch days are required';
        }

        return $problems;
    }

    /**
     * The address master (pincode table) is the city list buyers see, freight
     * is quoted against, and the storefront shows — so a loading point must be
     * one of its cities, not a free-text spelling.
     */
    public static function isMasterCity(string $city, string $state): bool
    {
        return Address::whereRaw('LOWER(TRIM(city)) = ?', [mb_strtolower(trim($city))])
            ->whereRaw('LOWER(TRIM(state)) = ?', [mb_strtolower(trim($state))])
            ->exists();
    }
}
