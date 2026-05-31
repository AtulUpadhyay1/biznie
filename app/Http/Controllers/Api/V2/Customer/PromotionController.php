<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProfileResource;
use App\Models\Business;
use App\Models\SellerKycDetail;
use App\Models\SellerType;
use App\Models\TransporterDetail;
use App\Models\UserDetail;
use App\Models\UserPromotionHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function sellerTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => SellerType::active()->get(['id', 'name']),
        ]);
    }

    public function becomeSeller(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->type === 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'You are already a seller.',
            ], 400);
        }

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'email'           => ['nullable', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'company_name'    => ['required', 'string', 'max:160'],
            'gst_number'      => ['required', 'string', 'max:30'],
            'pan_number'      => ['nullable', 'string', 'max:20'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'seller_type'     => ['nullable', 'array'],
            'seller_type.*'   => ['integer', 'min:1'],
        ]);

        $user->name = $data['name'];
        if (! empty($data['email'])) {
            $user->email = $data['email'];
        }
        $user->type = 'seller';
        $user->save();

        $log = new UserPromotionHistory();
        $log->user_id  = $user->id;
        $log->old_type = 'customer';
        $log->new_type = 'seller';
        $log->save();

        $business = Business::where('user_id', $user->id)->first() ?: new Business();
        $business->user_id     = $user->id;
        $business->name        = $data['company_name'];
        $business->seller_type = $data['seller_type'] ?? $business->seller_type;
        $business->save();

        $kyc = SellerKycDetail::where('user_id', $user->id)->first() ?: new SellerKycDetail();
        $kyc->user_id         = $user->id;
        $kyc->identity_type   = 'pan';
        $kyc->identity_number = $data['pan_number'] ?? $kyc->identity_number;
        $kyc->gst_number      = $data['gst_number'];
        $kyc->address         = $data['company_address'] ?? $kyc->address;
        if (! $kyc->status) {
            $kyc->status = 'pending';
        }
        $kyc->save();

        $detail = UserDetail::where('user_id', $user->id)->first() ?: new UserDetail();
        $detail->user_id      = $user->id;
        $detail->company_name = $data['company_name'];
        $detail->gst_number   = $data['gst_number'];
        if (! empty($data['pan_number'])) {
            $detail->pan_number = $data['pan_number'];
        }
        if (! empty($data['company_address'])) {
            $detail->company_address = $data['company_address'];
        }
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Congratulations, you are now a seller.',
            'data'    => new ProfileResource($user->fresh(['getUserDetail', 'getSellerKycDetail', 'getTransporterDetail'])),
        ]);
    }

    public function becomeTransporter(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->type === 'transporter') {
            return response()->json([
                'success' => false,
                'message' => 'You are already a transporter.',
            ], 400);
        }

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'phone'           => ['required', 'string', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'company_name'    => ['required', 'string', 'max:160'],
            'gst_number'      => ['nullable', 'string', 'max:30'],
            'address'         => ['nullable', 'string', 'max:500'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'aadhar_number'   => ['nullable', 'string', 'max:20'],
        ]);

        $user->name  = $data['name'];
        $user->phone = $data['phone'];
        $user->type  = 'transporter';
        $user->save();

        $log = new UserPromotionHistory();
        $log->user_id  = $user->id;
        $log->old_type = 'customer';
        $log->new_type = 'transporter';
        $log->save();

        $detail = TransporterDetail::where('user_id', $user->id)->first() ?: new TransporterDetail();
        $detail->user_id         = $user->id;
        $detail->company_name    = $data['company_name'];
        $detail->gst_number      = $data['gst_number'] ?? $detail->gst_number;
        $detail->address         = $data['address'] ?? $detail->address;
        $detail->alternate_phone = $data['alternate_phone'] ?? $detail->alternate_phone;
        $detail->aadhar_number   = $data['aadhar_number'] ?? $detail->aadhar_number;
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Congratulations, you are now a transporter.',
            'data'    => new ProfileResource($user->fresh(['getUserDetail', 'getSellerKycDetail', 'getTransporterDetail'])),
        ]);
    }
}
