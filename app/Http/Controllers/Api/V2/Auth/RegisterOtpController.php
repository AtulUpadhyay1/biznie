<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Controllers\Controller;
use App\Mail\V2RegisterOtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class RegisterOtpController extends Controller
{
    private const TTL_MINUTES   = 10;
    private const RESEND_SECONDS = 60;
    private const MAX_ATTEMPTS  = 5;

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
        ]);

        $email     = strtolower(trim($data['email']));
        $throttleK = $this->throttleKey($email);

        if (Cache::has($throttleK)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a minute before requesting another code.',
            ], 429);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put($this->otpKey($email), [
            'otp'      => $otp,
            'attempts' => 0,
        ], now()->addMinutes(self::TTL_MINUTES));

        Cache::put($throttleK, 1, now()->addSeconds(self::RESEND_SECONDS));

        try {
            Mail::to($email)->send(new V2RegisterOtpMail(
                otp: $otp,
                expiresInMinutes: self::TTL_MINUTES,
            ));
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Could not send verification email. Please try again shortly.',
            ], 500);
        }

        return response()->json([
            'success'           => true,
            'message'           => 'Verification code sent to your email.',
            'expires_in_minutes' => self::TTL_MINUTES,
        ]);
    }

    /**
     * Verify an OTP without consuming it. Used during the register-form submit
     * before user creation; consumeOtp() is called inside the same request.
     *
     * @return array{ok: bool, message?: string}
     */
    public static function verifyOtp(string $email, string $otp): array
    {
        $email = strtolower(trim($email));
        $key   = "v2:register-otp:{$email}";
        $rec   = Cache::get($key);

        if (! $rec) {
            return ['ok' => false, 'message' => 'Verification code expired. Please request a new one.'];
        }

        if (($rec['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            Cache::forget($key);
            return ['ok' => false, 'message' => 'Too many invalid attempts. Please request a new code.'];
        }

        if (! hash_equals((string) $rec['otp'], (string) $otp)) {
            $rec['attempts'] = ($rec['attempts'] ?? 0) + 1;
            Cache::put($key, $rec, now()->addMinutes(self::TTL_MINUTES));
            return ['ok' => false, 'message' => 'Invalid verification code.'];
        }

        return ['ok' => true];
    }

    public static function consumeOtp(string $email): void
    {
        $email = strtolower(trim($email));
        Cache::forget("v2:register-otp:{$email}");
        Cache::forget("v2:register-otp-throttle:{$email}");
    }

    private function otpKey(string $email): string
    {
        return "v2:register-otp:{$email}";
    }

    private function throttleKey(string $email): string
    {
        return "v2:register-otp-throttle:{$email}";
    }
}
