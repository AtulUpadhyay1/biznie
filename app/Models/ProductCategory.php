<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'attributes' => 'array',
    ];

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    public static function getSellerCategory()
    {
        return ProductCategory::whereIn('business_category_id', auth()->user()->getBusiness->category)->where('status', 1);
    }

    public static function active()
    {
        return ProductCategory::where('status', 1);
    }

    public function getSubCategory()
    {
        return $this->hasMany(ProductSubCategory::class, 'product_category_id');
    }

    public function getProducts()
    {
        return $this->hasMany(CommodityProduct::class, 'category_id');
    }
}
