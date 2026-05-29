<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProfileResource;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('getUserDetail');

        return response()->json([
            'success' => true,
            'data'    => new ProfileResource($user),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $detail = UserDetail::where('user_id', $user->id)->first() ?: new UserDetail();

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:120'],
            'email'            => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'            => ['nullable', 'string', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'company_name'     => ['nullable', 'string', 'max:160'],
            'company_address'  => ['nullable', 'string', 'max:500'],
            'address_line_one' => ['nullable', 'string', 'max:255'],
            'address_line_two' => ['nullable', 'string', 'max:255'],
            'postal_code'      => ['nullable', 'string', 'max:10'],
            'city'             => ['nullable', 'string', 'max:80'],
            'state'            => ['nullable', 'string', 'max:80'],
            'country'          => ['nullable', 'string', 'max:80'],
            'gst_number'       => ['nullable', 'string', 'max:30', Rule::unique('user_details', 'gst_number')->ignore($detail->id ?? null)],
            'pan_number'       => ['nullable', 'string', 'max:20', Rule::unique('user_details', 'pan_number')->ignore($detail->id ?? null)],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        if (array_key_exists('phone', $data)) {
            $user->phone = $data['phone'];
        }
        $user->save();

        $detail->user_id          = $user->id;
        $detail->company_name     = $data['company_name'] ?? $detail->company_name;
        $detail->company_address  = $data['company_address'] ?? $detail->company_address;
        $detail->address_line_one = $data['address_line_one'] ?? $detail->address_line_one;
        $detail->address_line_two = $data['address_line_two'] ?? $detail->address_line_two;
        $detail->postal_code      = $data['postal_code'] ?? $detail->postal_code;
        $detail->city             = $data['city'] ?? $detail->city;
        $detail->state            = $data['state'] ?? $detail->state;
        $detail->country          = $data['country'] ?? $detail->country ?? 'India';
        $detail->gst_number       = $data['gst_number'] ?? $detail->gst_number;
        $detail->pan_number       = $data['pan_number'] ?? $detail->pan_number;
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new ProfileResource($user->fresh('getUserDetail')),
        ]);
    }
}
