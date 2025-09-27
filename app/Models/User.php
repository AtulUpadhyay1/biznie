<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'kyc_status',
        'kyc_verified_at',
        'kyc_description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'business_interest' => 'array',
        'permission' => 'array',
    ];

    public function scopeSearch($query, $value){
        $query->where(function($q) use ($value) {
            $q->where("name", "like", "%{$value}%")
              ->orWhere("email", "like", "%{$value}%")
              ->orWhere("phone", "like", "%{$value}%");
        });
    }

    public function getBusiness()
    {
        return $this->hasOne(Business::class);
    }

    public function getSellerKycDetail()
    {
        return $this->hasOne(SellerKycDetail::class);
    }

    public function getUserDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function getTransporterDetail()
    {
        return $this->hasOne(TransporterDetail::class);
    }

    public function getSellerProductEnquiries()
    {
        return $this->hasMany(SellerProductEnquiry::class, 'user_id');
    }

    public function getSellerOrders()
    {
        return $this->hasMany(CommodityProductOrder::class, 'seller_user_id');
    }

    public function getAddedBy()
    {
        return $this->hasOne(User::class, 'id', 'added_by');
    }
}
