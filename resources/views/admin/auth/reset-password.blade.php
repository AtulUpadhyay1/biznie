@extends('admin.layouts.auth')

@section('title', $page_title ?? 'Reset password')
@section('heading', 'Set a new password')
@section('subheading', 'Choose a password of at least 8 characters. You will use it the next time you sign in.')

@section('form')
    <form method="POST" action="{{ route('admin.password.reset.submit') }}" novalidate>
        @csrf
        <input type="hidden" name="email" value="{{ session('verified_email') }}">

        <div class="bz-auth__field">
            <label class="form-label" for="password">New password</label>
            <div class="bz-auth__control">
                <i class="bi bi-lock bz-auth__icon"></i>
                <input type="password" id="password" name="password"
                    class="form-control has-toggle @error('password') is-invalid @enderror"
                    placeholder="At least 8 characters" minlength="8" autocomplete="new-password" autofocus required>
                <button type="button" class="bz-auth__toggle" data-bz-toggle-password="password"
                    aria-label="Show password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <span class="bz-auth__error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="bz-auth__field">
            <label class="form-label" for="password_confirmation">Confirm new password</label>
            <div class="bz-auth__control">
                <i class="bi bi-lock-fill bz-auth__icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-control has-toggle @error('password_confirmation') is-invalid @enderror"
                    placeholder="Re-enter the password" minlength="8" autocomplete="new-password" required>
                <button type="button" class="bz-auth__toggle" data-bz-toggle-password="password_confirmation"
                    aria-label="Show password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password_confirmation')
                <span class="bz-auth__error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger bz-auth__submit mt-2">
            Reset password <i class="bi bi-check-lg"></i>
        </button>
    </form>
@endsection

@section('foot')
    <a class="bz-auth__link" href="{{ route('admin.login') }}">
        <i class="bi bi-arrow-left"></i> Back to sign in
    </a>
@endsection
