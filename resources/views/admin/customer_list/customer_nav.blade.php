<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    <div class="card">
        <div class="card-header customer-profile-header">
            <div class="text-center">
                <img src="{{asset('admin_css/assets/images/avatar.png')}}" alt="" class="w-25 h-25">
                <h5 class="mt-3"> {{ $data->name }} </h5>
                <small>( {{ $data->getUserDetail ? $data->getUserDetail->priority : 0 }} ⭐ )</small>
            </div>
        </div>
        <div class="card-body">
            <ul class="custom-un-li">
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.customer-profile' || Route::currentRouteName()=='admin.edit-customer-info') nav_active @endif"
                        href="{{route('admin.customer-profile', $data->id)}}" wire:navigate>
                        Profile
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.customer-orders-list') nav_active @endif" href="{{route('admin.customer-orders-list', $data->id)}}" wire:navigate>Order List
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
                <li class="nav-item">
                    <a class="nav-link @if(Route::currentRouteName()=='admin.customer-staff-list') nav_active @endif" href="{{route('admin.customer-staff-list', $data->id)}}" wire:navigate>Staff List
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
