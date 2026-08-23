<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerProductEnquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'is_mark'
    ];

    /** Memoised `sellerListing()` result; not an attribute, never persisted. */
    private ?SellerCommodityProduct $resolvedListing = null;

    private bool $resolvedListingLoaded = false;

    protected $casts = [
        'value'                 => 'array',
        'billing_address'       => 'array',
        'delivery_address'      => 'array',
        'price'                 => 'array',
        'loading_address'       => 'array',
        'consignee_detail'      => 'array',
        'history'               => 'array',
        'quality'               => 'array',
        'packaging_charge'      => 'array',
        'ex_works_price'        => 'float',
        'freight_charges'       => 'float',
        'other_charges'         => 'float',
        'for_price'             => 'float',
        'price_updated_at'      => 'datetime',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    /**
     * A listing belonging to this enquiry's seller.
     *
     * The join is on `user_id` alone, so it is ambiguous the moment a seller
     * lists more than one product - and on an eager load the *last* matching
     * row wins. That started resolving to the half-filled rows the seller
     * product-request wizard leaves behind (no `commodity_product_id`, no state
     * prices), which is what made `getStatePrice[0]` blow up on screens that
     * had worked for months.
     *
     * Drafts are excluded here so the relation can only ever land on a real
     * listing. When you need the listing for *this* enquiry's product - which
     * is almost always what you want - use `sellerListing()` instead.
     */
    public function getSellerCommodityProduct()
    {
        return $this->belongsTo(SellerCommodityProduct::class, 'user_id', 'user_id')
            ->whereNotNull('commodity_product_id')
            ->where(function ($query) {
                // Tolerates rows predating the request-status column.
                $query->whereNull('request_status')->orWhere('request_status', '!=', 'draft');
            });
    }

    /**
     * The seller's listing for the product this enquiry is actually about.
     *
     * Narrows by product and brand first, then by product alone (a seller may
     * stock the product without the exact brand), then falls back to any real
     * listing so a caller still gets charges and a loading point rather than a
     * fatal. Returns null only when the seller has no usable listing at all.
     */
    public function sellerListing(): ?SellerCommodityProduct
    {
        if ($this->resolvedListingLoaded) {
            return $this->resolvedListing;
        }

        $this->resolvedListingLoaded = true;

        $base = fn () => SellerCommodityProduct::where('user_id', $this->user_id)
            ->whereNotNull('commodity_product_id')
            ->where(function ($query) {
                $query->whereNull('request_status')->orWhere('request_status', '!=', 'draft');
            });

        $this->resolvedListing = $base()
                ->where('commodity_product_id', $this->commodity_product_id)
                ->where('brand_id', $this->brand_id)
                ->first()
            ?? $base()
                ->where('commodity_product_id', $this->commodity_product_id)
                ->first()
            ?? $base()->orderBy('id')->first();

        return $this->resolvedListing;
    }

    /**
     * Where this seller loads from, as a (state, city) pair.
     *
     * The bid's own loading address is authoritative; older rows carry one with
     * no city/state at all, so the seller's published state prices stand in.
     * Either component can come back null - callers must render a placeholder
     * rather than assume both are present.
     */
    public function originPlace(): array
    {
        $address = is_array($this->loading_address) ? ($this->loading_address[0] ?? null) : null;

        $state = is_array($address) && ! empty($address['state']) ? $address['state'] : null;
        $city  = is_array($address) && ! empty($address['city']) ? $address['city'] : null;

        if ($state === null || $city === null) {
            $statePrice = $this->sellerListing()?->getStatePrice()->first();
            $state ??= $statePrice?->state;
            $city ??= $statePrice?->city;
        }

        return ['state' => $state, 'city' => $city];
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function getCommodityProductOrder()
    {
        return $this->hasOne(CommodityProductOrder::class, 'seller_product_enquiries_id');
    }

    public function getProductEnquiry()
    {
        return $this->belongsTo(ProductEnquiry::class, 'product_enquiries_id');
    }
}
