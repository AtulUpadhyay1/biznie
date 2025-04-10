<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductOrderHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];
}
