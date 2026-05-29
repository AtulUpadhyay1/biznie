<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Controllers\Controller;
use App\Mail\V2PasswordResetMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function forgot(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // Always return the same response to avoid email enumeration.
        if ($user) {
            $this->dispatchResetEmail($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'If an account with that email exists, a reset link has been sent.',
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'token'    => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (! $record || ! Hash::check($data['token'], $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'This password reset link is invalid.',
            ], 422);
        }

        $expireMinutes = (int) (config('auth.passwords.users.expire') ?? 60);
        if (now()->diffInMinutes($record->created_at) > $expireMinutes) {
            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
            return response()->json([
                'success' => false,
                'message' => 'This password reset link has expired. Please request a new one.',
            ], 422);
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No account is associated with that email.',
            ], 404);
        }

        $user->password = $data['password']; // hashed via $casts
        $user->save();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
        // Invalidate all existing Sanctum tokens.
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset. Please sign in with your new password.',
        ]);
    }

    private function dispatchResetEmail(User $user): void
    {
        $expireMinutes = (int) (config('auth.passwords.users.expire') ?? 60);
        $throttle      = (int) (config('auth.passwords.users.throttle') ?? 60);

        $existing = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        if ($existing && now()->diffInSeconds($existing->created_at) < $throttle) {
            return; // silently throttle
        }

        $rawToken = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => Hash::make($rawToken),
                'created_at' => now(),
            ]
        );

        $frontend = rtrim((string) env('FRONTEND_URL', 'http://localhost:3000'), '/');
        $resetUrl = $frontend.'/reset-password?token='.urlencode($rawToken).'&email='.urlencode($user->email);

        try {
            Mail::to($user->email)->send(new V2PasswordResetMail(
                resetUrl: $resetUrl,
                recipientName: $user->name ?: 'there',
                expiresInMinutes: $expireMinutes,
            ));
        } catch (\Throwable $e) {
            report($e); // don't reveal mailer issues to the caller
        }
    }
}
