<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerProductEnquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'value'                 => 'array',
        'billing_address'       => 'array',
        'delivery_address'      => 'array',
        'price'                 => 'array',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getSellerCommodityProduct()
    {
        return $this->belongsTo(SellerCommodityProduct::class, 'commodity_product_id');
    }
}
