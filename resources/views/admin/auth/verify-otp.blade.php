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
        <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/custom.min.css')}}">
        <!--Bootstrap icons-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- core:css -->
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/core/core.css')}}">
        <!-- Layout styles -->
        <link rel="stylesheet" href="{{asset('admin_css/assets/css/demo2/style.min.css')}}">
        <!-- End layout styles -->
        <link rel="stylesheet" href="{{asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.css')}}">
        <link rel="shortcut icon" href="{{asset('admin_css/assets/images/favicon.png')}}" />
    </head>

    <body>
        <div class="main-wrapper">
            <div class="page-wrapper full-page login-page-wrapper">
                <img src="{{asset('admin_css/assets/images/wave.png')}}" class="wave">
                <div class="container">
                    <div class="bg-img">
                        <img src="{{asset('admin_css/assets/images/bg.png')}}">
                    </div>
                    <div class="login-content">
                        <div>
                            <img src="{{asset('admin_css/assets/images/avatar.png')}}">
                            <h2 class="admin-title">Verify OTP</h2>
                            <p style="color: #666; margin-bottom: 20px;">Enter the 6-digit OTP sent to your recovery email</p>

                            <form class="admin-login-form forms-sample" method="POST" action="{{ route('admin.password.verify-otp.submit') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('email') }}">

                                <div class="input-area one">
                                    <div class="input-icon">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>
                                    <div class="input-text-area">
                                        <input type="text" class="input @error('otp') is-invalid @enderror" id="otp" name="otp" value="{{ old('otp') }}" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}" required>
                                    </div>
                                </div>
                                @error('otp')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <input type="submit" class="submit-btn" value="Verify OTP">

                                <div style="margin-top: 15px;">
                                    <a href="{{ route('admin.password.request') }}" style="color: #38d39f;">Request New OTP</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{asset('admin_css/assets/vendors/core/core.js')}}"></script>
        <script src="{{asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
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
                        Toast.fire({
                            icon: 'success',
                            title: success_message
                        });
                    }
                    if(error_message !=""){
                        Toast.fire({
                            icon: 'error',
                            title: error_message
                        });
                    }
                });
            });
        </script>
    </body>
</html>
