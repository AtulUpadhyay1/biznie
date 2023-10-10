<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerType extends Model
{
    use HasFactory, SoftDeletes;

    public static function active()
    {
        return SellerType::where('status', '1');
    }
}
