<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\UserResource;
use App\Models\User;
use App\Models\UserDetail;
use App\Rules\Gstin;
use App\Rules\IndianMobile;
use App\Services\Msg91OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Sign in or sign up with a mobile number and an SMS code (4.7).
 *
 *   POST auth/otp/send    {phone}                         -> is_registered
 *   POST auth/otp/verify  {phone, otp}                    -> token (existing)
 *   POST auth/otp/verify  {phone, otp, name, role, ...}   -> token (new account)
 *
 * The code is 6 digits, kept hashed in the cache for 10 minutes, allows five
 * tries and one resend a minute. Unlike the legacy app flow there is no
 * "master OTP" that opens any account.
 */
class PhoneOtpController extends Controller
{
    private const TTL_MINUTES    = 10;
    private const RESEND_SECONDS = 60;
    private const MAX_ATTEMPTS   = 5;

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate(['phone' => ['required', new IndianMobile]]);
        $phone = trim((string) $data['phone']);

        $user = User::where('phone', $phone)->first();
        if ($user && $user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Your account has been deactivated.'], 403);
        }

        if (Cache::has($this->throttleKey($phone))) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a minute before requesting another code.',
            ], 429);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put($this->otpKey($phone), ['hash' => Hash::make($otp), 'attempts' => 0], now()->addMinutes(self::TTL_MINUTES));
        Cache::put($this->throttleKey($phone), 1, now()->addSeconds(self::RESEND_SECONDS));

        if (Msg91OtpService::sendSms('91' . $phone, ['type' => 'Login', 'otp' => $otp]) === null) {
            Cache::forget($this->otpKey($phone));
            Cache::forget($this->throttleKey($phone));

            return response()->json([
                'success' => false,
                'message' => 'Could not send the code right now. Please try again shortly.',
            ], 503);
        }

        return response()->json(array_filter([
            'success'            => true,
            'message'            => 'Code sent to +91 ' . substr($phone, 0, 2) . 'XXXXXX' . substr($phone, -2) . '.',
            'is_registered'      => (bool) $user,
            'expires_in_minutes' => self::TTL_MINUTES,
            // Local and test runs send no SMS; hand the code back there only.
            'debug_otp'          => app()->environment('local', 'testing') ? $otp : null,
        ], fn ($v) => $v !== null));
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', new IndianMobile],
            'otp'   => ['required', 'string', 'regex:/^\d{6}$/'],
        ], ['otp.regex' => 'Enter the 6-digit code']);
        $phone = trim((string) $data['phone']);

        $check = $this->checkOtp($phone, $data['otp']);
        if ($check !== true) {
            return response()->json(['success' => false, 'message' => $check, 'errors' => ['otp' => [$check]]], 422);
        }

        $user = User::where('phone', $phone)->first();

        if (! $user) {
            // First time on this number: the sign-up details come with the code.
            $profile = $request->validate([
                'name'       => ['required', 'string', 'max:120'],
                'role'       => ['required', 'in:buyer,seller'],
                'email'      => ['nullable', 'email', 'max:160', 'unique:users,email'],
                'gst_number' => ['nullable', new Gstin],
                'company_name' => ['nullable', 'string', 'max:160'],
            ], ['role.in' => 'Choose Buyer or Seller']);

            $user = DB::transaction(function () use ($phone, $profile) {
                $user = new User();
                $user->name   = trim($profile['name']);
                $user->phone  = $phone;
                $user->email  = isset($profile['email']) ? strtolower(trim($profile['email'])) : null;
                // Everyone starts as a buyer; selling is the KYC-reviewed
                // seller application, which the "seller" choice leads to.
                $user->type   = 'customer';
                $user->status = 'active';
                $user->save();

                if (! empty($profile['gst_number']) || ! empty($profile['company_name'])) {
                    $detail = new UserDetail();
                    $detail->user_id = $user->id;
                    $detail->gst_number   = isset($profile['gst_number']) ? strtoupper($profile['gst_number']) : $detail->gst_number;
                    $detail->company_name = $profile['company_name'] ?? $detail->company_name;
                    $detail->save();
                }

                return $user;
            });

            $next = $profile['role'] === 'seller' ? '/profile/become-seller' : null;
        } elseif ($user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Your account has been deactivated.'], 403);
        }

        Cache::forget($this->otpKey($phone));
        $user->load(['getBusiness', 'getUserDetail', 'getTransporterDetail', 'getAddedBy.getBusiness']);

        return response()->json(array_filter([
            'success' => true,
            'message' => isset($next) || $user->wasRecentlyCreated ? 'Account created.' : 'Login successful.',
            'token'   => $user->createToken('biznie-next')->plainTextToken,
            'user'    => new UserResource($user),
            // Where a new seller continues: the seller application.
            'next'    => $next ?? null,
        ], fn ($v) => $v !== null), $user->wasRecentlyCreated ? 201 : 200);
    }

    /** true, or the message to show. Wrong codes count; the fifth ends it. */
    private function checkOtp(string $phone, string $otp): bool|string
    {
        $entry = Cache::get($this->otpKey($phone));
        if (! $entry) {
            return 'This code has expired. Request a new one.';
        }
        if ($entry['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget($this->otpKey($phone));

            return 'Too many wrong attempts. Request a new code.';
        }
        if (! Hash::check($otp, $entry['hash'])) {
            $entry['attempts']++;
            Cache::put($this->otpKey($phone), $entry, now()->addMinutes(self::TTL_MINUTES));

            return 'That code is not right.';
        }

        return true;
    }

    private function otpKey(string $phone): string
    {
        return "v2:phone_otp:{$phone}";
    }

    private function throttleKey(string $phone): string
    {
        return "v2:phone_otp_throttle:{$phone}";
    }
}
