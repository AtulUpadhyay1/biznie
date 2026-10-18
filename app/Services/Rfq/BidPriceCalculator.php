<?php

namespace App\Services\Rfq;

use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;
use App\Models\SellerProductEnquiry;
use App\Services\Orders\OrderSummary;
use App\Services\ProductPricingService;

/**
 * Turns the base price a seller bids into the Ex price the auction ranks on.
 *
 * Ported from the legacy seller quotation (App\Http\Resources\Seller\
 * ProductEnquiryDetailResource), which is how the old app priced a reply: the
 * seller types one Basic Price, and for every size the buyer asked for
 *
 *   charges  = loading + insurance + quality charge + extra charges (listing)
 *              + the buyer's quality price + packaging charge
 *   taxable  = base_price + gauge_diff + charges
 *   tax      = round(taxable * gst / 100)
 *   ex_price = taxable + tax                       (per unit, GST included)
 *
 * The bid's single Ex-Works figure is the quantity-weighted average across
 * the sizes, so F.O.R (that + freight) ranks every seller on the same basis.
 * The bid screen previews with terms() and the server prices with quote(), so
 * the number a seller sees before submitting is the number that is stored.
 */
class BidPriceCalculator
{
    public function __construct(private readonly ProductPricingService $pricing)
    {
    }

    /**
     * Everything that goes on top of the base price: the charges, the GST rate
     * and the buyer's sizes with this seller's gauge difference for each.
     *
     * @return array{
     *     listed_base_price: ?float,
     *     gst: float,
     *     charges: list<array{label: string, amount: float}>,
     *     charges_total: float,
     *     sizes: list<array{index: ?int, label: string, quantity: float, gauge_diff: float}>
     * }
     */
    public function terms(SellerProductEnquiry $bid, ?ProductEnquiry $rfq = null): array
    {
        $rfq ??= $bid->getProductEnquiry ?: ProductEnquiry::find($bid->product_enquiries_id);
        $listing = $bid->sellerListing();
        // Its last-resort fallback is a listing of another product, whose
        // charges and GST say nothing about this one.
        if ($listing && (int) $listing->commodity_product_id !== (int) $bid->commodity_product_id) {
            $listing = null;
        }

        $charges = [];
        if ($listing) {
            $charges[] = ['label' => 'Loading charges', 'amount' => (float) $listing->loading_charge];
            $charges[] = ['label' => 'Insurance', 'amount' => (float) $listing->insurance_charge];
            $charges[] = ['label' => 'Quality charge', 'amount' => (float) $listing->quality_charge];
            foreach ($this->pricing->otherCharges($listing) as $charge) {
                if (in_array($charge['operator'], ['+', '-'], true)) {
                    $charges[] = [
                        'label'  => (string) $charge['name'],
                        'amount' => $charge['operator'] === '-' ? -$charge['price'] : $charge['price'],
                    ];
                }
            }
        }

        // What the buyer chose on the product page costs the same from every seller.
        $quality = $rfq?->quality ?? $bid->quality;
        if (is_array($quality) && (float) ($quality['price'] ?? 0) !== 0.0) {
            $charges[] = ['label' => 'Quality: ' . ($quality['name'] ?? ''), 'amount' => (float) $quality['price']];
        }
        $packaging = $rfq?->packaging_charge ?? $bid->packaging_charge;
        if (is_array($packaging) && (float) ($packaging['charge'] ?? 0) !== 0.0) {
            $charges[] = ['label' => 'Packaging: ' . ($packaging['name'] ?? ''), 'amount' => (float) $packaging['charge']];
        }

        $charges = array_values(array_filter($charges, fn ($c) => $c['amount'] !== 0.0));

        $gst = $listing
            ? (float) ($listing->tax_rate ?? $listing->gst)
            : (float) ($rfq?->getCommodityProduct?->gst ?? 0);

        return [
            'listed_base_price' => $listing && (float) $listing->base_price > 0 ? (float) $listing->base_price : null,
            'gst'               => $gst,
            'charges'           => $charges,
            'charges_total'     => round(array_sum(array_column($charges, 'amount')), 2),
            'sizes'             => $this->sizes($bid, $rfq, $listing),
        ];
    }

    /**
     * The Ex price, size by size and as one per-unit figure, for a base price.
     *
     * @param  array  $terms  From terms().
     * @return array{
     *     base_price: float,
     *     sizes: list<array{index: ?int, label: string, quantity: float, gauge_diff: float, taxable: float, tax: float, ex_price: float, line_total: float}>,
     *     total_quantity: float,
     *     ex_total: float,
     *     ex_price: float
     * }
     */
    public function quote(array $terms, float $basePrice): array
    {
        $sizes = [];
        foreach ($terms['sizes'] as $size) {
            $taxable = $basePrice + $size['gauge_diff'] + $terms['charges_total'];
            $tax = round($taxable * $terms['gst'] / 100);
            $ex = $taxable + $tax;

            $sizes[] = $size + [
                'taxable'    => round($taxable, 2),
                'tax'        => (float) $tax,
                'ex_price'   => round($ex, 2),
                'line_total' => round($ex * $size['quantity'], 2),
            ];
        }

        $quantity = array_sum(array_column($sizes, 'quantity'));
        $exTotal = array_sum(array_column($sizes, 'line_total'));

        return [
            'base_price'     => round($basePrice, 2),
            'sizes'          => $sizes,
            'total_quantity' => round($quantity, 2),
            'ex_total'       => round($exTotal, 2),
            'ex_price'       => $quantity > 0 ? round($exTotal / $quantity, 2) : 0.0,
        ];
    }

    /**
     * The buyer's size rows, each with its gauge difference.
     *
     * As in the legacy quotation, a row's gauge difference is the current
     * price of the state-price row it was picked from (`SellerCommodityProduct
     * StatePrice::find($row['id'])`), so it follows the price list rather than
     * the figure frozen into the RFQ; a row whose price row is gone keeps the
     * difference the buyer was shown. A Rate Finder RFQ has no rows: it is one
     * line at the product's default gauge difference.
     */
    private function sizes(SellerProductEnquiry $bid, ?ProductEnquiry $rfq, ?SellerCommodityProduct $listing): array
    {
        $rows = is_array($bid->value) && $bid->value ? $bid->value : (is_array($rfq?->variation) ? $rfq->variation : []);
        $rows = array_filter($rows, 'is_array');

        if ($rows === []) {
            $gauge = $listing
                ? $this->pricing->defaultVariationPrice((int) $listing->commodity_product_id, $listing->brand_id ? (int) $listing->brand_id : null)
                : 0.0;

            return [[
                'index'      => null,
                'label'      => (string) ($rfq?->size_label ?: 'All sizes'),
                // An RFQ with no quantity still prices one unit.
                'quantity'   => (float) ($rfq?->quantity ?: 1),
                'gauge_diff' => (float) $gauge,
            ]];
        }

        $listed = SellerCommodityProductStatePrice::whereIn('id', array_filter(array_column($rows, 'id')))
            ->pluck('price', 'id');

        $sizes = [];
        foreach ($rows as $index => $row) {
            $gauge = $listed[$row['id'] ?? null] ?? ($row['price'] ?? 0);

            $sizes[] = [
                'index'      => $index,
                'label'      => OrderSummary::sizeLabel($row),
                'quantity'   => (float) ($row['quantity'] ?? 0),
                'gauge_diff' => is_numeric($gauge) ? (float) $gauge : 0.0,
            ];
        }

        return $sizes;
    }
}
