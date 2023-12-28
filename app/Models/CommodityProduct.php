<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'charge_name'           => 'array',
        'charge_price'          => 'array',
        'operator'              => 'array',
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
    ];

    public static function active()
    {
        return CommodityProduct::where('status', 'active');
    }

    public function getCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
