<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IngotPriceLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['location', 'is_default', 'status'];
}
