<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\EmailOtp;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except(['logout', 'showChangeRecoveryEmailForm', 'sendRecoveryEmailOtp', 'verifyAndChangeRecoveryEmail']);
    }

    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password', ['page_title' => 'Forgot Password']);
    }

    // Send OTP to recovery email
    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin->recovery_email) {
            return redirect()->back()->withErrors(['email' => 'No recovery email set for this account. Please contact support.']);
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP with expiration (10 minutes)
        $admin->password_reset_otp = $otp;
        $admin->password_reset_otp_expires_at = now()->addMinutes(10);
        $admin->save();

        // Send OTP email
        try {
            Mail::send(new EmailOtp($otp, $admin->name, $admin->recovery_email, 10));

            return redirect()->route('admin.password.verify-otp')
                ->with('success', 'OTP has been sent to your recovery email.')
                ->with('email', $request->email);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
    }

    // Show OTP verification form
    public function showVerifyOtpForm()
    {
        if (!session('email')) {
            return redirect()->route('admin.password.request');
        }

        return view('admin.auth.verify-otp', ['page_title' => 'Verify OTP']);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
            'otp' => 'required|digits:6'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin->password_reset_otp || $admin->password_reset_otp != $request->otp) {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.'])->withInput();
        }

        if (now()->greaterThan($admin->password_reset_otp_expires_at)) {
            return redirect()->back()->withErrors(['otp' => 'OTP has expired. Please request a new one.'])->withInput();
        }

        // OTP is valid, redirect to reset password form
        return redirect()->route('admin.password.reset')
            ->with('verified_email', $request->email)
            ->with('success', 'OTP verified successfully. Please set your new password.');
    }

    // Show reset password form
    public function showResetPasswordForm()
    {
        if (!session('verified_email')) {
            return redirect()->route('admin.password.request');
        }

        return view('admin.auth.reset-password', ['page_title' => 'Reset Password']);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
            'password' => 'required|min:8|confirmed'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        // Update password and clear OTP
        $admin->password = Hash::make($request->password);
        $admin->password_reset_otp = null;
        $admin->password_reset_otp_expires_at = null;
        $admin->save();

        return redirect()->route('admin.login')
            ->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    // Show change recovery email form (for logged in admins)
    public function showChangeRecoveryEmailForm()
    {
        return view('admin.auth.change-recovery-email', ['page_title' => 'Change Recovery Email']);
    }

    // Send OTP to old recovery email for verification
    public function sendRecoveryEmailOtp(Request $request)
    {
        $request->validate([
            'new_recovery_email' => 'required|email|different:recovery_email'
        ]);

        $admin = auth('admin')->user();

        if (!$admin->recovery_email) {
            // If no recovery email exists, set it directly
            $admin->recovery_email = $request->new_recovery_email;
            $admin->save();

            return redirect()->back()->with('success', 'Recovery email has been set successfully.');
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP and pending new email
        $admin->email_change_otp = $otp;
        $admin->email_change_otp_expires_at = now()->addMinutes(10);
        $admin->pending_recovery_email = $request->new_recovery_email;
        $admin->save();

        // Send OTP to old recovery email
        try {
            Mail::send(new EmailOtp($otp, $admin->name, $admin->recovery_email, 10));

            return redirect()->back()
                ->with('success', 'OTP has been sent to your current recovery email.')
                ->with('show_otp_form', true);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to send OTP. Please try again.']);
        }
    }

    // Verify OTP and change recovery email
    public function verifyAndChangeRecoveryEmail(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $admin = auth('admin')->user();

        if (!$admin->email_change_otp || $admin->email_change_otp != $request->otp) {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.'])->withInput()
                ->with('show_otp_form', true);
        }

        if (now()->greaterThan($admin->email_change_otp_expires_at)) {
            return redirect()->back()->withErrors(['otp' => 'OTP has expired. Please request a new one.'])->withInput()
                ->with('show_otp_form', true);
        }

        // Update recovery email
        $admin->recovery_email = $admin->pending_recovery_email;
        $admin->email_change_otp = null;
        $admin->email_change_otp_expires_at = null;
        $admin->pending_recovery_email = null;
        $admin->save();

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Recovery email has been changed successfully.');
    }
}
