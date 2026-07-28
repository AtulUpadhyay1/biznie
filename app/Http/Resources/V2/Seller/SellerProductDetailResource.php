<?php

namespace App\Http\Resources\V2\Seller;

use App\Services\ProductPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * The seller's own product, in full.
 *
 * Kept apart from SellerProductResource so the products list stays a cheap
 * query: resolving packaging names and unpacking specifications is worth one
 * round trip on a detail page and fifteen wasted ones on a grid of cards.
 */
class SellerProductDetailResource extends SellerProductResource
{
    public function toArray(Request $request): array
    {
        /** @var ProductPricingService $pricing */
        $pricing = app(ProductPricingService::class);

        return array_merge(parent::toArray($request), [
            // What the ex price is actually made of, named rather than summed.
            'charges' => [
                'loading'   => (float) ($this->loading_charge ?? 0),
                'insurance' => (float) ($this->insurance_charge ?? 0),
                'quality'   => (float) ($this->quality_charge ?? 0),
                'gst'       => (float) ($this->gst ?? 0),
                'tcs'       => (float) ($this->tcs ?? 0),
                'other'     => $pricing->otherCharges($this->resource),
            ],

            // Stored as ids; a seller should not have to know what "2, 4" means.
            'packaging' => $pricing->packagingOptions($this->resource),

            'loading_addresses' => $this->loadingAddresses(),

            'specifications' => [
                'physical' => $this->specRows($this->physical_specification),
                'chemical' => $this->specRows($this->chemical_specification),
            ],

            // Collected by the product wizard; null on catalog-linked products.
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'product_type'      => $this->product_type,
            'hsn_code'          => $this->hsn_code,
            'tax_rate'          => $this->tax_rate !== null ? (float) $this->tax_rate : null,
            'moq'               => $this->moq,
            'moq_unit'          => $this->moq_unit,
            'net_weight'        => $this->net_weight,
            'weight_unit'       => $this->weight_unit,
            'tolerance'         => $this->tolerance,
            'make'              => $this->make,
            'brand_name'        => $this->brand_name,

            'submitted_at' => $this->submitted_at ? Carbon::parse($this->submitted_at)->toIso8601String() : null,
        ]);
    }

    /**
     * Loading points, normalised.
     *
     * Catalog products carry a list of full addresses; wizard products carry a
     * single `{city, state, country}` map. Both arrive here as a list.
     *
     * @return array<int, array<string, string|null>>
     */
    private function loadingAddresses(): array
    {
        $address = $this->loading_address;
        if (! is_array($address) || $address === []) {
            return [];
        }

        $rows = array_is_list($address) ? $address : [$address];

        return collect($rows)
            ->filter(fn ($row) => is_array($row))
            ->map(fn ($row) => [
                'address_line_one' => $row['address_line_one'] ?? null,
                'address_line_two' => $row['address_line_two'] ?? null,
                'pincode'          => $row['pin_code'] ?? $row['pincode'] ?? null,
                'city'             => $row['city'] ?? null,
                'state'            => $row['state'] ?? null,
                'country'          => $row['country'] ?? null,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{parameter: string, value: string}>
     */
    private function specRows(mixed $rows): array
    {
        return collect(is_array($rows) ? $rows : [])
            ->filter(fn ($row) => is_array($row) && (($row['parameter'] ?? '') !== '' || ($row['value'] ?? '') !== ''))
            ->map(fn ($row) => [
                'parameter' => (string) ($row['parameter'] ?? ''),
                'value'     => (string) ($row['value'] ?? ''),
            ])
            ->values()
            ->all();
    }
}
