<div>
    <style>
        .card-header.customer-profile-header{
            background: url("{{asset('admin_css/assets/images/doodle.jpg')}}");
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
            border: 0;
        }
    </style>
    <div class="card ">
        <div class="card-header customer-profile-header">
            <div class="text-center">
                <img src="{{asset('admin_css/assets/images/avatar.png')}}" alt="" class="w-25 h-25">
                <h5 class="text-dark mt-3"> {{ $data->name }} </h5>
            </div>
        </div>
        <div class="card-body">
            <ul class="custom-un-li">
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.seller-kyc-detail') nav_active @endif"
                        href="{{route('admin.seller-kyc-detail', $data->id)}}"  wire:navigate>
                        Seller Kyc Detail
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
