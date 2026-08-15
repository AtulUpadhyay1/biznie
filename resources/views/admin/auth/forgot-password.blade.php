@extends('admin.layouts.auth')

@section('title', $page_title ?? 'Forgot password')
@section('heading', 'Forgot your password?')
@section('subheading', 'Enter your admin email address and we will send a one-time code to your recovery email.')

@section('form')
    <form method="POST" action="{{ route('admin.password.send-otp') }}" novalidate>
        @csrf

        <div class="bz-auth__field">
            <label class="form-label" for="email">Admin email address</label>
            <div class="bz-auth__control">
                <i class="bi bi-envelope bz-auth__icon"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="you@company.com" autocomplete="username" autofocus required>
            </div>
            @error('email')
                <span class="bz-auth__error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger bz-auth__submit">
            Send one-time code <i class="bi bi-send"></i>
        </button>
    </form>
@endsection

@section('foot')
    <a class="bz-auth__link" href="{{ route('admin.login') }}">
        <i class="bi bi-arrow-left"></i> Back to sign in
    </a>
@endsection
