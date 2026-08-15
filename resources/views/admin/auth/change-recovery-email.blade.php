@extends('admin.layouts.auth')

@php($bzRecovery = auth('admin')->user()->recovery_email)

@section('title', $page_title ?? 'Recovery email')
@section('heading', $bzRecovery ? 'Change recovery email' : 'Set a recovery email')
@section('subheading', 'This address receives the one-time codes used to recover your admin account.')

@section('form')
    <div class="bz-auth__alert {{ $bzRecovery ? 'bz-auth__alert--info' : 'bz-auth__alert--error' }}">
        <i class="bi {{ $bzRecovery ? 'bi-envelope-check' : 'bi-exclamation-triangle' }}"></i>
        <span>
            @if ($bzRecovery)
                Current recovery email: <strong>{{ $bzRecovery }}</strong>
            @else
                No recovery email is set yet — you will not be able to reset your password without one.
            @endif
        </span>
    </div>

    @if (!session('show_otp_form'))
        <form method="POST" action="{{ route('admin.recovery-email.send-otp') }}" novalidate>
            @csrf
            <input type="hidden" name="recovery_email" value="{{ $bzRecovery }}">

            <div class="bz-auth__field">
                <label class="form-label" for="new_recovery_email">New recovery email</label>
                <div class="bz-auth__control">
                    <i class="bi bi-envelope bz-auth__icon"></i>
                    <input type="email" id="new_recovery_email" name="new_recovery_email"
                        value="{{ old('new_recovery_email') }}"
                        class="form-control @error('new_recovery_email') is-invalid @enderror"
                        placeholder="recovery@company.com" autocomplete="email" autofocus required>
                </div>
                @error('new_recovery_email')
                    <span class="bz-auth__error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger bz-auth__submit">
                {{ $bzRecovery ? 'Send code to current email' : 'Set recovery email' }}
                <i class="bi bi-send"></i>
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.recovery-email.verify-otp') }}" novalidate>
            @csrf

            <div class="bz-auth__field">
                <label class="form-label" for="otp">6-digit code</label>
                <div class="bz-auth__control">
                    <input type="text" id="otp" name="otp" value="{{ old('otp') }}"
                        class="form-control bz-auth__otp @error('otp') is-invalid @enderror"
                        placeholder="······" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                        autocomplete="one-time-code" autofocus required>
                </div>
                @error('otp')
                    <span class="bz-auth__error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger bz-auth__submit">
                Verify and update <i class="bi bi-check-lg"></i>
            </button>
        </form>
    @endif
@endsection

@section('foot')
    <a class="bz-auth__link" href="{{ route('admin.profile.edit') }}">
        <i class="bi bi-arrow-left"></i> Back to profile
    </a>
@endsection
