<?php

namespace App\Http\Controllers\Admin;

use App\Models\Address;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', ['page_title' => 'Admin Dashboard']);
    }

    public function addressSyn()
    {
        ini_set('max_execution_time', 1800);
        $pincodes = DB::table('pincodes')->get();
        foreach ($pincodes as $pincode){
            $city = DB::table('cities')->find($pincode->city_id);
            $state = DB::table('states')->find($city->state_id);

            $data = new Address;
            $data->pincode  = $pincode->pincode;
            $data->city     = $city->city;
            $data->state    = $state->state;
            $data->country  = 'India';
            $data->save();
        }

        return redirect()->back()->with('success', 'Address data synchronized successfully.');
    }

    public function notificationRead()
    {
        $list = Notification::where('is_admin_read', 0)->latest()->get();
        foreach ($list as $item){
            $item->is_admin_read = 1;
            $item->save();
        }
        return redirect()->route('admin.dashboard')->with('success', 'Notification marked as read successfully.');
    }

    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'FCM Token saved successfully']);
    }

    public function sendNotification()
    {
        sendAdminNotification('Hi Admin', 'This is a test notification');
        return 'notification sent successfully.';
    }
}
