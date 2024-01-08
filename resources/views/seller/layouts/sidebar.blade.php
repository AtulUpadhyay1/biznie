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

            <!--Sellers-->
            <li class="nav-item {{ isActiveRoute(['seller.product.index', 'seller.commodity-product.create']) ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#product" role="button"
                    aria-expanded="false" aria-controls="product">
                    <i class="bi bi-bag-check"></i>
                    <span class="link-title">Product</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
                </a>
            </li>
            <div class="collapse {{ isActiveRoute(['seller.product.index', 'seller.commodity-product.create']) ? 'show' : '' }}" id="product">
                <ul class="nav sub-menu">
                    <li class="nav-item {{ isActiveRoute(['seller.product.index']) ? 'active' : '' }}">
                        <a href="{{route('seller.product.index')}}" class="nav-link" wire:navigate>
                           Product List
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['seller.commodity-product.create']) ? 'active' : '' }}">
                        <a href="{{route('seller.commodity-product.create')}}" class="nav-link">
                           Add Product
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of seller -->

        </ul>
    </div>
</nav>
