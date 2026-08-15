@extends('admin.layouts.auth')

@section('title', $page_title ?? 'Verify code')
@section('heading', 'Enter your verification code')
@section('subheading', 'We sent a 6-digit code to your recovery email. It expires shortly, so use it soon.')

@section('form')
    @if (session('email'))
        <div class="bz-auth__alert bz-auth__alert--info">
            <i class="bi bi-envelope-check"></i>
            <span>Code sent for <strong>{{ session('email') }}</strong></span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.verify-otp.submit') }}" novalidate>
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">

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
            Verify code <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection

@section('foot')
    Didn't get it?
    <a class="bz-auth__link" href="{{ route('admin.password.request') }}">Request a new code</a>
@endsection
