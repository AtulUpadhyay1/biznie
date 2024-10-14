<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransporterProductEnquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'is_mark'
    ];

    protected $casts = [
        'value'                 => 'array',
        'billing_address'       => 'array',
        'delivery_address'      => 'array',
        'consignee_detail'      => 'array',
        'history'               => 'array',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }
}
