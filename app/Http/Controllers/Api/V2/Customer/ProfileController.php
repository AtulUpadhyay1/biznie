<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProfileResource;
use App\Models\SellerKycDetail;
use App\Models\TransporterDetail;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'getUserDetail',
            'getSellerKycDetail',
            'getTransporterDetail',
        ]);

        return response()->json([
            'success' => true,
            'data'    => new ProfileResource($user),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $user->type ?? 'customer';

        if ($type === 'seller') {
            return $this->updateSeller($request);
        }
        if ($type === 'transporter') {
            return $this->updateTransporter($request);
        }
        return $this->updateCustomer($request);
    }

    private function updateCustomer(Request $request): JsonResponse
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
            'data'    => new ProfileResource($user->fresh(['getUserDetail', 'getSellerKycDetail', 'getTransporterDetail'])),
        ]);
    }

    private function updateSeller(Request $request): JsonResponse
    {
        $user = $request->user();
        $kyc = SellerKycDetail::where('user_id', $user->id)->first() ?: new SellerKycDetail();
        $detail = UserDetail::where('user_id', $user->id)->first() ?: new UserDetail();

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:120'],
            'email'            => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'            => ['nullable', 'string', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'company_name'     => ['required', 'string', 'max:160'],
            'gst_number'       => ['required', 'string', 'max:30'],
            'pan_number'       => ['nullable', 'string', 'max:20'],
            'address_line_one' => ['nullable', 'string', 'max:255'],
            'address_line_two' => ['nullable', 'string', 'max:255'],
            'postal_code'      => ['nullable', 'string', 'max:10'],
            'city'             => ['nullable', 'string', 'max:80'],
            'state'            => ['nullable', 'string', 'max:80'],
            'country'          => ['nullable', 'string', 'max:80'],
            'account_number'   => ['nullable', 'string', 'max:30'],
            'account_holder_name' => ['nullable', 'string', 'max:120'],
            'bank_name'        => ['nullable', 'string', 'max:120'],
            'ifsc_code'        => ['nullable', 'string', 'max:20'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        if (array_key_exists('phone', $data)) {
            $user->phone = $data['phone'];
        }
        $user->save();

        // Maintain user_detail with company info too
        $detail->user_id      = $user->id;
        $detail->company_name = $data['company_name'];
        $detail->gst_number   = $data['gst_number'];
        $detail->pan_number   = $data['pan_number'] ?? $detail->pan_number;
        $detail->save();

        $kyc->user_id          = $user->id;
        $kyc->gst_number       = $data['gst_number'];
        $kyc->identity_number  = $data['pan_number'] ?? $kyc->identity_number;
        $kyc->address_line_one = $data['address_line_one'] ?? $kyc->address_line_one;
        $kyc->address_line_two = $data['address_line_two'] ?? $kyc->address_line_two;
        $kyc->postal_code      = $data['postal_code'] ?? $kyc->postal_code;
        $kyc->city             = $data['city'] ?? $kyc->city;
        $kyc->state            = $data['state'] ?? $kyc->state;
        $kyc->country          = $data['country'] ?? $kyc->country ?? 'India';
        $kyc->account_number   = $data['account_number'] ?? $kyc->account_number;
        $kyc->account_holder_name = $data['account_holder_name'] ?? $kyc->account_holder_name;
        $kyc->bank_name        = $data['bank_name'] ?? $kyc->bank_name;
        $kyc->ifsc_code        = $data['ifsc_code'] ?? $kyc->ifsc_code;
        if (! $kyc->status) {
            $kyc->status = 'pending';
        }
        $kyc->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new ProfileResource($user->fresh(['getUserDetail', 'getSellerKycDetail', 'getTransporterDetail'])),
        ]);
    }

    private function updateTransporter(Request $request): JsonResponse
    {
        $user = $request->user();
        $detail = TransporterDetail::where('user_id', $user->id)->first() ?: new TransporterDetail();

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'email'           => ['nullable', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'           => ['required', 'string', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'company_name'    => ['required', 'string', 'max:160'],
            'gst_number'      => ['nullable', 'string', 'max:30'],
            'pan_number'      => ['nullable', 'string', 'max:20'],
            'address'         => ['nullable', 'string', 'max:500'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'aadhar_number'   => ['nullable', 'string', 'max:20'],
        ]);

        $user->name  = $data['name'];
        if (array_key_exists('email', $data) && $data['email']) {
            $user->email = $data['email'];
        }
        $user->phone = $data['phone'];
        $user->save();

        $detail->user_id         = $user->id;
        $detail->company_name    = $data['company_name'];
        $detail->gst_number      = $data['gst_number'] ?? $detail->gst_number;
        $detail->pan_number      = $data['pan_number'] ?? $detail->pan_number;
        $detail->address         = $data['address'] ?? $detail->address;
        $detail->alternate_phone = $data['alternate_phone'] ?? $detail->alternate_phone;
        $detail->aadhar_number   = $data['aadhar_number'] ?? $detail->aadhar_number;
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new ProfileResource($user->fresh(['getUserDetail', 'getSellerKycDetail', 'getTransporterDetail'])),
        ]);
    }
}
