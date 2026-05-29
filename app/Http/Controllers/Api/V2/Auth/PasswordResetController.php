<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function forgot(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::broker('users')->sendResetLink(
            ['email' => $data['email']],
            function (User $user, string $token) {
                $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
                $url = $frontend.'/reset-password?token='.$token.'&email='.urlencode($user->email);

                $user->sendPasswordResetNotificationWithUrl($url);
            }
        );

        // Always respond 200 with a neutral message to prevent user enumeration.
        return response()->json([
            'success' => true,
            'message' => 'If that email exists in our system, a reset link has been sent.',
            'status'  => $status,
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::broker('users')->reset(
            $data,
            function (User $user, string $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();

                // Invalidate all existing Sanctum tokens for safety.
                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => match ($status) {
                    Password::INVALID_TOKEN => 'This reset link is invalid or has expired.',
                    Password::INVALID_USER  => 'We could not find an account with that email.',
                    default                 => 'Could not reset password. Please request a new link.',
                },
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset. You may now sign in.',
        ]);
    }
}
