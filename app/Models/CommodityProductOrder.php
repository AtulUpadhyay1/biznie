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
    ];
}
