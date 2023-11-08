<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'category'      => 'array',
        'type'          => 'array',
        'seller_type'   => 'array',
    ];

    public static function active()
    {
        return Business::where('status', 1);
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSellerKycDetail()
    {
        return $this->belongsTo(SellerKycDetail::class, 'user_id', 'user_id');
    }
}
