<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Mail\V2NewUserRegisteredAdminMail;
use App\Mail\V2WelcomeUserMail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V2\Auth\RegisterOtpController;
use App\Http\Resources\V2\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'regex:/^\d{10}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'otp'      => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $verify = RegisterOtpController::verifyOtp($data['email'], $data['otp']);
        if (! $verify['ok']) {
            return response()->json([
                'success' => false,
                'message' => $verify['message'] ?? 'Invalid verification code.',
                'errors'  => ['otp' => [$verify['message'] ?? 'Invalid verification code.']],
            ], 422);
        }

        $user = new User();
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->phone    = $data['phone'] ?? null;
        $user->password = $data['password']; // hashed via $casts
        $user->type     = 'customer';
        $user->status   = 'active';
        $user->email_verified_at = now();
        $user->save();

        RegisterOtpController::consumeOtp($data['email']);
        $this->sendRegistrationEmails($user);

        $token = $user->createToken('biznie-next')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        $user->load(['getBusiness', 'getUserDetail', 'getTransporterDetail', 'getAddedBy.getBusiness']);

        $token = $user->createToken('biznie-next')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['getBusiness', 'getUserDetail', 'getTransporterDetail', 'getAddedBy.getBusiness']);

        return response()->json([
            'success' => true,
            'user'    => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out.',
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices.',
        ]);
    }

    private function sendRegistrationEmails(User $user): void
    {
        $adminEmail = (string) config('mail.admin_contact_address', 'contact@biznie.com');

        if ($adminEmail !== '') {
            try {
                Mail::to($adminEmail)->send(new V2NewUserRegisteredAdminMail($user));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        try {
            Mail::to($user->email)->send(new V2WelcomeUserMail($user->name));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
