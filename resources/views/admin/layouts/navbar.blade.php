<!-- partial:partials/_navbar.html -->
@php
    $bzUser = auth()->user();
    $bzRole = method_exists($bzUser, 'getRoleNames') ? ($bzUser->getRoleNames()->first() ?? 'Administrator') : 'Administrator';
    $bzInitials = collect(preg_split('/\s+/', trim($bzUser->name ?? 'A')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp
<nav class="navbar">
    <a href="#" class="sidebar-toggler" title="Toggle menu" aria-label="Toggle menu">
        <i class="bi bi-list"></i>
    </a>
    <div class="navbar-content">
        <div class="bz-topbar-title">
            <span class="bz-topbar-title__eyebrow">Biznie Admin</span>
            <span class="bz-topbar-title__main">Dashboard</span>
        </div>
        {{-- <form class="search-form">
            <div class="input-group">
                <div class="input-group-text">
                    <i class="bi bi-search"></i>
                </div>
                <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
            </div>
        </form> --}}
        <ul class="navbar-nav">
            {{-- <li class="nav-item me-3">
                <form>
                    <div class="d-flex flex-row">
                        <h6 class="me-2">Shop Status</h6>
                        <label class="switch-slider">
                            <input type="checkbox" checked>
                            <span class="switch-slider-btn round"></span>
                        </label>
                    </div>
                </form>
            </li> --}}
            {{-- <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-envelope fs-4"></i>
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
                    <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                        <p>9 New Messages</p>
                        <a href="javascript:;" class="text-muted">Clear all</a>
                    </div>
                    <div class="p-1">
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div class="me-3">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{asset('admin_css/assets/images/faces/face1.jpg')}}" alt="userr">
                            </div>
                            <div class="d-flex justify-content-between flex-grow-1">
                                <div class="me-4">
                                    <p>Leonardo Payne</p>
                                    <p class="tx-12 text-muted">Project status</p>
                                </div>
                                <p class="tx-12 text-muted">2 min ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div class="me-3">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{asset('admin_css/assets/images/faces/face1.jpg')}}" alt="userr">
                            </div>
                            <div class="d-flex justify-content-between flex-grow-1">
                                <div class="me-4">
                                    <p>Carl Henson</p>
                                    <p class="tx-12 text-muted">Client meeting</p>
                                </div>
                                <p class="tx-12 text-muted">30 min ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div class="me-3">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{asset('admin_css/assets/images/faces/face1.jpg')}}" alt="userr">
                            </div>
                            <div class="d-flex justify-content-between flex-grow-1">
                                <div class="me-4">
                                    <p>Jensen Combs</p>
                                    <p class="tx-12 text-muted">Project updates</p>
                                </div>
                                <p class="tx-12 text-muted">1 hrs ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div class="me-3">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{asset('admin_css/assets/images/faces/face1.jpg')}}" alt="userr">
                            </div>
                            <div class="d-flex justify-content-between flex-grow-1">
                                <div class="me-4">
                                    <p>Amiah Burton</p>
                                    <p class="tx-12 text-muted">Project deatline</p>
                                </div>
                                <p class="tx-12 text-muted">2 hrs ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div class="me-3">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{asset('admin_css/assets/images/faces/face1.jpg')}}" alt="userr">
                            </div>
                            <div class="d-flex justify-content-between flex-grow-1">
                                <div class="me-4">
                                    <p>Yaretzi Mayo</p>
                                    <p class="tx-12 text-muted">New record</p>
                                </div>
                                <p class="tx-12 text-muted">5 hrs ago</p>
                            </div>
                        </a>
                    </div>
                    <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                        <a href="javascript:;">View all</a>
                    </div>
                </div>
            </li> --}}
            <li class="head-btn nav-item">
                <button type="button" class="bz-quick-btn" data-bs-toggle="modal"
                    data-bs-target="#productAddProcess" title="Product Add Process">
                    <i class="bi bi-info-circle"></i>
                    <span>Product Add Process</span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="bz-quick-btn" data-bs-toggle="modal"
                    data-bs-target="#freightFinderModal" title="Freight Finder">
                    <i class="bi bi-truck"></i>
                    <span>Freight Finder</span>
                </button>
            </li>
            <livewire:Admin.Notification.NavbarNotification />
            <li class="nav-item dropdown">
                <a class="bz-profile-trigger dropdown-toggle" href="#" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="bz-avatar">{{ $bzInitials }}</span>
                    <span class="bz-profile-trigger__meta">
                        <span class="bz-profile-trigger__name d-block">{{ $bzUser->name }}</span>
                        <span class="bz-profile-trigger__role d-block">{{ $bzRole }}</span>
                    </span>
                    <i class="bi bi-chevron-down text-muted" style="font-size:.7rem"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end bz-profile-menu" aria-labelledby="profileDropdown">
                    <div class="bz-profile-menu__head">
                        <span class="bz-avatar" style="flex:0 0 40px;width:40px;height:40px;font-size:.875rem">{{ $bzInitials }}</span>
                        <div class="min-w-0">
                            <div class="bz-profile-menu__name">{{ $bzUser->name }}</div>
                            <div class="bz-profile-menu__mail">{{ $bzUser->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-person"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Profile</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right text-danger"></i>
                        <span>Log Out</span>
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Add Product Modal -->
<div class="modal fade" id="productAddProcess" tabindex="-1" aria-labelledby="productAddProcessLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="productAddProcessLabel">
                    Product Add Process
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! websiteSetupValue('product_add_process') !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Freight Finder Modal -->
<div class="modal fade" id="freightFinderModal" tabindex="-1" aria-labelledby="freightFinderModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="freightFinderModalLabel">
                    <i class="bi bi-truck me-2"></i>Freight Finder
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <livewire:Admin.FreightFinder />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
