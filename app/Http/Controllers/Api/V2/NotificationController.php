<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\NotificationResource;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $perPage = min((int) $request->get('per_page', 20), 50);

        $list = Notification::where('user_id', $userId)
            ->when($request->boolean('unread_only'), fn ($q) => $q->where('is_read', 0))
            ->latest()
            ->paginate($perPage);

        $unread = Notification::where('user_id', $userId)->where('is_read', 0)->count();

        return response()->json([
            'success'      => true,
            'data'         => NotificationResource::collection($list)->resolve(),
            'unread_count' => $unread,
            'meta'         => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('is_read', 0)
            ->count();
        return response()->json(['success' => true, 'data' => ['count' => $count]]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $n = Notification::where('user_id', $request->user()->id)->find($id);
        if (! $n) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }
        $n->is_read = 1;
        $n->save();
        return response()->json(['success' => true, 'message' => 'Notification marked read.']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        return response()->json(['success' => true, 'message' => 'All notifications marked read.']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id'   => ['nullable', 'array'],
            'id.*' => ['integer'],
            'all'  => ['nullable', 'boolean'],
        ]);

        $q = Notification::where('user_id', $request->user()->id);
        if (! empty($data['all'])) {
            $q->delete();
        } else {
            if (empty($data['id'])) {
                return response()->json(['success' => false, 'message' => 'No notifications selected.'], 422);
            }
            $q->whereIn('id', $data['id'])->delete();
        }
        return response()->json(['success' => true, 'message' => 'Notifications deleted.']);
    }

    public function updateToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fcm_token'   => ['required', 'string', 'max:512'],
            'device_type' => ['required', 'string', 'in:web,ios,android'],
        ]);

        $user = $request->user();
        if ($data['device_type'] === 'web') {
            $user->web_fcm_token = $data['fcm_token'];
        } elseif ($data['device_type'] === 'ios') {
            $user->ios_fcm_token = $data['fcm_token'];
        } else {
            $user->fcm_token = $data['fcm_token'];
        }
        $user->device_type = $data['device_type'];
        $user->save();

        return response()->json(['success' => true, 'message' => 'Token updated successfully.']);
    }

    public function clearToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_type' => ['required', 'string', 'in:web,ios,android'],
        ]);

        $user = $request->user();
        if ($data['device_type'] === 'web') {
            $user->web_fcm_token = null;
        } elseif ($data['device_type'] === 'ios') {
            $user->ios_fcm_token = null;
        } else {
            $user->fcm_token = null;
        }
        $user->save();

        return response()->json(['success' => true, 'message' => 'Token cleared.']);
    }

    public function getSettings(Request $request): JsonResponse
    {
        $row = NotificationSetting::where('model', User::class)
            ->where('model_id', $request->user()->id)
            ->first();

        $setting = [
            'notify_new_order_enquiry' => $row ? (bool) $row->notify_new_order_enquiry : true,
            'notify_seller_reply'      => $row ? (bool) $row->notify_seller_reply : true,
            'notify_booking_confirmed' => $row ? (bool) $row->notify_booking_confirmed : true,
        ];

        return response()->json(['success' => true, 'data' => $setting]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $data = $request->validate([
            'notify_new_order_enquiry' => ['required', 'boolean'],
            'notify_seller_reply'      => ['required', 'boolean'],
            'notify_booking_confirmed' => ['required', 'boolean'],
        ]);

        $row = NotificationSetting::where('model', User::class)
            ->where('model_id', $request->user()->id)
            ->first();

        if ($row) {
            $row->update($data);
        } else {
            NotificationSetting::create(array_merge([
                'model'    => User::class,
                'model_id' => $request->user()->id,
            ], $data));
        }

        return response()->json(['success' => true, 'message' => 'Notification settings updated.']);
    }
}
