<!-- partial:partials/_navbar.html -->
<nav class="navbar">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <form class="search-form">
            <div class="input-group">
                <div class="input-group-text">
                    <i class="bi bi-search"></i>
                </div>
                <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
            </div>
        </form>
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
            <li class="nav-item dropdown">
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
                                    src="{{asset('seller_css/assets/images/faces/face1.jpg')}}" alt="userr">
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
                                    src="{{asset('seller_css/assets/images/faces/face1.jpg')}}" alt="userr">
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
                                    src="{{asset('seller_css/assets/images/faces/face1.jpg')}}" alt="userr">
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
                                    src="{{asset('seller_css/assets/images/faces/face1.jpg')}}" alt="userr">
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
                                    src="{{asset('seller_css/assets/images/faces/face1.jpg')}}" alt="userr">
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
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown"
                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-bell fs-4"></i>
                    <div class="indicator">
                        <div class="circle"></div>
                    </div>
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
                    <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                        <p>6 New Notifications</p>
                        <a href="javascript:;" class="text-muted">Clear all</a>
                    </div>
                    <div class="p-1">
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div
                                class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                <i class="icon-sm text-white" data-feather="gift"></i>
                            </div>
                            <div class="flex-grow-1 me-2">
                                <p>New Order Recieved</p>
                                <p class="tx-12 text-muted">30 min ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div
                                class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                <i class="icon-sm text-white" data-feather="alert-circle"></i>
                            </div>
                            <div class="flex-grow-1 me-2">
                                <p>Server Limit Reached!</p>
                                <p class="tx-12 text-muted">1 hrs ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div
                                class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{asset('seller_css/assets/images/faces/face1.jpg')}}" alt="userr">
                            </div>
                            <div class="flex-grow-1 me-2">
                                <p>New customer registered</p>
                                <p class="tx-12 text-muted">2 sec ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div
                                class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                <i class="icon-sm text-white" data-feather="layers"></i>
                            </div>
                            <div class="flex-grow-1 me-2">
                                <p>Apps are ready for update</p>
                                <p class="tx-12 text-muted">5 hrs ago</p>
                            </div>
                        </a>
                        <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                            <div
                                class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                <i class="icon-sm text-white" data-feather="download"></i>
                            </div>
                            <div class="flex-grow-1 me-2">
                                <p>Download completed</p>
                                <p class="tx-12 text-muted">6 hrs ago</p>
                            </div>
                        </a>
                    </div>
                    <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                        <a href="javascript:;">View all</a>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="wd-30 ht-30 rounded-circle" src="{{asset('seller_css/assets/images/seller.png')}}"
                        alt="profile">
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            <img class="wd-80 ht-80 rounded-circle" src="{{asset('seller_css/assets/images/seller.png')}}"
                                alt="">
                        </div>
                        <div class="text-center">
                            <p class="tx-16 fw-bolder">{{auth()->user()->name}}</p>
                            <p class="tx-12 text-muted">{{auth()->user()->email}}</p>
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="bi bi-person me-2 icon-md"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="bi bi-pencil-square me-2 icon-md"></i>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="{{ route('seller.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-body ms-0">
                                <i class="bi bi-box-arrow-right me-2 icon-md"></i>
                                <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
