<?php

namespace App\Livewire\Admin\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Edit extends Component
{
    public $name;
    public $email;
    public $phone;
    
    // Password fields
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    
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
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
            $this->resetValidation(['current_password', 'new_password', 'new_password_confirmation']);
        }
    }

    public function updateProfile()
    {
        $this->validate();

        $admin = Auth::user();

        // If password section is shown, verify current password
        if ($this->showPasswordSection) {
            if (!Hash::check($this->current_password, $admin->password)) {
                $this->addError('current_password', 'The current password is incorrect.');
                return;
            }
        }

        // Update profile information
        $admin->name = $this->name;
        $admin->email = $this->email;
        $admin->phone = $this->phone;

        // Update password if provided
        if ($this->showPasswordSection && $this->new_password) {
            $admin->password = Hash::make($this->new_password);
        }

        $admin->save();

        // Reset password fields
        if ($this->showPasswordSection) {
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
            $this->showPasswordSection = false;
        }

        // Dispatch alert event
        $this->dispatch('alert', type: 'success', message: 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.profile.edit')->layout('admin.layouts.app');
    }
}
