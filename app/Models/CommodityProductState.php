<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductState extends Model
{
    use HasFactory, SoftDeletes;

    public function getStateVariationPrice()
    {
        return $this->hasMany(CommodityProductStatePrice::class, 'commodity_product_state_id');
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
