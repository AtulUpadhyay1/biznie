<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    public function scopeSearch($query, $value){
        $query->where(function($q) use ($value) {
            $q->where("pincode", "like", "%{$value}%")
              ->orWhere("city", "like", "%{$value}%")
              ->orWhere("state", "like", "%{$value}%")
              ->orWhere("country", "like", "%{$value}%");
        });
    }
}
