<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'value'             => 'array',
        'billing_address'   => 'array',
        'delivery_address'  => 'array',
        'loading_address'   => 'array',
        'message'           => 'array',
        'price'             => 'array',
        'quality_check_image'   => 'array',
        'history'           => 'array',
        'consignee_detail'  => 'array',
    ];

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getDrivers()
    {
        return $this->hasMany(CommodityProductOrderDriver::class, 'order_id');
    }

    public function getSeller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function getTransporter()
    {
        return $this->belongsTo(User::class, 'transporter_user_id');
    }

    public function getProductEnquiry()
    {
        return $this->belongsTo(ProductEnquiry::class, 'product_enquiries_id');
    }

    public function getSellerProductEnquiry()
    {
        return $this->belongsTo(SellerProductEnquiry::class, 'seller_product_enquiries_id');
    }
}
