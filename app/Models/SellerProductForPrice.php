<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A seller's own freight rate to one city, for one listing.
 *
 * `price` holds freight per MT, not a delivered price: the buyer's F.O.R price
 * is the listing's ex-works price plus this. A row here therefore replaces the
 * transporter rate that would otherwise be looked up — see
 * ProductPricingService::destinationForPrice().
 */
class SellerProductForPrice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'state', 'city', 'price'];

    protected $casts = [
        'price' => 'float',
    ];

    public function getUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSellerProduct(): BelongsTo
    {
        return $this->belongsTo(SellerCommodityProduct::class, 'product_id');
    }
}
