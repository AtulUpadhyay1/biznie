<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerKycDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'bank_response' => 'array',
        'address'       => 'array',
    ];

}
