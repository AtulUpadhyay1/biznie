<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerCommodityProductHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'seller_commodity_product_detail'   => 'array',
    ];
}
