@extends('admin.layouts.auth')

@section('title', $page_title ?? 'Sign in')
@section('heading', 'Sign in to your account')
@section('subheading', 'Enter your admin credentials to open the Biznie console.')

@section('form')
    <form method="POST" action="{{ route('admin.login') }}" novalidate>
        @csrf

        <div class="bz-auth__field">
            <label class="form-label" for="email">Email address</label>
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

        <div class="bz-auth__field">
            <label class="form-label" for="password">Password</label>
            <div class="bz-auth__control">
                <i class="bi bi-lock bz-auth__icon"></i>
                <input type="password" id="password" name="password"
                    class="form-control has-toggle @error('password') is-invalid @enderror"
                    placeholder="Enter your password" autocomplete="current-password" required>
                <button type="button" class="bz-auth__toggle" data-bz-toggle-password="password"
                    aria-label="Show password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <span class="bz-auth__error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="bz-auth__row">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" id="remember" name="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>
            <a class="bz-auth__link" href="{{ route('admin.password.request') }}">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-danger bz-auth__submit">
            Sign in <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection

@section('foot')
    Trouble signing in? Contact your system administrator.
@endsection
