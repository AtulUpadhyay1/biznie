<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title',config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap"
        rel="stylesheet">

    <!--Font awesome icons-->
    <script src="https://use.fontawesome.com/80ace2cf8a.js"></script>
    <!-- End fonts -->
    <meta name="keywords"
        content="Biznie, bootstrap, bootstrap 5, bootstrap5, admin_css, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    {{-- <!-- core:css -->
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/core/core.css')}}">
        <!-- endinject -->

        <!-- Plugin css for this page -->
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/select2/select2.min.css')}}">
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/jquery-tags-input/jquery.tagsinput.min.css')}}">
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/flatpickr/flatpickr.min.css')}}">
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.css')}}">
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/easymde/easymde.min.css')}}">
        <!-- End plugin css for this page -->

        <!-- inject:css -->
        <link rel="stylesheet" href="{{asset('admin_css/assets/fonts/feather-font/css/iconfont.css')}}">
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
        <!-- endinject -->

        <!-- Layout styles -->

        @if (session()->has('selected_theme') && session()->get('selected_theme') == 'Dark')
            <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/style.min.css')}}">
        @else
            <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo1/style.min.css')}}">
        @endif
        <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/style.min.css')}}">
        <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/custom.min.css')}}">
        <!-- End layout styles -->

        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css')}}">

        @if (websiteSetupValue('favicon'))
            <link rel="shortcut icon" href="{{asset('admin_css/admin_css/website_setup/'.websiteSetupValue('favicon'))}}" />
        @else
            <link rel="shortcut icon" href="{{asset('admin_css/assets/images/favicon.png')}}" />
        @endif

        <link rel="shortcut icon" href="{{asset('admin_css/assets/images/favicon.png')}}" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap"
        rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/core/core.css')}}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/flatpickr/flatpickr.min.css')}}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{asset('admin_css/assets/fonts/feather-font/css/iconfont.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/style.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/custom.min.css')}}">
    <!-- End layout styles -->

    <!--Bootstrap icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!--End of bootstrap icons-->

    <!-- core:js -->
    <script src="{{asset('admin_css/assets/vendors/core/core.js')}}"></script>
    <!-- endinject -->
    @livewireStyles
</head>

<body>
    <div class="main-wrapper">

        @include('admin.layouts.sidebar')

        <div class="page-wrapper">

            @include('admin.layouts.navbar')

            <div class="page-content">
                {{ $slot }}
                {{-- @yield('content') --}}
            </div>

            <footer
                class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
                <p class="text-muted mb-1 mb-md-0">Copyright © {{ date('Y') }} <a
                        href="https://www.techuptechnologies.com/" target="_blank">Techup Technologies</a>.</p>
            </footer>

        </div>

    </div>
    @include('admin.layouts.footer')

    @stack('scripts')
    @livewireScripts
</body>

</html>
