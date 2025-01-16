<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    public function scopeSearch($query, $value){
        $query->where(function($q) use ($value) {
            $q->where("name", "like", "%{$value}%");
        });
    }

    public static function active()
    {
        return Brand::where('status', '1');
    }
}
