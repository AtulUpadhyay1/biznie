<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{config('app.name')}} | {{isset($page_title) ? $page_title : ""}}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap" rel="stylesheet">
        <!-- End fonts -->
        <!-- core:css -->
        <link rel="stylesheet" href="{{asset('admin/assets/vendors/core/core.css')}}">
        <!-- Layout styles -->
        @if(session()->has('selected_theme') && session()->get('selected_theme') == "Dark")
            <link rel="stylesheet" href="{{asset('admin/assets/css/demo2/style.min.css')}}">
        @else
            <link rel="stylesheet" href="{{asset('admin/assets/css/demo2/style.min.css')}}">
        @endif
        <!-- End layout styles -->
        <link rel="stylesheet" href="{{asset('admin/assets/vendors/sweetalert2/sweetalert2.min.css')}}">
        {{-- @if(websiteSetupValue('favicon'))
            <link rel="shortcut icon" href="{{asset('admin/admin/website_setup/'.websiteSetupValue('favicon'))}}" />
        @else
            <link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.png')}}" />
        @endif --}}
        <link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.png')}}" />

    </head>

    <body>
        <div class="main-wrapper">
            <div class="page-wrapper full-page">
                <div class="page-content d-flex align-items-center justify-content-center">
                    <div class="row w-75 mx-0 auth-page">
                        <div class="col-md-8 col-xl-6 mx-auto">
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-12 ps-md-0">
                                        <div class="auth-form-wrapper px-4 py-5">
                                            <a href="#" class="noble-ui-logo logo-light d-block mb-2 text-center">
                                                {{-- @if(websiteSetupValue('logo'))
                                                    <img src="{{asset('admin/admin/website_setup/'.websiteSetupValue('logo'))}}" class="img-fluid"  style="height: 40px">
                                                @else
                                                    <h4> {{config('app.name')}} </h4>
                                                @endif --}}
                                                <h4> {{config('app.name')}} </h4>
                                            </a>
                                            <h5 class="text-muted fw-normal mb-4 text-center">Welcome back! Log in to your account.</h5>
                                            <form class="forms-sample" method="POST" action="{{ route('admin.login') }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" autocomplete="current-password" name="password" placeholder="Password" required>
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                                        Login
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{asset('admin/assets/vendors/core/core.js')}}"></script>
        <script src="{{asset('admin/assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
        <script>
            $(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });

                $( document ).ready(function() {
                    var success_message = "{{Session::get('success')}}";
                    var error_message = "{{Session::get('error')}}";

                    if(success_message != ""){
                        success_sweet_alert(success_message);
                    }
                    if(error_message !=""){
                        error_sweet_alert(error_message)
                    }

                });

                function success_sweet_alert(success_message){
                    Toast.fire({
                        icon: 'success',
                        title: success_message
                    });
                }

                function error_sweet_alert(error_message){
                    Toast.fire({
                        icon: 'error',
                        title: error_message
                    });
                }
            });
        </script>
    </body>
</html>
