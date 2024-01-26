<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'attributes' => 'array',
    ];

    public static function active()
    {
        return ProductSubCategory::where('status', '1');
    }
}
