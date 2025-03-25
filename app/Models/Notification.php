<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'data'      => 'array',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'data',
        'is_read',
        'is_admin_read'
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class);
    }
}
