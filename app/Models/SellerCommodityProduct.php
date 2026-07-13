<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerCommodityProduct extends Model
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
        'packaging_type'        => 'array',
        'packaging_type_price'  => 'array',
        'loading_address'       => 'array',
        'physical_specification' => 'array',
        'chemical_specification' => 'array',
        'timeline'              => 'array',
        'current_step'          => 'integer',
        'submitted_at'          => 'datetime',
        'reviewed_at'           => 'datetime',
    ];

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
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

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatePrice()
    {
        return $this->hasMany(SellerCommodityProductStatePrice::class, 'seller_commodity_product_id');
    }

    public function getReviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function appendTimeline(string $event, array $meta = []): void
    {
        $timeline = $this->timeline ?? [];
        $timeline[] = array_merge([
            'event' => $event,
            'label' => match ($event) {
                'step_saved' => 'Step saved',
                'submitted' => 'Product submitted',
                'approved' => 'Product approved',
                'rejected' => 'Product rejected',
                'resubmitted' => 'Product resubmitted',
                default => $event,
            },
            'at' => now()->toIso8601String(),
        ], $meta);

        $this->timeline = $timeline;
    }
}
