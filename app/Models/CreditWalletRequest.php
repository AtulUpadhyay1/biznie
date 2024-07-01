<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditWalletRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'document'     => 'array',
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
