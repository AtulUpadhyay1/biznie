<?php

namespace App\Models;

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
        'brand_id'              => 'array',
        'packaging_type'        => 'array',
        'packaging_type_price'  => 'array',
    ];
}
