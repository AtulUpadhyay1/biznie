<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#0F172A">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ config('app.name') }} | @yield('title', 'Admin')</title>

    <link rel="shortcut icon" href="{{ asset('admin_css/assets/images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('admin_css/assets/vendors/core/core.css') }}">
    {{-- Bootstrap base lives here; the theme layer only restyles it --}}
    <link rel="stylesheet" href="{{ asset('admin_css/assets/css/demo2/style.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.css') }}">

    {{-- Same token layer as the panel, so auth controls match the app exactly --}}
    <link rel="stylesheet" href="{{ asset('admin_css/assets/css/biznie-admin.css') }}?v=18">
</head>

<body>
    <div class="bz-auth">

        <aside class="bz-auth__brand">
            <div class="bz-auth__logo">
                <img src="{{ asset('admin_css/assets/images/logo-white.png') }}" alt="Biznie">
            </div>

            <div class="bz-auth__pitch">
                <h1 class="bz-auth__headline">The control room for your trading network.</h1>
                <p class="bz-auth__lede">
                    Manage sellers, buyers and transporters, keep commodity pricing current, and move
                    every enquiry through to a delivered order — from one console.
                </p>
                <ul class="bz-auth__points">
                    <li><i class="bi bi-check-lg"></i><span>Verify KYC and onboard trading partners</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Publish live commodity and freight pricing</span></li>
                    <li><i class="bi bi-check-lg"></i><span>Track enquiries, orders and settlements end to end</span></li>
                </ul>
            </div>

            <p class="bz-auth__legal">© {{ date('Y') }} Biznie TradeTech LLP. All rights reserved.</p>
        </aside>

        <main class="bz-auth__main">
            <div class="bz-auth__card">
                <div class="bz-auth__mark">
                    <img src="{{ asset('admin_css/assets/images/favicon.png') }}" alt="">
                    <span>Biznie</span>
                </div>

                <span class="bz-auth__eyebrow"><i class="bi bi-shield-lock"></i> Admin access</span>

                <h2 class="bz-auth__title">@yield('heading')</h2>
                <p class="bz-auth__sub">@yield('subheading')</p>

                @if ($errors->has('_global'))
                    <div class="bz-auth__alert bz-auth__alert--error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>{{ $errors->first('_global') }}</span>
                    </div>
                @endif

                @yield('form')

                @hasSection('foot')
                    <div class="bz-auth__foot">@yield('foot')</div>
                @endif
            </div>
        </main>

    </div>

    <script src="{{ asset('admin_css/assets/vendors/core/core.js') }}"></script>
    <script src="{{ asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        // Password visibility toggles — delegated, no framework needed.
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-bz-toggle-password]');
            if (!btn) { return; }
            var input = document.getElementById(btn.getAttribute('data-bz-toggle-password'));
            if (!input) { return; }
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });

        // Keep OTP fields numeric.
        document.addEventListener('input', function (e) {
            if (e.target.matches('.bz-auth__otp')) {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            }
        });

        (function () {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            });
            var success = @json(session('success'));
            var error = @json(session('error'));
            if (success) { Toast.fire({ icon: 'success', title: success }); }
            if (error) { Toast.fire({ icon: 'error', title: error }); }
        })();
    </script>

    @stack('scripts')
</body>

</html>
