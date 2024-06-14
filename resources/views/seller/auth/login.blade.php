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
        <link rel="stylesheet" href="{{asset('seller_css/assets/css/demo2/custom.min.css')}}">
        <!--Bootstrap icons-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- core:css -->
        <link rel="stylesheet" href="{{asset('seller_css/assets/vendors/core/core.css')}}">
        <!-- Layout styles -->
        <link rel="stylesheet" href="{{asset('seller_css/assets/css/demo2/style.min.css')}}">
        <!-- End layout styles -->
        <link rel="stylesheet" href="{{asset('seller_css/assets/vendors/sweetalert2/sweetalert2.min.css')}}">
        {{-- @if(websiteSetupValue('favicon'))
            <link rel="shortcut icon" href="{{asset('admin/admin/website_setup/'.websiteSetupValue('favicon'))}}" />
        @else
            <link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.png')}}" />
        @endif --}}
        <link rel="shortcut icon" href="{{asset('seller_css/assets/images/favicon.png')}}" />
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body>
        <div class="main-wrapper">
            <div class="page-wrapper full-page login-page-wrapper">
                <img src="{{asset('seller_css/assets/images/wave.png')}}" class="wave" style="filter: invert(1);">
                <div class="container">
                    <div class="bg-img">
                        <img src="{{asset('seller_css/assets/images/bg.png')}}" style="filter: drop-shadow(2px 4px 6px #33ffff);">
                    </div>
                    <div class="login-content">
                        <div>
                            <img src="{{asset('seller_css/assets/images/seller.png')}}">
                            <h2 class="admin-title">Welcome Seller </h2>
                            <form class="admin-login-form forms-sample" method="POST" action="{{ route('seller.login') }}">
                                @csrf
                                <div class="input-area one">
                                    <div class="input-icon">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="input-text-area">
                                        <input type="email" class="input @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Username">
                                    </div>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="input-area pass" x-data="{ showPassword: false }">
                                    <div class="input-icon">
                                        <i x-bind:class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'" x-bind:title="showPassword ? 'Hide Password' : 'Show Password'" x-on:click="showPassword = ! showPassword"></i>
                                    </div>
                                    <div class="input-text-area">
                                        <input x-bind:type="showPassword ? 'text' : 'password'" class="input @error('password') is-invalid @enderror" id="password" autocomplete="current-password" name="password" placeholder="Password">
                                    </div>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <a href="#">Forgot Password?</a>
				                <input type="submit" class="submit-btn" value="Login">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="{{asset('seller_css/assets/vendors/core/core.js')}}"></script>
        <script src="{{asset('seller_css/assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
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
