<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{asset('admin_css/assets/images/favicon.png')}}">

    <title>@yield('title',config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet">

    <script src="https://use.fontawesome.com/80ace2cf8a.js"></script>

    <meta name="keywords"
        content="Biznie">
    <meta name="theme-color" content="#0F172A">

    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/core/core.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/fonts/feather-font/css/iconfont.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/style.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/custom.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/jquery-tags-input/jquery.tagsinput.min.css')}}">

    {{-- Biznie theme layer — must stay last so it wins the cascade --}}
    <link rel="stylesheet" href="{{asset('admin_css/assets/css/biznie-admin.css')}}?v=15">

    <!-- core:js -->
    <script src="{{asset('admin_css/assets/vendors/core/core.js')}}"></script>
    <!-- endinject -->

    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="module" src="{{ asset('firebase/app.js') }}"></script>
    @livewireStyles
</head>

<body>
    {{-- Restore the folded sidebar before the shell paints, so there is no layout flash --}}
    <script>
        try {
            if (localStorage.getItem('bz:sidebar-folded') === '1' && window.matchMedia('(min-width: 992px)').matches) {
                document.body.classList.add('sidebar-folded');
            }
        } catch (e) {}
    </script>

    <div id="bz-progress"></div>

    <div class="main-wrapper">

        @include('admin.layouts.sidebar')

        <div class="page-wrapper">

            @include('admin.layouts.navbar')

            <div class="page-content">
                {{ $slot }}
                {{-- @yield('content') --}}
                <livewire:Admin.Notification.NotificationModel />
            </div>

            <footer
                class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
                <p class="text-muted mb-1 mb-md-0">Copyright © {{ date('Y') }} <a
                        href="https://biznie.com/" target="_blank">Biznie TradeTech LLP</a>.</p>
            </footer>

        </div>

    </div>
    @include('admin.layouts.footer')

    @stack('scripts')
    @livewireScripts
</body>

</html>
