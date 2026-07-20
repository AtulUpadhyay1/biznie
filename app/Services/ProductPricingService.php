<?php

namespace App\Services;

use App\Models\CommodityProduct;
use App\Models\CommodityProductStatePrice;
use App\Models\CommodityProductVariation;
use App\Models\PackagingType;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductHistory;
use App\Models\TransporterAddressPrice;
use App\Models\TransporterDetail;
use Carbon\Carbon;

/**
 * Product price calculation.
 *
 * The rules are ported verbatim from the v1 detail resource
 * (App\Http\Resources\ProductDetailResource) so the legacy app, the v2 API and
 * the Next.js storefront all produce the same numbers:
 *
 *   extra_charges = sum of charge_price[i] where operator[i] is "+" or "-"
 *                   ("*", "/" and "%" contribute 0 on the detail page)
 *   total_charges = loading_charge + insurance_charge + quality_charge + extra_charges
 *   total_amount  = base_price + gauge_diff + total_charges
 *   tax_amount    = round(total_amount * gst / 100)
 *   ex_price      = total_amount + tax_amount
 *   for_price     = ex_price + freight (per unit)
 *
 * All amounts are per unit (per MT unless the product says otherwise). The
 * legacy pricing performs no kg <-> MT conversion, so none is done here.
 */
class ProductPricingService
{
    /**
     * Only this seller's listings are offered to buyers.
     *
     * Carried over from the legacy `seller-list-by-commodity-product` endpoint,
     * which was hardcoded to `user_id = 1`. Kept deliberately for now — set to
     * null to open the marketplace to every active seller.
     */
    private const HOUSE_SELLER_USER_ID = 1;

    /**
     * Sums the named charges of a seller product.
     *
     * Only "+" and "-" are applied. The multiplicative operators are ignored on
     * the detail page — see ProductDetailResource, where the multiplicative
     * branch is deliberately disabled.
     */
    public function extraCharges(SellerCommodityProduct $sellerProduct): float
    {
        $chargeNames = is_array($sellerProduct->charge_name) ? $sellerProduct->charge_name : [];
        $chargePrices = is_array($sellerProduct->charge_price) ? $sellerProduct->charge_price : [];
        $operators = is_array($sellerProduct->operator) ? $sellerProduct->operator : [];

        $extra = 0;
        foreach ($chargeNames as $key => $name) {
            $price = (float) ($chargePrices[$key] ?? 0);
            $operator = $operators[$key] ?? '';

            if ($operator === '+') {
                $extra += $price;
            } elseif ($operator === '-') {
                $extra -= $price;
            }
        }

        return $extra;
    }

    /** The named charges in the shape the clients render them. */
    public function otherCharges(SellerCommodityProduct $sellerProduct): array
    {
        $chargeNames = is_array($sellerProduct->charge_name) ? $sellerProduct->charge_name : [];
        $chargePrices = is_array($sellerProduct->charge_price) ? $sellerProduct->charge_price : [];
        $operators = is_array($sellerProduct->operator) ? $sellerProduct->operator : [];

        $charges = [];
        foreach ($chargeNames as $key => $name) {
            $charges[] = [
                'name'     => $name,
                'price'    => (float) ($chargePrices[$key] ?? 0),
                'operator' => $operators[$key] ?? '',
            ];
        }

        return $charges;
    }

    /**
     * Price difference of the product's default variation (the "gauge
     * difference"), for the brand this seller sells.
     */
    public function defaultVariationPrice(int $commodityProductId, ?int $brandId): float
    {
        $defaultVariation = CommodityProductVariation::where('commodity_product_id', $commodityProductId)
            ->where('is_default', 1)
            ->first();

        if (! $defaultVariation) {
            return 0;
        }

        $statePrice = CommodityProductStatePrice::where('commodity_product_id', $commodityProductId)
            ->where('commodity_product_variation_id', $defaultVariation->id)
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->first();

        return $statePrice ? (float) $statePrice->price : 0;
    }

    /**
     * Ex-Works breakup for a single seller product.
     */
    public function exWorksBreakup(SellerCommodityProduct $sellerProduct, ?float $gaugeDiff = null): array
    {
        $gaugeDiff ??= $this->defaultVariationPrice(
            (int) $sellerProduct->commodity_product_id,
            $sellerProduct->brand_id ? (int) $sellerProduct->brand_id : null
        );

        $basePrice = (float) $sellerProduct->base_price;
        $loadingCharge = (float) $sellerProduct->loading_charge;
        $insuranceCharge = (float) $sellerProduct->insurance_charge;
        $qualityCharge = (float) $sellerProduct->quality_charge;
        $gst = (float) $sellerProduct->gst;

        $extraCharges = $this->extraCharges($sellerProduct);
        $totalCharges = $loadingCharge + $insuranceCharge + $qualityCharge + $extraCharges;
        $totalAmount = $basePrice + $gaugeDiff + $totalCharges;
        $taxAmount = round($totalAmount * $gst / 100);

        return [
            'base_price'        => $basePrice,
            'gauge_diff'        => $gaugeDiff,
            'loading_charge'    => $loadingCharge,
            'insurance_charge'  => $insuranceCharge,
            'quality_charge'    => $qualityCharge,
            'extra_charges'     => $extraCharges,
            'total_charges'     => $totalCharges,
            'total_amount'      => $totalAmount,
            'gst'               => $gst,
            'tcs'               => (float) $sellerProduct->tcs,
            'tax_amount'        => $taxAmount,
            'ex_price'          => $totalAmount + $taxAmount,
        ];
    }

    /**
     * Every live offer for a commodity product, cheapest ex price first.
     *
     * This is the v2 replacement for the legacy
     * `seller-list-by-commodity-product` endpoint. It keeps that endpoint's
     * house-seller restriction, but prices every listing with the same rule the
     * detail page uses, so the number on the list card matches the number on
     * the detail page, and orders by price instead of database order.
     *
     * @return \Illuminate\Support\Collection<int, SellerCommodityProduct>
     */
    public function sellerOffers(CommodityProduct $product)
    {
        return SellerCommodityProduct::where('commodity_product_id', $product->id)
            ->where('status', 'active')
            ->when(self::HOUSE_SELLER_USER_ID, fn ($query) => $query->where('user_id', self::HOUSE_SELLER_USER_ID))
            ->whereNotNull('base_price')
            ->where('base_price', '>', 0)
            ->with(['getUser', 'getBrand', 'getStatePrice'])
            ->get()
            ->sortBy(fn ($sellerProduct) => $this->exWorksBreakup($sellerProduct)['ex_price'])
            ->values();
    }

    /**
     * Cheapest live offer for a commodity product.
     *
     * The legacy app only ever showed one seller, so "best price across sellers"
     * is new behaviour: every active seller product is priced with the rule
     * above and the lowest ex price wins.
     */
    public function bestSellerOffer(CommodityProduct $product, ?int $sellerProductId = null): ?array
    {
        $offers = $this->sellerOffers($product);

        if ($offers->isEmpty()) {
            return null;
        }

        $sellerProduct = $sellerProductId
            ? $offers->firstWhere('id', $sellerProductId) ?? $offers->first()
            : $offers->first();

        return [
            'seller_product' => $sellerProduct,
            'breakup'        => $this->exWorksBreakup($sellerProduct),
            'sellers_count'  => $offers->count(),
        ];
    }

    /**
     * Cheapest freight rate to the given destination.
     *
     * Ported from ProductDetailResource: transporters that carry this commodity
     * product, filtered to the buyer's state + city, cheapest min_price wins.
     * Returns 0 when the destination is unknown or uncovered.
     */
    public function freight(int $commodityProductId, ?string $state, ?string $city): int
    {
        if (! $state || ! $city) {
            return 0;
        }

        $transporterIds = TransporterDetail::whereJsonContains('commodity_product', $commodityProductId)
            ->pluck('user_id');

        if ($transporterIds->isEmpty()) {
            return 0;
        }

        $rate = TransporterAddressPrice::whereIn('user_id', $transporterIds)
            ->where('state', $state)
            ->where('city', $city)
            ->orderBy('min_price', 'asc')
            ->first();

        return $rate ? (int) $rate->min_price : 0;
    }

    /**
     * Destinations the transporters carrying this product actually quote for.
     *
     * Only these can produce a freight leg, so the storefront offers exactly
     * this list rather than a hardcoded set of cities.
     *
     * @return array<int, array{city: string, state: string}>
     */
    public function deliveryDestinations(int $commodityProductId): array
    {
        $transporterIds = TransporterDetail::whereJsonContains('commodity_product', $commodityProductId)
            ->pluck('user_id');

        if ($transporterIds->isEmpty()) {
            return [];
        }

        return TransporterAddressPrice::whereIn('user_id', $transporterIds)
            ->whereNotNull('city')
            ->whereNotNull('state')
            ->orderBy('city')
            ->get(['city', 'state'])
            ->unique(fn ($row) => $row->city.'|'.$row->state)
            ->map(fn ($row) => ['city' => $row->city, 'state' => $row->state])
            ->values()
            ->all();
    }

    /**
     * Dense 30-day base price series, oldest first.
     *
     * Mirrors the legacy payload: one entry per day, days without a history row
     * fall back to the product's current base price so the series has no holes.
     */
    public function priceHistory(SellerCommodityProduct $sellerProduct, int $days = 30): array
    {
        $history = SellerCommodityProductHistory::where('user_id', $sellerProduct->user_id)
            ->where('commodity_product_id', $sellerProduct->commodity_product_id)
            ->where('seller_commodity_product_id', $sellerProduct->id)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->get();

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->format('d/m/y');

            $latestOfDay = $history
                ->filter(fn ($row) => Carbon::parse($row->created_at)->format('d/m/y') === $key)
                ->last();

            $price = $latestOfDay->seller_commodity_product_detail['base_price']
                ?? $sellerProduct->base_price;

            $series[] = [
                'date'  => $date->toDateString(),
                'label' => $date->format('d M'),
                'price' => round((float) $price),
            ];
        }

        return $series;
    }

    /** Packaging options with their charge, cheapest first. */
    public function packagingOptions(SellerCommodityProduct $sellerProduct): array
    {
        $types = is_array($sellerProduct->packaging_type) ? $sellerProduct->packaging_type : [];
        $prices = is_array($sellerProduct->packaging_type_price) ? $sellerProduct->packaging_type_price : [];

        $options = [];
        foreach ($types as $typeId) {
            $type = PackagingType::find($typeId);
            if (! $type) {
                continue;
            }

            $options[] = [
                'id'     => $type->id,
                'name'   => $type->name,
                'charge' => (float) ($prices[$type->id] ?? 0),
            ];
        }

        usort($options, fn ($a, $b) => $a['charge'] <=> $b['charge']);

        return $options;
    }

    /** Quality options, zipping the product's parallel name/price arrays. */
    public function qualityOptions(CommodityProduct $product): array
    {
        $names = is_array($product->quality) ? $product->quality : [];
        $prices = is_array($product->quality_price) ? $product->quality_price : [];

        $options = [];
        foreach ($names as $index => $name) {
            $options[] = [
                'name'  => $name,
                'price' => (float) ($prices[$index] ?? 0),
            ];
        }

        return $options;
    }

    /**
     * F.O.R (doorstep, all inclusive) price for one unit.
     */
    public function forPrice(float $exPrice, float $freight, float $otherCharges = 0): float
    {
        return $exPrice + $freight + $otherCharges;
    }
}
