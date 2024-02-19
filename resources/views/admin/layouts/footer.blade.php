<script src="{{asset('admin_css/assets/vendors/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('admin_css/assets/js/template.js')}}"></script>
<script src="{{asset('admin_css/assets/js/dashboard-light.js')}}"></script>

<script src="{{asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{asset('admin_css/assets/js/select2.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/jquery-tags-input/jquery.tagsinput.min.js')}}"></script>
<script src="{{asset('admin_css/assets/js/tags-input.js')}}"></script>
<script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    window.addEventListener('alert',({detail:{type, message}})=>{
        Toast.fire({
            icon: type,
            title: message,
            showCloseButton: true,
        });
    })


</script>

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
                title: success_message,
                showCloseButton: true,
            });
        }

        function error_sweet_alert(error_message){
            Toast.fire({
                icon: 'error',
                title: error_message,
                showCloseButton: true,
            });
        }
    });
</script>
