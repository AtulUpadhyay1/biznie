<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerCommodityProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'packaging_type'        => 'array',
        'packaging_type_price'  => 'array',
        'charge_name'           => 'array',
        'charge_price'          => 'array',
        'operator'              => 'array',
        'size'                  => 'array',
        'size_price'            => 'array',
        'dimension'             => 'array',
        'dimension_price'       => 'array',
        'specification'         => 'array',
        'quality'               => 'array',
        'quality_price'         => 'array',
        'pincode'               => 'array',
        'address'               => 'array',
        'images'                => 'array',
    ];
}
