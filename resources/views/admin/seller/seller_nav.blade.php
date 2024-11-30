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
                <h5 class="text-dark mt-3"> {{ $data->name }}
                    @if ($data->getSellerKycDetail)
                        @if ($data->getSellerKycDetail->status == 'uploaded' || $data->getSellerKycDetail->status == 'pending')
                            <i class="bi bi-stopwatch-fill text-warning"></i>
                        @elseif ($data->getSellerKycDetail->status == 'approved')
                            <i class="bi bi-patch-check-fill text-success"></i>
                        @elseif (($data->getSellerKycDetail->status == 'rejected'))
                            <i class="bi bi-x-circle-fill text-danger"></i>
                        @endif
                    @endif
                </h5>
            </div>
        </div>
        <div class="card-body">
            <ul class="custom-un-li">
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.seller-kyc-detail') nav_active @endif"
                        href="{{route('admin.seller-kyc-detail', $data->id)}}" wire:navigate>
                        Seller Kyc Detail
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.seller.orders') nav_active @endif" href="{{route('admin.seller.orders', $data->id)}}" wire:navigate>Order List
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.customer-payment-list') nav_active @endif" href="{{route('admin.customer-payment-list', $data->id)}}" wire:navigate>Payment History
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.customer-enquiry-list') nav_active @endif" href="{{route('admin.customer-enquiry-list', $data->id)}}" wire:navigate>RFQ
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
