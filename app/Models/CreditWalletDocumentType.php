<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditWalletDocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'title'         => 'array',
        'description'   => 'array',
        'forms'         => 'array',
    ];

    public function scopeActive($query)
    {
        $query->where("status", 1);
    }
}
