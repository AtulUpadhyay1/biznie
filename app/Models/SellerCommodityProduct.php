<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerCommodityProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'charge_name'           => 'array',
        'charge_price'          => 'array',
        'unit'                  => 'array',
        'operator'              => 'array',
        'attributes'            => 'array',
        'variation'             => 'array',
        'size'                  => 'array',
        'size_price'            => 'array',
        'specification'         => 'array',
        'dimension'             => 'array',
        'dimension_price'       => 'array',
        'quality'               => 'array',
        'quality_price'         => 'array',
        'images'                => 'array',
        'packaging_type'        => 'array',
        'packaging_type_price'  => 'array',
        'loading_address'       => 'array',
        'physical_specification' => 'array',
        'chemical_specification' => 'array',
        'timeline'              => 'array',
        'current_step'          => 'integer',
        'submitted_at'          => 'datetime',
        'reviewed_at'           => 'datetime',
        // Which of the four prices the seller exposes on this listing.
        'show_basic_price'      => 'boolean',
        'show_ex_price'         => 'boolean',
        'show_for_price'        => 'boolean',
        'show_fob_price'        => 'boolean',
    ];

    /** How `price_validity` is written by every price form (string column). */
    public const PRICE_VALIDITY_FORMAT = 'Y-m-d h:i A';

    /**
     * Offers whose price is still valid. A listing past its `price_validity`
     * is a stale quote, not a live offer (B09) — it stays in the seller's
     * catalog but leaves the storefront until the seller re-prices it.
     * Listings that never set a validity are treated as open-ended.
     */
    public function scopeLiveOffer($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('price_validity')
                ->orWhere('price_validity', '')
                ->orWhereRaw("STR_TO_DATE(price_validity, '%Y-%m-%d %h:%i %p') >= ?", [now()->format('Y-m-d H:i:s')]);
        });
    }

    /** `price_validity` as a date, or null when unset or unparseable. */
    public function validTill(): ?Carbon
    {
        if (! $this->price_validity) {
            return null;
        }

        try {
            return Carbon::createFromFormat(self::PRICE_VALIDITY_FORMAT, $this->price_validity);
        } catch (\Throwable) {
            try {
                return Carbon::parse($this->price_validity);
            } catch (\Throwable) {
                return null;
            }
        }
    }

    public function isPriceExpired(): bool
    {
        return ($till = $this->validTill()) !== null && $till->isPast();
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getSubCategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id');
    }

    public function getSubSubCategory()
    {
        return $this->belongsTo(ProductSubSubCategory::class, 'sub_sub_category_id');
    }

    public function getUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatePrice()
    {
        return $this->hasMany(SellerCommodityProductStatePrice::class, 'seller_commodity_product_id');
    }

    public function getReviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    /**
     * Write a set of size variants onto the record.
     *
     * `variation` is the authoritative shape, but `size`, `size_price` and `unit`
     * are flat mirrors that the older catalog/pricing screens still read from, so
     * every writer has to keep all four in step — hence one place to do it.
     *
     * @param  array<int, array{size?: mixed, unit?: mixed, charge?: mixed, stock?: mixed}>  $rows
     */
    public function applyVariants(array $rows): void
    {
        $variation = [];
        $sizes = [];
        $sizePrices = [];
        $units = [];

        foreach ($rows as $row) {
            if (empty($row['size']) && empty($row['unit'])) {
                continue;
            }
            $variation[] = [
                'size'   => $row['size'] ?? null,
                'unit'   => $row['unit'] ?? null,
                'charge' => $row['charge'] ?? null,
                'stock'  => $row['stock'] ?? null,
            ];
            $sizes[] = $row['size'] ?? null;
            $sizePrices[] = ($row['charge'] ?? '') !== '' ? (float) $row['charge'] : null;
            $units[] = $row['unit'] ?? null;
        }

        $this->variation = $variation;
        $this->size = $sizes;
        $this->size_price = $sizePrices;
        $this->unit = $units;
    }

    public function appendTimeline(string $event, array $meta = []): void
    {
        $timeline = $this->timeline ?? [];
        $timeline[] = array_merge([
            'event' => $event,
            'label' => match ($event) {
                'step_saved' => 'Step saved',
                'submitted' => 'Product submitted',
                'approved' => 'Product approved',
                'rejected' => 'Product rejected',
                'resubmitted' => 'Product resubmitted',
                default => $event,
            },
            'at' => now()->toIso8601String(),
        ], $meta);

        $this->timeline = $timeline;
    }
}
