<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductStatePrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'price'     => 'array',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
