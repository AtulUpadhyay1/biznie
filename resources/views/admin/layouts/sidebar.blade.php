<!-- partial:partials/_sidebar.html -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{route('admin.dashboard')}}" class="sidebar-brand">
            <img src="{{asset('admin_css/assets/images/logo.png')}}" style="width: 70%;">
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Home</li>
            <li class="nav-item">
                <a href="{{route('admin.dashboard')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">All Customers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.all-seller')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">All Sellers</span>
                </a>
            </li>
            <!--Category & products-->
            <li class="nav-item nav-category">Products</li>
            {{-- <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#business" role="button"
                    aria-expanded="false" aria-controls="business">
                    <i class="link-icon" data-feather="shopping-bag"></i>
                    <span class="link-title">Products</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="business">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">All Products</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Add Products</a>
                        </li>
                    </ul>
                </div>
            </li> --}}
            <li class="nav-item">
                <a href="{{route('admin.product-category')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.product-sub-category')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Sub Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.product-sub-subcategory')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Sub Sub Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="plus-circle"></i>
                    <span class="link-title">All Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="plus-circle"></i>
                    <span class="link-title">Order Book</span>
                </a>
            </li>

            <!--Businesses-->
            <li class="nav-item nav-category">Business Setup</li>
            <li class="nav-item">
                <a href="{{route('admin.business-category')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="inbox"></i>
                    <span class="link-title">Business Category</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.vendor-type')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="user-plus"></i>
                    <span class="link-title">Vendor Type</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.seller-type')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="shopping-bag"></i>
                    <span class="link-title">Seller Type</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.gst-type')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">GST Type</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.product-unit')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Unit</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.tax-type')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="pie-chart"></i>
                    <span class="link-title">Tax Type</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.identity-type')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Identity Type</span>
                </a>
            </li>

            <!--Transaction-->
            <li class="nav-item nav-category">Transaction</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="calendar"></i>
                    <span class="link-title">Invoice</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="credit-card"></i>
                    <span class="link-title">Payment List</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="aperture"></i>
                    <span class="link-title">Analytics</span>
                </a>
            </li>

            <!--Inboxes-->
            <li class="nav-item nav-category">Inbox</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="compass"></i>
                    <span class="link-title">Request For Quotation</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Messages</span>
                </a>
            </li>

            <!--Shop Setup-->
            <li class="nav-item nav-category">Profile</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="tool"></i>
                    <span class="link-title">Shop Setup</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="settings"></i>
                    <span class="link-title">Setting</span>
                </a>
            </li>

            <!--Others-->
            <li class="nav-item nav-category">Others</li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="help-circle"></i>
                    <span class="link-title">Help Center</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="log-out"></i>
                    <span class="link-title">Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
