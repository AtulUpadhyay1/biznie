<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'images'            => 'array',
        'color_image'       => 'array',
        'colors'            => 'array',
        'attributes'        => 'array',
        'choice_options'    => 'array',
        'variation'         => 'array',
    ];

    public function getBusiness()
    {
        return $this->belongsTo(Business::class, 'user_id', 'user_id');
    }
}
