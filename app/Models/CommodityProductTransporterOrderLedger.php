<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityProductTransporterOrderLedger extends Model
{
    use HasFactory, SoftDeletes;

    public function getDrivers()
    {
        return $this->belongsTo(CommodityProductOrderDriver::class, 'driver_id');
    }
}
