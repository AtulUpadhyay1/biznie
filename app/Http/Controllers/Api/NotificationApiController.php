<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;

class NotificationApiController extends Controller
{
    public function index()
    {
        $list = Notification::where('user_id', auth()->id())->get();
        return response()->json([
            'success'   => true,
            'list'      => NotificationResource::collection($list),
        ], 200);
    }

    public function update($id)
    {
        $data = Notification::find($id);
        if($data){
            $data->is_read = 1;
            $data->save();
            return response()->json([
                'success'   => true,
                'message'   => 'Notification mark read successfully.'
            ], 200);
        }
        return response([
            'success'       => false,
            'message'       => 'Invalid given notification id.'
        ], 400);
    }

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required|array'
        ]);

        Notification::whereIn('id', $request->id)->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Notification deleted successfully.',
        ], 200);
    }

    public function updateToken(Request $request)
    {
        $this->validate($request, [
            'fcm_token'     => 'required',
            'device_type'   => 'required',
        ]);

        $user = auth()->user();
        if($request->device_type == 'web'){
            $user->web_fcm_token = $request->fcm_token;
        }else if($request->device_type == 'ios'){
            $user->ios_fcm_token = $request->fcm_token;
        }else{
            $user->fcm_token    = $request->fcm_token;
        }
        $user->device_type  = $request->device_type;
        $user->save();
        return response([
            'success'   => true,
            'message'   => 'Token updated successfully.',
        ],200);

    }

    public function getSettings()
    {
        $data = NotificationSetting::where('model', User::class)
            ->where('model_id', auth()->id())
            ->first();
        $setting = [
            'notify_new_order_enquiry'  => true,
            'notify_seller_reply'       => true,
            'notify_booking_confirmed'  => true,
        ];

        if ($data) {
            $setting = [
                'notify_new_order_enquiry'  => (bool) $data->notify_new_order_enquiry,
                'notify_seller_reply'       => (bool) $data->notify_seller_reply,
                'notify_booking_confirmed'  => (bool) $data->notify_booking_confirmed,
            ];
        }

        return response()->json([
            'success'   => true,
            'setting'   => $setting,
        ], 200);

    }

    public function updateSettings(Request $request)
    {
        $this->validate($request, [
            'notify_new_order_enquiry'  => 'required|boolean',
            'notify_seller_reply'       => 'required|boolean',
            'notify_booking_confirmed'  => 'required|boolean',
        ]);

        $data = NotificationSetting::where('model', User::class)
            ->where('model_id', auth()->id())
            ->first();

        $settingData = [
            'notify_new_order_enquiry'  => (bool) $request->notify_new_order_enquiry,
            'notify_seller_reply'       => (bool) $request->notify_seller_reply,
            'notify_booking_confirmed'  => (bool) $request->notify_booking_confirmed,
        ];

        if ($data) {
            $data->update($settingData);
        } else {
            NotificationSetting::create(array_merge([
                'model'     => User::class,
                'model_id'  => auth()->id(),
            ], $settingData));
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Notification settings updated successfully.',
        ], 200);
    }

}
