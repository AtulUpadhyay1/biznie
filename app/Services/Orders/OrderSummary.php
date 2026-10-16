<?php

namespace App\Services\Orders;

use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use App\Support\AddressFormatter;

/**
 * The figures every party reads off one order.
 *
 * Buyer, seller, transporter and admin used to each assemble an order's
 * quantity, rate and GST their own way — the v2 seller resource read columns
 * that do not exist on the order (`total_quantity`, `variation`, `load_within`)
 * and so showed "—" for quantity and no GST line at all (B37/B38), while the
 * buyer page showed only a base price. This class derives them once, from what
 * is actually stored:
 *
 *   - lines      — the order's `value` (one row per size), which carries the
 *                  GST-inclusive ex-works rate and GST per unit; auction orders,
 *                  whose rows carry no rate, fall back to the accepted bid's
 *                  ex-works price; Rate Finder RFQs, which have no rows, fall
 *                  back to the RFQ's single quantity.
 *   - terms      — dispatch and credit days copied from the accepted quote.
 *
 * All money is per the product unit (MT unless the product says otherwise).
 */
class OrderSummary
{
    /**
     * @return array{
     *   unit: string,
     *   lines: list<array{label: string, quantity: float, rate: float, taxable_rate: float, gst_per_unit: float, amount: float}>,
     *   total_quantity: float,
     *   gst_rate: float,
     *   taxable_amount: float,
     *   gst_amount: float,
     *   ex_works_amount: float,
     *   freight_per_unit: float,
     *   freight_amount: float,
     *   other_amount: float,
     *   order_total: float,
     *   dispatch_days: ?int,
     *   buyer_credit_days: ?int,
     *   seller_credit_days: ?int,
     *   quality: ?string,
     *   packaging: ?string,
     * }
     */
    public static function forOrder(CommodityProductOrder $order): array
    {
        // Always the full rows: list endpoints eager-load these relations with
        // only `id,unique_id`, which would silently zero the quantity and terms.
        $bid = $order->getSellerProductEnquiry()->first();
        $rfq = $order->getProductEnquiry()->first();

        $summary = self::build(
            rows: is_array($order->value) ? $order->value : [],
            bid: $bid,
            rfq: $rfq,
            gstRate: self::gstRate($order->gst, $bid),
            unit: $order->getCommodityProduct?->getUnit?->short_name
                ?? $order->getCommodityProduct?->getUnit?->name
                ?? $rfq?->unit_label,
            freightPerUnit: (float) ($order->transport_price ?? 0),
            exWorksTotal: (float) ($order->total_ex_factory_amount ?? 0),
        );

        // What was actually booked wins over the recomputation: admin may have
        // adjusted the total when converting, and the ledger runs off it. The
        // difference (commission, packaging added at conversion) is shown as
        // its own line so the breakup still adds up to the total.
        if ((float) $order->total_amount > 0) {
            $summary['order_total'] = round((float) $order->total_amount, 2);
            $summary['other_amount'] = max(0.0, round(
                $summary['order_total'] - $summary['ex_works_amount'] - $summary['freight_amount'],
                2
            ));
        }

        return $summary;
    }

    /**
     * Figures for a quote that is about to become an order, so the order is
     * created from server-side numbers rather than ones posted by the client.
     */
    public static function forBid(SellerProductEnquiry $bid, ?ProductEnquiry $rfq = null): array
    {
        $rfq ??= $bid->getProductEnquiry;
        $rows = is_array($bid->value) && $bid->value !== [] ? $bid->value : (is_array($rfq?->variation) ? $rfq->variation : []);

        return self::build(
            rows: $rows,
            bid: $bid,
            rfq: $rfq,
            gstRate: self::gstRate(null, $bid),
            unit: $rfq?->unit_label ?? $bid->getCommodityProduct?->getUnit?->short_name,
            freightPerUnit: (float) ($bid->freight_charges ?? $bid->transport_price ?? 0),
        );
    }

    private static function build(
        array $rows,
        ?SellerProductEnquiry $bid,
        ?ProductEnquiry $rfq,
        float $gstRate,
        ?string $unit,
        float $freightPerUnit,
        float $exWorksTotal = 0.0
    ): array {
        $bidRate = (float) ($bid?->ex_works_price ?? 0);
        $lines = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $quantity = (float) ($row['quantity'] ?? 0);
            if ($quantity <= 0) {
                continue;
            }

            $rate = (float) ($row['per_unit_price'] ?? 0) ?: $bidRate;
            $gstPerUnit = isset($row['tax']) && (float) $row['tax'] > 0
                ? (float) $row['tax']
                : self::gstIncluded($rate, $gstRate);

            $lines[] = self::line(self::sizeLabel($row), $quantity, $rate, $gstPerUnit);
        }

        // Rate Finder RFQs carry one quantity and a size label, no rows.
        if ($lines === [] && $rfq && (float) $rfq->quantity > 0) {
            $lines[] = self::line(
                (string) ($rfq->size_label ?: 'All sizes'),
                (float) $rfq->quantity,
                $bidRate,
                self::gstIncluded($bidRate, $gstRate)
            );
        }

        // Orders converted before rates were stored per row: the only figure
        // left is the ex-works total. With a single size it is exact; with
        // several it can only be shown as a total, never as a per-size rate.
        $unpriced = $lines !== [] && array_sum(array_column($lines, 'amount')) <= 0;
        if ($unpriced && $exWorksTotal > 0 && count($lines) === 1) {
            $rate = $exWorksTotal / $lines[0]['quantity'];
            $lines[0] = self::line($lines[0]['label'], $lines[0]['quantity'], $rate, self::gstIncluded($rate, $gstRate));
        }

        $totalQuantity = array_sum(array_column($lines, 'quantity'));
        $exWorks = array_sum(array_column($lines, 'amount'));
        $gstAmount = array_sum(array_map(fn ($l) => $l['gst_per_unit'] * $l['quantity'], $lines));
        if ($exWorks <= 0 && $exWorksTotal > 0) {
            $exWorks = $exWorksTotal;
            $gstAmount = self::gstIncluded($exWorksTotal, $gstRate);
        }
        $freight = round($freightPerUnit * $totalQuantity, 2);

        return [
            'unit'               => self::unitLabel($unit),
            'lines'              => $lines,
            'total_quantity'     => round($totalQuantity, 2),
            'gst_rate'           => $gstRate,
            'taxable_amount'     => round($exWorks - $gstAmount, 2),
            'gst_amount'         => round($gstAmount, 2),
            'ex_works_amount'    => round($exWorks, 2),
            'freight_per_unit'   => round($freightPerUnit, 2),
            'freight_amount'     => $freight,
            'other_amount'       => 0.0,
            'order_total'        => round($exWorks + $freight, 2),
            'dispatch_days'      => self::days($bid?->load_within),
            'buyer_credit_days'  => self::days($bid?->customer_credit_days ?? $rfq?->credit_day),
            'seller_credit_days' => self::days($bid?->seller_credit_days),
            'quality'            => self::optionName($rfq?->quality ?? $bid?->quality),
            'packaging'          => self::optionName($rfq?->packaging_charge ?? $bid?->packaging_charge),
        ];
    }

    private static function line(string $label, float $quantity, float $rate, float $gstPerUnit): array
    {
        return [
            'label'        => $label,
            'quantity'     => round($quantity, 2),
            'rate'         => round($rate, 2),
            'taxable_rate' => round($rate - $gstPerUnit, 2),
            'gst_per_unit' => round($gstPerUnit, 2),
            'amount'       => round($rate * $quantity, 2),
        ];
    }

    /** GST contained in a GST-inclusive rate. */
    private static function gstIncluded(float $rate, float $gstRate): float
    {
        return $gstRate > 0 ? round($rate * $gstRate / (100 + $gstRate), 2) : 0.0;
    }

    /** The order's own rate, else the seller listing's, else the catalog's. */
    private static function gstRate(mixed $orderGst, ?SellerProductEnquiry $bid): float
    {
        if (is_numeric($orderGst) && (float) $orderGst > 0) {
            return (float) $orderGst;
        }

        $listing = $bid?->sellerListing();
        if ($listing && is_numeric($listing->gst) && (float) $listing->gst > 0) {
            return (float) $listing->gst;
        }

        $catalog = $bid?->getCommodityProduct;

        return $catalog && is_numeric($catalog->gst) ? (float) $catalog->gst : 0.0;
    }

    /** `8 MM` from `{value: [{value: "8", unit: {short_name: "MM"}}]}`. */
    private static function sizeLabel(array $row): string
    {
        $parts = [];
        foreach (is_array($row['value'] ?? null) ? $row['value'] : [] as $attribute) {
            if (! is_array($attribute) || ! isset($attribute['value'])) {
                continue;
            }
            $unit = $attribute['unit']['short_name'] ?? '';
            $parts[] = trim($attribute['value'] . ' ' . $unit);
        }

        return $parts ? implode(' × ', $parts) : ($row['size'] ?? 'Standard');
    }

    private static function unitLabel(?string $unit): string
    {
        $unit = trim((string) $unit);
        if ($unit === '' || in_array(strtolower($unit), ['mt', 'metric ton', 'metric tonne', 'ton', 'tonne'], true)) {
            return 'MT';
        }

        return $unit;
    }

    private static function days(mixed $value): ?int
    {
        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    private static function optionName(mixed $option): ?string
    {
        return is_array($option) && ! empty($option['name']) ? (string) $option['name'] : null;
    }

    /**
     * Normalised billing and delivery addresses. Ship-to lives in
     * `consignee_detail` for orders raised through the RFQ form, so delivery
     * falls back to it; `null` means neither was captured.
     *
     * @return array{billing: ?array, delivery: ?array}
     */
    public static function addresses(CommodityProductOrder $order): array
    {
        return [
            'billing'  => AddressFormatter::normalize($order->billing_address),
            'delivery' => AddressFormatter::first($order->delivery_address, $order->consignee_detail),
        ];
    }
}
