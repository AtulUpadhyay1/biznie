<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('banner_type', 'like', "%{$search}%")
            ->orWhere('resource_type', 'like', "%{$search}%")
            ->orWhere('for', 'like', "%{$search}%");
        });
    }
}
