<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'value'             => 'array',
        'billing_address'   => 'array',
        'delivery_address'  => 'array',
        'loading_address'   => 'array',
        'message'           => 'array',
        'price'             => 'array',
        'quality_check_image'   => 'array',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getDrivers()
    {
        return $this->hasMany(CommodityProductOrderDriver::class, 'order_id');
    }
}
