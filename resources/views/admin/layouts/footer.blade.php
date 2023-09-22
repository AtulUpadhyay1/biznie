<!-- Plugin js for this page -->
<script src="{{asset('admin_css/assets/vendors/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/apexcharts/apexcharts.min.js')}}"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="{{asset('admin_css/assets/vendors/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('admin_css/assets/js/template.js')}}"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="{{asset('admin_css/assets/js/dashboard-light.js')}}"></script>
<!-- End custom js for this page -->

{{-- <!-- Plugin js for this page -->
<script src="{{asset('admin_css/assets/vendors/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/inputmask/jquery.inputmask.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/jquery-tags-input/jquery.tagsinput.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/moment/moment.min.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/easymde/easymde.min.js')}}"></script>
<script src="{{asset('admin_css/assets/js/easymde.js')}}"></script>

<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="{{asset('admin_css/assets/vendors/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('admin_css/assets/js/template.js')}}"></script>
<!-- endinject -->

<!-- Custom js for this page -->
@if(session()->has('selected_theme') && session()->get('selected_theme') == "Dark")
    <script src="{{asset('admin_css/assets/js/dashboard-dark.js')}}"></script>
@else
    <script src="{{asset('admin_css/assets/js/dashboard-light.js')}}"></script>
@endif

<script src="{{asset('admin_css/assets/js/sweet-alert.js')}}"></script>

<script src="{{asset('admin_css/assets/js/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('admin_css/assets/js/inputmask.js')}}"></script>
<script src="{{asset('admin_css/assets/js/select2.js')}}"></script>
<script src="{{asset('admin_css/assets/js/tags-input.js')}}"></script>
<script src="{{asset('admin_css/assets/js/flatpickr.js')}}"></script>
<!-- End custom js for this page -->

<script src="{{asset('admin_css/assets/vendors/datatables.net/jquery.dataTables.js')}}"></script>
<script src="{{asset('admin_css/assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js')}}"></script>
<script src="{{asset('admin_css/assets/js/data-table.js')}}"></script>

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

        $('#valid_form').validate({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
<script>
    $('.deleteBtn').click(function(event) {
        var form =  $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger me-2'
            },
            buttonsStyling: false,
        })

        swalWithBootstrapButtons.fire({
            title: "{{trans('lang.are_you_confirm')}}",
            text: "{{trans('lang.not_able_to_revert')}}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{trans('lang.yes_delete')}}",
            cancelButtonText: "{{trans('lang.no_cancel')}}",
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                form.submit();
                // swalWithBootstrapButtons.fire(
                //     'Deleted!',
                //     'Your file has been deleted.',
                //     'success'
                // )
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    "{{trans('lang.cancelled')}}",
                    "{{trans('lang.data_is_safe')}}",
                    'error'
                )
            }
        })
    });
</script> --}}
