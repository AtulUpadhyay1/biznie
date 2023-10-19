<!-- partial:partials/_sidebar.html -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{route('seller.dashboard')}}" class="sidebar-brand">
            <img src="{{asset('admin_css/assets/images/logo.png')}}" style="width: 70%;">
        </a>
        <div class="sidebar-toggler not-active">
            <i class="bi bi-list fs-3 text-white"></i>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item {{ isActiveRoute(['seller.dashboard']) ? 'active' : '' }}">
                <a href="{{route('seller.dashboard')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-speedometer"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

        </ul>
    </div>
</nav>
