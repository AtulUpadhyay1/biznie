<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralEnquiry extends Model
{
    use HasFactory, SoftDeletes;

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
            ->orWhereHas('getSellerCommodityProduct', function ($q) use ($search) {
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

    public function getSellerCommodityProduct()
    {
        return $this->belongsTo(SellerCommodityProduct::class, 'seller_commodity_product_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
