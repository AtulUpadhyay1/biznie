<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
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
        $user->fcm_token    = $request->fcm_token;
        $user->device_type  = $request->device_type;
        $user->save();
        return response([
            'success'   => true,
            'message'   => 'Token updated successfully.',
        ],200);

    }

}
