<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\RfqStatus;

class ProductEnquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'variation'         => 'array',
        'billing_address'   => 'array',
        'delivery_address'  => 'array',
        'consignee_detail'  => 'array',
        'message'           => 'array',
        'history'           => 'array',
        'quality'           => 'array',
        'packaging_charge'  => 'array',
        'quantity'          => 'float',
        'best_for_price'    => 'float',
        'bidding_started_at' => 'datetime',
        'bidding_ends_at'    => 'datetime',
    ];

    /** SQL for "this bid carries a price" — mirrors RfqStatus::isPriced(). */
    public const PRICED_BID_SQL = "(for_price IS NOT NULL OR base_price > 0 OR status IN ('replied','Mark For Sell','ordered'))";

    /** Adds `priced_bids_count` — how many sellers have actually quoted. */
    public function scopeWithQuoteCount($query)
    {
        return $query->withCount(['getSellerProductEnquiries as priced_bids_count' => fn ($q) => $q->whereRaw(self::PRICED_BID_SQL)]);
    }

    /** `?status=` on the buyer's RFQ list, by canonical code (RfqStatus). */
    public function scopeRfqStatusFilter($query, ?string $value)
    {
        $status = $value ? RfqStatus::tryFrom($value) : null;
        if (! $status) {
            return $value ? $query->where('status', $value) : $query;
        }

        $priced = fn ($q) => $q->whereRaw(self::PRICED_BID_SQL);
        $over = fn ($q) => $q->where('bidding_status', 'closed')->orWhere('bidding_ends_at', '<', now())->orWhere('status', 'No Seller Available');

        return match ($status) {
            RfqStatus::Accepted      => $query->where('status', 'ordered'),
            RfqStatus::Cancelled     => $query->whereIn('status', ['cancel', 'cancelled']),
            RfqStatus::Quoted        => $query->whereNotIn('status', ['ordered', 'cancel', 'cancelled'])->whereHas('getSellerProductEnquiries', $priced),
            RfqStatus::ClosedNoQuote => $query->whereNotIn('status', ['ordered', 'cancel', 'cancelled'])->whereDoesntHave('getSellerProductEnquiries', $priced)->where($over),
            RfqStatus::Open          => $query->whereNotIn('status', ['ordered', 'cancel', 'cancelled', 'No Seller Available'])
                ->whereDoesntHave('getSellerProductEnquiries', $priced)
                ->where(fn ($q) => $q->whereNull('bidding_status')->orWhere('bidding_status', '!=', 'closed'))
                ->where(fn ($q) => $q->whereNull('bidding_ends_at')->orWhere('bidding_ends_at', '>=', now())),
            default                  => $query,
        };
    }

    public function rfqStatus(): RfqStatus
    {
        $quotes = $this->priced_bids_count
            ?? $this->getSellerProductEnquiries()->whereRaw(self::PRICED_BID_SQL)->count();

        return RfqStatus::forBuyer($this, (int) $quotes);
    }

    /** Auctions still taking bids — what the admin "Live RFQs" screen lists. */
    public function scopeLiveBidding($query)
    {
        return $query->where('bidding_status', 'live')->where('bidding_ends_at', '>', now());
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('unique_id', 'like', "%{$search}%")
            ->orWhereHas('getBrand', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('getCommodityProduct', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('getUser', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getMarkedSellerProductEnquiry()
    {
        return $this->hasOne(SellerProductEnquiry::class, 'product_enquiries_id')->where('is_mark', 1);
    }

    public function getSellerProductEnquiries()
    {
        return $this->hasMany(SellerProductEnquiry::class, 'product_enquiries_id');
    }

    public function getMarkedTransporterEnquiry()
    {
        return $this->hasOne(TransporterProductEnquiry::class, 'product_enquiries_id')->where('is_mark', 1);
    }

    public function getCommodityProductOrder()
    {
        return $this->hasOne(CommodityProductOrder::class, 'product_enquiries_id');
    }
}
