<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'charge_name'           => 'array',
        'charge_price'          => 'array',
        'unit'                  => 'array',
        'operator'              => 'array',
        'attributes'            => 'array',
        'variation'             => 'array',
        'size'                  => 'array',
        'size_price'            => 'array',
        'specification'         => 'array',
        'dimension'             => 'array',
        'dimension_price'       => 'array',
        'quality'               => 'array',
        'quality_price'         => 'array',
        'images'                => 'array',
        'brand_id'              => 'array',
        'packaging_type'        => 'array',
        'packaging_type_price'  => 'array',
    ];

    public static function active()
    {
        return CommodityProduct::where('status', 'active');
    }

    public function getCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getSubCategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id');
    }

    public function getSubSubCategory()
    {
        return $this->belongsTo(ProductSubSubCategory::class, 'sub_sub_category_id');
    }

    public function getUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function getCommodityProductVariation()
    {
        return $this->hasMany(CommodityProductVariation::class, 'commodity_product_id');
    }

    public function getStateVariation()
    {
        return $this->hasMany(CommodityProductState::class, 'commodity_product_id');
    }
}
