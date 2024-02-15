<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeProduct extends Model
{
    use HasFactory;

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCommodityProduct()
    {
        return $this->belongsTo(CommodityProduct::class, 'commodity_product_id');
    }

    public function getSellerCommodityProduct()
    {
        return $this->belongsTo(SellerCommodityProduct::class, 'seller_commodity_product_id');
    }

    public function getBrand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
