<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\TransporterDetail;
use App\Http\Controllers\Controller;

class VehicleApiController extends Controller
{
    public function index()
    {
        $list = Vehicle::active()->latest()->select(['id', 'name', 'type', 'capacity', 'photo'])->get();
        foreach ($list as $data){
            $data->photo = imageUrl($data->photo);
            $data->is_selected = auth()->user()->getTransporterDetail->vehicle && in_array($data->id, auth()->user()->getTransporterDetail->vehicle) ? true : false;
        }
        return response([
            'success'   => true,
            'list'      => $list
        ],200);
    }

    public function assignVehicle(Request $request)
    {
        $request->validate([
            'vehicle' =>'nullable|array'
        ]);

        $transporter = TransporterDetail::where('user_id', auht()->id())->first();
        $transporter->vehicle = $request->vehicle;
        $transporter->save();

        return response([
            'success'   => true,
            'message'   => 'Vehicle assigned successfully.',
        ],200);
    }
}
