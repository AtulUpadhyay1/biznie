<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'value'     => 'array',
    ];
}
