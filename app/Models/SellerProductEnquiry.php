<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerProductEnquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'is_mark'
    ];

    protected $casts = [
        'value'                 => 'array',
        'billing_address'       => 'array',
        'delivery_address'      => 'array',
        'price'                 => 'array',
        'loading_address'       => 'array',
        'consignee_detail'      => 'array',
        'history'               => 'array',
        'quality'               => 'array',
        'packaging_charge'      => 'array',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getSellerCommodityProduct()
    {
        return $this->belongsTo(SellerCommodityProduct::class, 'user_id', 'user_id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function getCommodityProductOrder()
    {
        return $this->hasOne(CommodityProductOrder::class, 'seller_product_enquiries_id');
    }
}
