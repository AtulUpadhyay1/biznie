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
            <li class="nav-item">
                <a href="{{route('admin.dashboard')}}" class="nav-link" wire:navigate>
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <!--Sellers-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sellers" role="button"
                    aria-expanded="false" aria-controls="sellers">
                    <i class="link-icon" data-feather="user-plus"></i>
                    <span class="link-title">Sellers</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="sellers">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{route('admin.seller-list')}}" class="nav-link" wire:navigate>
                           All Sellers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.business-listing')}}" class="nav-link" wire:navigate>
                           Business Listings
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of seller -->

            <!--Customers-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#customer" role="button"
                    aria-expanded="false" aria-controls="customer">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Customers</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="customer">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{route('admin.customer-list')}}" class="nav-link" wire:navigate>
                            All Customers
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of customer -->

            <!--products-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#products" role="button"
                    aria-expanded="false" aria-controls="products">
                    <i class="link-icon" data-feather="shopping-cart"></i>
                    <span class="link-title">Products</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="products">
                <ul class="nav sub-menu">
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link">Add New Products</a>
                    </li> --}}
                    <li class="nav-item">
                        <a href="{{route('admin.product-list')}}" class="nav-link" wire:navigate>All Products</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.product-category')}}" class="nav-link" wire:navigate>
                            Category
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.product-sub-category')}}" class="nav-link" wire:navigate>
                            Sub Category
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.product-sub-subcategory')}}" class="nav-link" wire:navigate>
                            Sub Sub Category
                        </a>
                    </li>

                </ul>
            </div>
            <!--End of product -->

            <!--Businesses-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#business" role="button"
                    aria-expanded="false" aria-controls="business">
                    <i class="link-icon" data-feather="shopping-bag"></i>
                    <span class="link-title">Business</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="business">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Order Book
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Invoice
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Payment List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Analytics
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of business -->

            <!--Messages-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#message" role="button"
                    aria-expanded="false" aria-controls="message">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Messages</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="message">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                           Request For Quotation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                           Messages
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of messages-->

            <!--Biznie Set up-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#master-setting" role="button"
                    aria-expanded="false" aria-controls="master-setting">
                    <i class="link-icon" data-feather="settings"></i>
                    <span class="link-title">Biznie Setup</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="master-setting">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{route('admin.business-category')}}" class="nav-link" wire:navigate>
                           Business Category
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.vendor-type')}}" class="nav-link" wire:navigate>
                            Vendor Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.seller-type')}}" class="nav-link" wire:navigate>
                           Seller Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.gst-type')}}" class="nav-link" wire:navigate>
                           GST Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.product-unit')}}" class="nav-link" wire:navigate>
                           Unit
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.tax-type')}}" class="nav-link" wire:navigate>
                           Tax Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.identity-type')}}" class="nav-link" wire:navigate>
                            Identity Type
                        </a>
                    </li>
                </ul>
            </div>
            <!-- End of biznie set up -->

            <!--App Setup-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#app-setup" role="button"
                    aria-expanded="false" aria-controls="app-setup">
                    <i class="link-icon" data-feather="tool"></i>
                    <span class="link-title">App Setup</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
            </li>
            <div class="collapse" id="app-setup">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                           App Banner/Sliders</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- End of app setup -->

            <!--Others-->
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
