<div>
    @section('title', config('app.name') . ' | Edit Profile')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Edit Profile</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-secondary btn-sm btn-icon-text float-end align-items-center" title="Back"
                                href="{{ route('admin.dashboard') }}" wire:navigate>
                                <i class="bi bi-arrow-left btn-icon-prepend"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="updateProfile">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" wire:model="name" placeholder="Enter your name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" wire:model="email" placeholder="Enter your email">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" wire:model="phone" placeholder="Enter your phone number">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Recovery Email</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" class="form-control"
                                        value="{{ auth('admin')->user()->recovery_email ?? 'Not Set' }}"
                                        disabled readonly>
                                    <a href="{{ route('admin.recovery-email.change') }}"
                                        class="btn btn-sm btn-secondary text-nowrap"
                                        title="Change Recovery Email">
                                        <i class="bi bi-pencil-square"></i> Change
                                    </a>
                                </div>
                                <small class="text-muted">Used for password reset and account recovery</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-outline-primary d-block"
                                    wire:click="togglePasswordSection">
                                    @if ($showPasswordSection)
                                        <i class="bi bi-eye-slash me-1"></i>Hide Password Section
                                    @else
                                        <i class="bi bi-key me-1"></i>Change Password
                                    @endif
                                </button>
                            </div>
                        </div>

                        @if ($showPasswordSection)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info" role="alert">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Password Requirements:</strong> Minimum 8 characters
                                        @if(!$otpSent)
                                            <br><strong>Note:</strong> An OTP will be sent to your recovery email to verify this change.
                                        @endif
                                    </div>
                                </div>

                                @if(!$showOtpField)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="current_password">Current Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" wire:model="current_password"
                                            placeholder="Enter current password">
                                        @error('current_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="new_password">New Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password"
                                            class="form-control @error('new_password') is-invalid @enderror"
                                            id="new_password" wire:model="new_password" placeholder="Enter new password">
                                        @error('new_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="new_password_confirmation">Confirm New Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            wire:model="new_password_confirmation" placeholder="Confirm new password">
                                    </div>
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-success" role="alert">
                                            <i class="bi bi-envelope-check me-2"></i>
                                            OTP has been sent to your recovery email. Please check your inbox.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="otp">Enter OTP <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('otp') is-invalid @enderror"
                                            id="otp" wire:model="otp"
                                            placeholder="Enter 6-digit OTP"
                                            maxlength="6"
                                            pattern="[0-9]*"
                                            inputmode="numeric">
                                        @error('otp')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <small class="text-muted">OTP expires in 10 minutes</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                wire:click="resendOtp">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Resend OTP
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                @if($showPasswordSection && !$showOtpField)
                                    <x-submit-btn text="Send OTP to Recovery Email" />
                                @elseif($showPasswordSection && $showOtpField)
                                    <x-submit-btn text="Verify OTP & Update Password" />
                                @else
                                    <x-submit-btn text="Update Profile" />
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
