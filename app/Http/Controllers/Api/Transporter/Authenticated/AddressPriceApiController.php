<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransporterAddressPrice;

class AddressPriceApiController extends Controller
{
    public function index()
    {
        $transporter_address = TransporterAddressPrice::where('user_id', auth()->id())
        ->get()
        ->groupBy('loading_address')
        ->map(function ($group) {
            return [
                'loading_address'   => $group->first()->loading_address,
                'unloading_address' => $group->map(function ($item) {
                    return [
                        'state'     => $item->state,
                        'city'      => $item->city,
                        'min_price' => $item->min_price,
                        'max_price' => $item->max_price,
                    ];
                })->toArray(),
            ];
        })->values()->toArray();

        return response([
            'success'   => true,
            'list'      => $transporter_address
        ],200);
    }

    public function assignBelt(Request $request)
    {
        $request->validate([
            'loading_address'   => 'required',
        ]);

        if($request->selected_city){
            foreach ($request->selected_city as $selected_city) {
                $request->validate([
                    'min_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'required',
                    'max_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'required',
                ],[
                    'min_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'Enter '.$selected_city.' Min Price.',
                    'max_price.'.strtolower(str_replace(" ","_",$selected_city))    => 'Enter '.$selected_city.' Max Price.',
                ]);
            }

            foreach ($request->selected_city as $selected_city) {

                $states_data = Address::where('city', $selected_city)->first();

                $data = new TransporterAddressPrice;
                $data->user_id = auth()->id();
                $data->loading_address = $request->loading_address;
                $data->state = $states_data ? $states_data->state : '';
                $data->city = $selected_city;
                $data->min_price = $request->min_price[strtolower(str_replace(" ","_",$selected_city))];
                $data->max_price = $request->max_price[strtolower(str_replace(" ","_",$selected_city))];
                $data->save();
            }

            return response([
                'success'   => true,
                'message'   => 'Vehicle assigned successfully.',
            ],200);

        }

        return response([
            'success'   => false,
            'message'   => 'Please select a city first and try again.',
        ],400);
    }
}
