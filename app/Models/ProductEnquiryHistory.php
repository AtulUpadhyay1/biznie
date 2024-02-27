<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductEnquiryHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'variation'         => 'array',
        'billing_address'   => 'array',
        'delivery_address'  => 'array',
        'consignee_detail'  => 'array',
        'message'           => 'array',
    ];
}
