<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'model_id',
        'notify_new_order_enquiry',
        'notify_seller_reply',
        'notify_booking_confirmed',
    ];

    protected $casts = [
        'notify_new_order_enquiry' => 'boolean',
        'notify_seller_reply' => 'boolean',
        'notify_booking_confirmed' => 'boolean',
    ];

    /**
     * Polymorphic owner (Admin, Seller, Buyer, or null for global)
     */
    public function owner()
    {
        return $this->morphTo('model', 'model', 'model_id');
    }
}

