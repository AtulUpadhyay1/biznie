<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductOrderDriver extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'final_quantity_by_seller'      => 'array',
        'final_quantity_by_customer'    => 'array',
        'seller_invoices'               => 'array',
    ];
}
