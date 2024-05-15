<?php

namespace App\Http\Controllers\Api\Seller\Authenticated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;

class DriverApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'      => 'required',
            'name'          => 'required',
            'phone'         => 'required',
            'transporter_name'  => 'required',
            'transporter_phone_number'  => 'required',
            // 'photo'         => 'required',
            // 'unloaded_vehicle_photo' => 'required',
            // 'loaded_vehicle_photo'   => 'required',
            // 'driver_with_vehicle_photo'   => 'required',
        ]);

        $data = CommodityProductOrder::find($request->order_id);
        if(!$data){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }

        $driver = new CommodityProductOrderDriver;
        $driver->order_id   = $request->order_id;
        $driver->name       = $request->name;
        $driver->phone      = $request->phone;
        $driver->photo      = $request->photo;
        $driver->unloaded_vehicle_photo         = $request->unloaded_vehicle_photo;
        $driver->loaded_vehicle_photo           = $request->loaded_vehicle_photo;
        $driver->driver_with_vehicle_photo      = $request->driver_with_vehicle_photo;
        $driver->tracking_number                = $request->tracking_number;
        $driver->vehicle_number                 = $request->vehicle_number;
        $driver->transporter_name               = $request->transporter_name;
        $driver->transporter_phone_number       = $request->transporter_phone_number;
        $driver->save();

        return response([
            'success'   => true,
            'message'   => 'Driver added successfully.',
        ],200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'name'          => 'required',
            // 'phone'         => 'required',
            // 'photo'         => 'required',
            // 'unloaded_vehicle_photo' => 'required',
            // 'loaded_vehicle_photo'   => 'required',
            // 'driver_with_vehicle_photo'   => 'required',
        ]);

        $driver = CommodityProductOrderDriver::find($id);

        if(!$driver){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }

        $driver->name       = $request->name ?? $driver->name;
        $driver->phone      = $request->phone ?? $driver->phone;
        $driver->photo      = $request->photo ?? $driver->photo;
        $driver->unloaded_vehicle_photo         = $request->unloaded_vehicle_photo ?? $driver->unloaded_vehicle_photo;
        $driver->loaded_vehicle_photo           = $request->loaded_vehicle_photo ?? $driver->loaded_vehicle_photo;
        $driver->driver_with_vehicle_photo      = $request->driver_with_vehicle_photo ?? $driver->driver_with_vehicle_photo;
        $driver->tracking_number                = $request->tracking_number ?? $driver->tracking_number;
        $driver->vehicle_number                 = $request->vehicle_number ?? $driver->vehicle_number;
        $driver->invoice                        = $request->invoice ?? $driver->invoice;
        $driver->ebill                          = $request->ebill ?? $driver->ebill;
        $driver->transport_receipt              = $request->transport_receipt ?? $driver->transport_receipt;
        $driver->alternate_phone_number         = $request->alternate_phone_number ?? $driver->alternate_phone_number;
        $driver->transporter_name               = $request->transporter_name ?? $driver->transporter_name;
        $driver->transporter_phone_number       = $request->transporter_phone_number ?? $driver->transporter_phone_number;
        $driver->advance_amount                 = $request->advance_amount ?? $driver->advance_amount;
        $driver->save();

        return response([
            'success'   => true,
            'message'   => 'Driver detail updated successfully.',
        ],200);
    }

    public function destroy($id)
    {
        $driver = CommodityProductOrderDriver::find($id);

        if(!$driver){
            return response([
                'success'   => false,
                'message'   => 'Invalid given id.',
            ],200);
        }

        $driver->delete();

        return response([
            'success'   => true,
            'message'   => 'Driver detail deleted successfully.',
        ],200);
    }
}
