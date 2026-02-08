<?php

namespace App\Livewire\Admin\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use App\Mail\EmailOtp;

class Edit extends Component
{
    public $name;
    public $email;
    public $phone;

    // Password fields
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // OTP fields
    public $otp;
    public $showOtpField = false;
    public $otpSent = false;

    public $showPasswordSection = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ];

        if ($this->showPasswordSection) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = ['required', 'string', Password::min(8), 'confirmed'];

            if ($this->showOtpField) {
                $rules['otp'] = 'required|string|size:6';
            }
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already taken.',
        'current_password.required' => 'Current password is required.',
        'new_password.required' => 'New password is required.',
        'new_password.min' => 'New password must be at least 8 characters.',
        'new_password.confirmed' => 'Password confirmation does not match.',
        'otp.required' => 'OTP is required.',
        'otp.size' => 'OTP must be 6 digits.',
    ];

    public function mount()
    {
        $admin = Auth::user();
        $this->name = $admin->name;
        $this->email = $admin->email;
        $this->phone = $admin->phone;
    }

    public function togglePasswordSection()
    {
        $this->showPasswordSection = !$this->showPasswordSection;

        // Reset password fields when hiding
        if (!$this->showPasswordSection) {
            $this->reset(['current_password', 'new_password', 'new_password_confirmation', 'otp', 'showOtpField', 'otpSent']);
            $this->resetValidation(['current_password', 'new_password', 'new_password_confirmation', 'otp']);
        }
    }

    public function updateProfile()
    {
        $this->validate();

        $admin = Auth::user();

        // If password section is shown, verify current password first
        if ($this->showPasswordSection) {
            if (!Hash::check($this->current_password, $admin->password)) {
                $this->addError('current_password', 'The current password is incorrect.');
                return;
            }

            // Check if recovery email is set
            if (empty($admin->recovery_email)) {
                $this->addError('current_password', 'Recovery email is not set. Please set a recovery email before changing password.');
                return;
            }

            // If OTP not sent yet, send OTP and show OTP field
            if (!$this->otpSent) {
                $this->sendOtpToRecoveryEmail($admin);
                return;
            }

            // If OTP is sent, verify it
            if ($this->showOtpField) {
                if (!$this->verifyOtp($admin)) {
                    return;
                }
                // OTP verified, proceed with password update
            }
        }

        // Update profile information
        $admin->name = $this->name;
        $admin->email = $this->email;
        $admin->phone = $this->phone;

        // Update password if provided and OTP verified
        if ($this->showPasswordSection && $this->new_password && $this->otpSent) {
            $admin->password = Hash::make($this->new_password);
            // Clear OTP fields after password update
            $admin->password_reset_otp = null;
            $admin->password_reset_otp_expires_at = null;
        }

        $admin->save();

        // Reset password and OTP fields
        if ($this->showPasswordSection) {
            $this->reset(['current_password', 'new_password', 'new_password_confirmation', 'otp', 'showOtpField', 'otpSent']);
            $this->showPasswordSection = false;
        }

        // Dispatch alert event
        $this->dispatch('alert', type: 'success', message: 'Profile updated successfully!');
    }

    protected function sendOtpToRecoveryEmail($admin)
    {
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));

        // Save OTP to database with 10 minutes expiry
        $admin->password_reset_otp = $otp;
        $admin->password_reset_otp_expires_at = now()->addMinutes(10);
        $admin->save();

        // Send OTP to recovery email
        try {
            Mail::to($admin->recovery_email)->send(new EmailOtp(
                $otp,
                $admin->name,
                $admin->recovery_email,
                10,
                'Password Change Verification - ' . config('app.name', 'Biznie')
            ));

            $this->otpSent = true;
            $this->showOtpField = true;

            $this->dispatch('alert', type: 'success', message: 'OTP has been sent to your recovery email: ' . $this->maskEmail($admin->recovery_email));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Failed to send OTP. Please try again.');
        }
    }

    protected function verifyOtp($admin)
    {
        // Check if OTP is expired
        if ($admin->password_reset_otp_expires_at < now()) {
            $this->addError('otp', 'OTP has expired. Please request a new one.');
            return false;
        }

        // Check if OTP matches
        if ($this->otp !== $admin->password_reset_otp) {
            $this->addError('otp', 'Invalid OTP. Please try again.');
            return false;
        }

        return true;
    }

    public function resendOtp()
    {
        $admin = Auth::user();

        if (empty($admin->recovery_email)) {
            $this->dispatch('alert', type: 'error', message: 'Recovery email is not set.');
            return;
        }

        $this->sendOtpToRecoveryEmail($admin);
    }

    protected function maskEmail($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);

        return $maskedName . '@' . $domain;
    }

    public function render()
    {
        return view('livewire.admin.profile.edit')->layout('admin.layouts.app');
    }
}
