<!-- partial:partials/_sidebar.html -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{route('admin.dashboard')}}" class="sidebar-brand" wire:navigate>
            <img src="{{asset('admin_css/assets/images/logo.png')}}" alt="Biznie">
        </a>
        <div class="sidebar-toggler not-active" title="Collapse menu">
            <i class="bi bi-list"></i>
        </div>
    </div>
    <div class="sidebar-body">
        <div class="bz-sb-search">
            <div class="bz-sb-search__box">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search menu…  (press /)" autocomplete="off" spellcheck="false"
                    aria-label="Filter menu">
                <button type="button" class="bz-sb-search__clear" aria-label="Clear">
                    <i class="bi bi-x-lg" style="font-size:.7rem"></i>
                </button>
            </div>
        </div>
        <p class="bz-sb-empty">No menu item matches that search.</p>

        <ul class="nav">
            @can('dashboard')
                <li class="nav-item nav-category">Overview</li>
            @endcan
            @can('dashboard')
                <li class="nav-item {{ isActiveRoute(['admin.dashboard']) ? 'active' : '' }}">
                    <a href="{{route('admin.dashboard')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-speedometer"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
            @endcan

            @canany(['seller-list', 'buyer-list', 'vehicle-list', 'transporter-list'])
                <li class="nav-item nav-category">Network</li>
            @endcanany

            @can('seller-list')
                <!--Sellers-->
                <li class="nav-item {{ isActiveRoute(['admin.seller.index', 'admin.business-listing', 'admin.edit-business', 'admin.edit-seller', 'admin.seller-kyc-detail', 'admin.seller.orders', 'admin.seller.payments', 'admin.tag-priority', 'admin.seller.create', 'admin.seller-product.index', 'admin.seller-product.edit', 'admin.seller-product.variation', 'admin.seller-product.price', 'admin.seller-product.stock', 'admin.seller-product.add', 'admin.seller.staffs', 'admin.seller-request.index', 'admin.seller-request.show', 'admin.seller-request.create']) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sellers" role="button"
                        aria-expanded="false" aria-controls="sellers">
                        <i class="bi bi-person-plus"></i>
                        <span class="link-title">Sellers</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.seller.index', 'admin.business-listing', 'admin.edit-business', 'admin.edit-seller', 'admin.seller-kyc-detail', 'admin.seller.orders', 'admin.seller.payments', 'admin.tag-priority', 'admin.seller.create', 'admin.seller-product.index', 'admin.seller-product.edit', 'admin.seller-product.variation', 'admin.seller-product.price', 'admin.seller-product.stock', 'admin.seller-product.add', 'admin.seller.staffs', 'admin.seller-request.index', 'admin.seller-request.show', 'admin.seller-request.create']) ? 'show' : '' }}" id="sellers">
                    <ul class="nav sub-menu">
                        <li class="nav-item {{ isActiveRoute(['admin.seller.index', 'admin.edit-seller', 'admin.seller-kyc-detail', 'admin.seller.orders', 'admin.seller.payments', 'admin.tag-priority', 'admin.seller.create', 'admin.seller-product.index', 'admin.seller-product.edit', 'admin.seller-product.variation', 'admin.seller-product.price', 'admin.seller-product.stock', 'admin.seller-product.add', 'admin.seller.staffs']) ? 'active' : '' }}">
                            <a href="{{route('admin.seller.index')}}" class="nav-link" wire:navigate>
                            All Sellers
                            </a>
                        </li>
                        <li class="nav-item {{ isActiveRoute(['admin.seller-request.index', 'admin.seller-request.show', 'admin.seller-request.create']) ? 'active' : '' }}">
                            <a href="{{route('admin.seller-request.index')}}" class="nav-link" wire:navigate>
                                Seller Request
                            </a>
                        </li>
                        {{--
                        <li class="nav-item {{ isActiveRoute(['admin.business-listing', 'admin.edit-business']) ? 'active' : '' }}">
                            <a href="{{route('admin.business-listing')}}" class="nav-link" wire:navigate>
                            Business Listings
                            </a>
                        </li> --}}
                    </ul>
                </div>
                <!--End of seller -->
            @endcan

            @can('buyer-list')
                <!--Customers-->
                <li class="nav-item {{ isActiveRoute(['admin.customer-list', 'admin.customer-profile',
                'admin.customer-orders-list', 'admin.customer-payment-list', 'admin.edit-customer-info', 'admin.customer-enquiry-list', 'admin.customer-staff-list']) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#customer" role="button"
                        aria-expanded="false" aria-controls="customer">
                        <i class="bi bi-people"></i>
                        <span class="link-title">Buyer</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.customer-list', 'admin.customer-profile',
                    'admin.customer-orders-list', 'admin.customer-payment-list', 'admin.edit-customer-info', 'admin.customer-enquiry-list', 'admin.customer-staff-list']) ? 'show' : '' }}" id="customer">
                    <ul class="nav sub-menu">
                        <li class="nav-item {{ isActiveRoute(['admin.customer-list', 'admin.customer-profile',
                            'admin.customer-orders-list', 'admin.customer-payment-list', 'admin.edit-customer-info', 'admin.customer-enquiry-list', 'admin.customer-staff-list']) ? 'active' : '' }}">
                            <a href="{{route('admin.customer-list')}}" class="nav-link" wire:navigate>
                                All Buyers
                            </a>
                        </li>
                    </ul>
                </div>
                <!--End of customer -->
            @endcan

            @can('vehicle-list')
                <!-- Vehicle -->
                <li class="nav-item {{ isActiveRoute(['admin.vehicle.index', 'admin.vehicle.create', 'admin.vehicle.edit']) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#trasporter" role="button"
                        aria-expanded="false" aria-controls="trasporter">
                        <i class="bi bi-truck"></i>
                        <span class="link-title">Vehicles</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.vehicle.index', 'admin.vehicle.create', 'admin.vehicle.edit']) ? 'show' : '' }}" id="trasporter">
                    <ul class="nav sub-menu">
                        <li class="nav-item {{ isActiveRoute(['admin.vehicle.index', 'admin.vehicle.create', 'admin.vehicle.edit']) ? 'active' : '' }}">
                            <a href="{{route('admin.vehicle.index')}}" class="nav-link" wire:navigate>
                                All Vehicles
                            </a>
                        </li>
                    </ul>
                </div>
                <!--End of Vehicle -->
            @endcan

            @can('transporter-list')
                <!--Transporter-->
                <li class="nav-item {{ isActiveRoute(['admin.transporter.index', 'admin.transporter.create', 'admin.transporter.edit', 'admin.transporter.vehicle', 'admin.transporter.product', 'admin.transporter.addressPrice', 'admin.transporter.profile', 'admin.transporter.enquiry']) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#vehicle" role="button"
                        aria-expanded="false" aria-controls="vehicle">
                        <i class="bi bi-truck-front"></i>
                        <span class="link-title">Transporters</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.transporter.index', 'admin.transporter.create', 'admin.transporter.edit', 'admin.transporter.vehicle', 'admin.transporter.product', 'admin.transporter.addressPrice', 'admin.transporter.profile', 'admin.transporter.enquiry']) ? 'show' : '' }}" id="vehicle">
                    <ul class="nav sub-menu">
                        <li class="nav-item {{ isActiveRoute(['admin.transporter.index', 'admin.transporter.create', 'admin.transporter.edit', 'admin.transporter.vehicle', 'admin.transporter.product', 'admin.transporter.addressPrice', 'admin.transporter.profile', 'admin.transporter.enquiry']) ? 'active' : '' }}">
                            <a href="{{route('admin.transporter.index')}}" class="nav-link" wire:navigate>
                                All Transporters
                            </a>
                        </li>
                    </ul>
                </div>
                <!--End of Transporter -->
            @endcan

            {{-- <li class="nav-item nav-category">Product Management</li>
            <li class="nav-item {{ isActiveRoute(['admin.attribute.index', 'admin.attribute.create', 'admin.attribute.edit']) ? 'active' : '' }}">
                <a href="{{route('admin.attribute.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-opencollective"></i>
                    <span class="link-title">Product Attributes</span>
                </a>
            </li>

            <li class="nav-item {{ isActiveRoute(['admin.packaging-type.index', 'admin.packaging-type.create', 'admin.packaging-type.edit']) ? 'active' : '' }}">
                <a href="{{route('admin.packaging-type.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-box"></i>
                    <span class="link-title">Packaging Type</span>
                </a>
            </li>

            <li class="nav-item {{ isActiveRoute(['admin.brand', 'admin.create-brand', 'admin.edit-brand', 'admin.brand.show']) ? 'active' : ''}}">
                <a href="{{route('admin.brand')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-bing"></i>
                    <span class="link-title">Brand</span>
                </a>
            </li> --}}
            @canany(['product_wise_seller_buyer-list', 'commodity_product-list', 'product_category-list',
                'product_sub_category-list', 'product_sub_subcategory-list', 'product_attribute-list',
                'packaging_type-list', 'brand-list', 'product_enquiry-list', 'commodity_product_order-list'])
                <li class="nav-item nav-category">Trade &amp; Catalogue</li>
            @endcanany

            @can('product_wise_seller_buyer-list')
                <li class="nav-item {{ isActiveRoute(['admin.product-wise-seller-buyer.index']) ? 'active' : ''}}">
                    <a href="{{route('admin.product-wise-seller-buyer.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-people"></i>
                        <span class="link-title">Product wise Seller / Buyer</span>
                    </a>
                </li>

                <li class="nav-item {{ isActiveRoute(['admin.latest_-price-update.index']) ? 'active' : ''}}">
                    <a href="{{route('admin.latest-price-update.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-stopwatch"></i>
                        <span class="link-title">Latest Price Update</span>
                    </a>
                </li>
            @endcan

            @canany(['commodity_product-list', 'product_category-list', 'product_sub_category-list', 'product_sub_subcategory-list', 'product_attribute-list', 'packaging_type-list', 'brand-list'])
                <!--products-->
                <li class="nav-item {{ isActiveRoute(['admin.product-list', 'admin.edit-product',
                    'admin.product-category', 'admin.create-product-category', 'admin.edit-product-category',
                    'admin.product-sub-category', 'admin.create-product-sub-category', 'admin.create-product-sub-subcategory',
                    'admin.edit-product-sub-category', 'admin.product-sub-subcategory', 'admin.edit-product-sub-subcategory',
                    'admin.commodity-product.index', 'admin.commodity-product.create', 'admin.commodity-product.edit',
                    'admin.commodity-product.price', 'admin.commodity-product.variation', 'admin.commodity-product.quality',
                    'admin.commodity-product.show', 'admin.commodity-product.statePrice',
                    'admin.attribute.index', 'admin.attribute.create', 'admin.attribute.edit', 'admin.packaging-type.index', 'admin.packaging-type.create', 'admin.packaging-type.edit', 'admin.brand', 'admin.create-brand', 'admin.edit-brand', 'admin.brand.show', 'admin.seller-product-request.index', 'admin.seller-product-request.show', 'admin.seller-product-request.create']) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#products" role="button"
                        aria-expanded="false" aria-controls="products">
                        <i class="bi bi-cart"></i>
                        <span class="link-title">Products</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.product-list', 'admin.edit-product',
                    'admin.product-category','admin.create-product-category', 'admin.edit-product-category',
                    'admin.product-sub-category', 'admin.create-product-sub-category', 'admin.create-product-sub-subcategory',
                    'admin.edit-product-sub-category', 'admin.product-sub-subcategory', 'admin.edit-product-sub-subcategory',
                    'admin.commodity-product.index', 'admin.commodity-product.create', 'admin.commodity-product.edit',
                    'admin.commodity-product.price', 'admin.commodity-product.variation', 'admin.commodity-product.quality',
                    'admin.commodity-product.show', 'admin.commodity-product.statePrice',
                    'admin.attribute.index', 'admin.attribute.create', 'admin.attribute.edit', 'admin.packaging-type.index', 'admin.packaging-type.create', 'admin.packaging-type.edit', 'admin.brand', 'admin.create-brand', 'admin.edit-brand', 'admin.brand.show', 'admin.seller-product-request.index', 'admin.seller-product-request.show', 'admin.seller-product-request.create']) ? 'show' : '' }}" id="products">
                    <ul class="nav sub-menu">
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">Add New Products</a>
                        </li> --}}
                        {{-- <li class="nav-item {{ isActiveRoute(['admin.product-list', 'admin.edit-product']) ? 'active' : '' }}">
                            <a href="{{route('admin.product-list')}}?status=pending" class="nav-link" wire:navigate>New Request Products</a>
                        </li>
                        <li class="nav-item {{ isActiveRoute(['admin.product-list', 'admin.edit-product']) ? 'active' : '' }}">
                            <a href="{{route('admin.product-list')}}?status=approved" class="nav-link" wire:navigate>Approved Products</a>
                        </li>
                        <li class="nav-item {{ isActiveRoute(['admin.product-list', 'admin.edit-product']) ? 'active' : '' }}">
                            <a href="{{route('admin.product-list')}}?status=rejected" class="nav-link" wire:navigate>Rejected Products</a>
                        </li> --}}
                        @can('commodity_product-list')
                            <li class="nav-item {{ isActiveRoute(['admin.commodity-product.index', 'admin.commodity-product.create', 'admin.commodity-product.edit', 'admin.commodity-product.price', 'admin.commodity-product.variation', 'admin.commodity-product.quality', 'admin.commodity-product.show', 'admin.commodity-product.statePrice', 'admin.commodity-product.sellerPrice']) ? 'active' : '' }}">
                                <a href="{{route('admin.commodity-product.index')}}" class="nav-link" wire:navigate>
                                    Commodity Product
                                </a>
                            </li>
                        @endcan
                        @can('product_category-list')
                            <li class="nav-item {{ isActiveRoute(['admin.product-category','admin.create-product-category', 'admin.edit-product-category']) ? 'active' : '' }}">
                                <a href="{{route('admin.product-category')}}" class="nav-link" wire:navigate>
                                    Category
                                </a>
                            </li>
                        @endcan
                        @can('product_sub_category-list')
                            <li class="nav-item {{ isActiveRoute(['admin.product-sub-category', 'admin.create-product-sub-category', 'admin.edit-product-sub-category']) ? 'active' : '' }}">
                                <a href="{{route('admin.product-sub-category')}}" class="nav-link" wire:navigate>
                                    Sub Category
                                </a>
                            </li>
                        @endcan
                        @can('product_sub_subcategory-list')
                            <li class="nav-item {{ isActiveRoute(['admin.product-sub-subcategory', 'admin.create-product-sub-subcategory', 'admin.edit-product-sub-subcategory']) ? 'active' : '' }}">
                                <a href="{{route('admin.product-sub-subcategory')}}" class="nav-link" wire:navigate>
                                    Sub Sub Category
                                </a>
                            </li>
                        @endcan
                        @can('product_attribute-list')
                            <li class="nav-item {{ isActiveRoute(['admin.attribute.index', 'admin.attribute.create', 'admin.attribute.edit']) ? 'active' : '' }}">
                                <a href="{{route('admin.attribute.index')}}" class="nav-link" wire:navigate>
                                    Product Attributes
                                </a>
                            </li>
                        @endcan
                        @can('packaging_type-list')
                            <li class="nav-item {{ isActiveRoute(['admin.packaging-type.index', 'admin.packaging-type.create', 'admin.packaging-type.edit']) ? 'active' : '' }}">
                                <a href="{{route('admin.packaging-type.index')}}" class="nav-link" wire:navigate>
                                    Packaging Type
                                </a>
                            </li>
                        @endcan
                        @can('brand-list')
                            <li class="nav-item {{ isActiveRoute(['admin.brand', 'admin.create-brand', 'admin.edit-brand', 'admin.brand.show']) ? 'active' : ''}}">
                                <a href="{{route('admin.brand')}}" class="nav-link" wire:navigate>
                                    Brand
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item {{ isActiveRoute(['admin.seller-product-request.index', 'admin.seller-product-request.show', 'admin.seller-product-request.create']) ? 'active' : '' }}">
                            <a href="{{route('admin.seller-product-request.index')}}" class="nav-link" wire:navigate>
                                Product Request
                            </a>
                        </li>
                    </ul>
                </div>
                <!--End of product -->
            @endcanany

            @canany(['product_enquiry-list', 'product_enquiry-general_enquiry', 'product_enquiry-query'])
                <!--Product Enquiry-->
                <li class="nav-item {{ isActiveRoute(['admin.commodity-product-enquiry.index', 'admin.commodity-product-enquiry.create', 'admin.commodity-product-enquiry.variation', 'admin.commodity-product-enquiry.show', 'admin.commodity-product-enquiry.sellerReply', 'admin.commodity-product-enquiry.history', 'admin.commodity-product-enquiry.convertToOrder', 'admin.general-enquiry.index', 'admin.general-enquiry.show', 'admin.contact-us.index']) ? 'active' : '' }}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#products_enquiry" role="button"
                        aria-expanded="false" aria-controls="products_enquiry">
                        <i class="bi bi-journal-check"></i>
                        <span class="link-title">Product Enquiry</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.commodity-product-enquiry.index', 'admin.commodity-product-enquiry.create', 'admin.commodity-product-enquiry.variation', 'admin.commodity-product-enquiry.show', 'admin.commodity-product-enquiry.sellerReply', 'admin.commodity-product-enquiry.history', 'admin.commodity-product-enquiry.convertToOrder', 'admin.general-enquiry.index', 'admin.general-enquiry.show', 'admin.contact-us.index']) ? 'show' : '' }}" id="products_enquiry">
                    <ul class="nav sub-menu">
                        @can('product_enquiry-list')
                            <li class="nav-item {{ isActiveRoute(['admin.commodity-product-enquiry.index', 'admin.commodity-product-enquiry.create', 'admin.commodity-product-enquiry.variation', 'admin.commodity-product-enquiry.show', 'admin.commodity-product-enquiry.sellerReply', 'admin.commodity-product-enquiry.history', 'admin.commodity-product-enquiry.convertToOrder']) ? 'active' : '' }}">
                                <a href="{{route('admin.commodity-product-enquiry.index')}}" class="nav-link" wire:navigate>
                                    Enquiry
                                </a>
                            </li>
                        @endcan

                        @can('product_enquiry-general_enquiry')
                            <li class="nav-item {{ isActiveRoute(['admin.general-enquiry.index', 'admin.general-enquiry.show']) ? 'active' : '' }}">
                                <a href="{{route('admin.general-enquiry.index')}}" class="nav-link" wire:navigate>
                                    General Enquiry
                                </a>
                            </li>
                        @endcan
                        @can('product_enquiry-query')
                            <li class="nav-item {{ isActiveRoute(['admin.contact-us.index', 'admin.contact-us.show']) ? 'active' : '' }}">
                                <a href="{{route('admin.contact-us.index')}}" class="nav-link" wire:navigate>
                                    Query
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
                <!--End of Product Enquiry -->
            @endcanany

            @can('commodity_product_order-list')
                <li class="nav-item {{ isActiveRoute(['admin.commodity-product-order.index', 'admin.commodity-product-order.show', 'admin.commodity-product-order-driver.create', 'admin.commodity-product-order-driver.show', 'admin.commodity-product-order-driver.edit', 'admin.commodity-product-order.status', 'admin.commodity-product-order.history', 'admin.commodity-product-order.transporter']) ? 'active' : ''}}">
                    <a href="{{route('admin.commodity-product-order.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-bootstrap"></i>
                        <span class="link-title">Orders</span>
                    </a>
                </li>
            @endcan

            @canany(['staff-list', 'role-list'])
                <li class="nav-item nav-category">Staff Management</li>
            @endcanany
            @can('staff-list')
                <li class="nav-item {{ isActiveRoute(['admin.staff.index', 'admin.staff.create', 'admin.staff.edit']) ? 'active' : ''}}">
                    <a href="{{route('admin.staff.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-person-badge"></i>
                        <span class="link-title">Staff</span>
                    </a>
                </li>
            @endcan

            @can('role-list')
                <li class="nav-item {{ isActiveRoute(['admin.role.index', 'admin.role.create', 'admin.role.edit']) ? 'active' : ''}}">
                    <a href="{{route('admin.role.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-bootstrap-reboot"></i>
                        <span class="link-title">Role</span>
                    </a>
                </li>
            @endcan

            <li class="nav-item nav-category">Promotion Management</li>
            @can('banner-list')
                <li class="nav-item {{ isActiveRoute(['admin.banner.index', 'admin.banner.create', 'admin.banner.edit']) ? 'active' : '' }}">
                    <a href="{{route('admin.banner.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-image"></i>
                        <span class="link-title">Banners</span>
                    </a>
                </li>
            @endcan
            @can('market_news-list')
                <li class="nav-item {{ isActiveRoute(['admin.market-news.index', 'admin.market-news.create', 'admin.market-news.edit']) ? 'active' : '' }}">
                    <a href="{{route('admin.market-news.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-newspaper"></i>
                        <span class="link-title">Market News</span>
                    </a>
                </li>
            @endcan
            @can('testimonial-list')
                <li class="nav-item {{ isActiveRoute(['admin.testimonial.index', 'admin.testimonial.create', 'admin.testimonial.edit']) ? 'active' : '' }}">
                    <a href="{{route('admin.testimonial.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-chat-right-quote-fill"></i>
                        <span class="link-title">Testimonial</span>
                    </a>
                </li>
            @endcan
            @can('ingot_price_location-list')
                <li class="nav-item {{ isActiveRoute(['admin.ingot_price_location.index', 'admin.ingot_price_location.create', 'admin.ingot_price_location.edit']) ? 'active' : '' }}">
                    <a href="{{route('admin.ingot_price_location.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-geo-fill"></i>
                        <span class="link-title">Ingot Price Location</span>
                    </a>
                </li>
            @endcan
            @can('ingot_price-list')
                <li class="nav-item {{ isActiveRoute(['admin.ingot_price.index', 'admin.ingot_price.create', 'admin.ingot_price.edit']) ? 'active' : '' }}">
                    <a href="{{route('admin.ingot_price.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-graph-up-arrow"></i>
                        <span class="link-title">Ingot Price</span>
                    </a>
                </li>
            @endcan

            <li class="nav-item nav-category">Wallet Management</li>
            @can('cash_wallet')
                <li class="nav-item {{ isActiveRoute(['admin.cash-wallet.index']) ? 'active' : ''}}">
                    <a href="{{route('admin.cash-wallet.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-cash-stack"></i>
                        <span class="link-title">Cash Wallet</span>
                    </a>
                </li>
            @endcan
            @can('credit_wallet_request-list')
                <li class="nav-item {{ isActiveRoute(['admin.credit-wallet-request.index', 'admin.credit-wallet-request.create', 'admin.credit-wallet-request.show']) ? 'active' : ''}}">
                    <a href="{{route('admin.credit-wallet-request.index')}}" class="nav-link" wire:navigate>
                        <i class="bi bi-credit-card-2-front"></i>
                        <span class="link-title">Credit Wallet Request</span>
                    </a>
                </li>
            @endcan

            <li class="nav-item nav-category">Setup</li>
            @canany(['business_category-list', 'business_type-list', 'seller_type-list', 'gst_type-list', 'product_unit-list', 'tax_type-list', 'identity_type-list', 'address-list', 'seller_tag-list', 'credit_wallet_document_type-list'])
                <!--Biznie Set up-->
                <li class="nav-item {{ isActiveRoute(['admin.business-category', 'admin.create-business-category', 'admin.edit-business-category', 'admin.business-type', 'admin.create-business-type', 'admin.edit-business-type', 'admin.seller-type', 'admin.create-seller-type', 'admin.edit-seller-type', 'admin.gst-type', 'admin.create-gst-type', 'admin.edit-gst-type', 'admin.product-unit', 'admin.create-product-unit', 'admin.edit-product-unit', 'admin.tax-type', 'admin.create-tax-type', 'admin.edit-tax-type', 'admin.identity-type', 'admin.create-identity-type', 'admin.edit-identity-type', 'admin.seller-tag.index', 'admin.seller-tag.create', 'admin.seller-tag.edit', 'admin.credit-wallet-document-type.index', 'admin.credit-wallet-document-type.create', 'admin.credit-wallet-document-type.edit', 'admin.address.index', 'admin.address.create', 'admin.address.edit']) ? 'active' : ''}}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#master-setting" role="button"
                        aria-expanded="false" aria-controls="master-setting">
                        <i class="bi bi-building-gear"></i>
                        <span class="link-title">Biznie Setup</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.business-category', 'admin.create-business-category', 'admin.edit-business-category', 'admin.vendor-type', 'admin.seller-type', 'admin.brand', 'admin.gst-type', 'admin.product-unit', 'admin.tax-type', 'admin.identity-type', 'admin.business-type', 'admin.create-business-type', 'admin.edit-business-type', 'admin.seller-type', 'admin.create-seller-type', 'admin.edit-seller-type', 'admin.gst-type', 'admin.create-gst-type', 'admin.edit-gst-type', 'admin.product-unit', 'admin.create-product-unit', 'admin.edit-product-unit', 'admin.tax-type', 'admin.create-tax-type', 'admin.edit-tax-type', 'admin.identity-type', 'admin.create-identity-type', 'admin.edit-identity-type', 'admin.seller-tag.index', 'admin.seller-tag.create', 'admin.seller-tag.edit', 'admin.credit-wallet-document-type.index', 'admin.credit-wallet-document-type.create', 'admin.credit-wallet-document-type.edit', 'admin.address.index', 'admin.address.create', 'admin.address.edit']) ? 'show' : ''}}" id="master-setting">
                    <ul class="nav sub-menu">
                        @can('business_category-list')
                            <li class="nav-item {{ isActiveRoute(['admin.business-category', 'admin.create-business-category', 'admin.edit-business-category']) ? 'active' : ''}}">
                                <a href="{{route('admin.business-category')}}" class="nav-link" wire:navigate>
                                Business Category
                                </a>
                            </li>
                        @endcan
                        @can('business_type-list')
                            <li class="nav-item {{ isActiveRoute(['admin.business-type', 'admin.create-business-type', 'admin.edit-business-type']) ? 'active' : ''}}">
                                <a href="{{route('admin.business-type')}}" class="nav-link" wire:navigate>
                                    Business Type
                                </a>
                            </li>
                        @endcan
                        @can('seller_type-list')
                            <li class="nav-item  {{ isActiveRoute(['admin.seller-type', 'admin.create-seller-type', 'admin.edit-seller-type']) ? 'active' : ''}}">
                                <a href="{{route('admin.seller-type')}}" class="nav-link" wire:navigate>
                                Seller Type
                                </a>
                            </li>
                        @endcan
                        @can('gst_type-list')
                            <li class="nav-item {{ isActiveRoute(['admin.gst-type', 'admin.create-gst-type', 'admin.edit-gst-type']) ? 'active' : ''}} ">
                                <a href="{{route('admin.gst-type')}}" class="nav-link" wire:navigate>
                                GST Type
                                </a>
                            </li>
                        @endcan
                        @can('product_unit-list')
                            <li class="nav-item {{ isActiveRoute(['admin.product-unit', 'admin.create-product-unit', 'admin.edit-product-unit']) ? 'active' : ''}}">
                                <a href="{{route('admin.product-unit')}}" class="nav-link" wire:navigate>
                                Product Unit
                                </a>
                            </li>
                        @endcan
                        @can('tax_type-list')
                            <li class="nav-item {{ isActiveRoute(['admin.tax-type', 'admin.create-tax-type', 'admin.edit-tax-type']) ? 'active' : ''}}">
                                <a href="{{route('admin.tax-type')}}" class="nav-link" wire:navigate>
                                Tax Type
                                </a>
                            </li>
                        @endcan
                        @can('identity_type-list')
                            <li class="nav-item {{ isActiveRoute(['admin.identity-type', 'admin.create-identity-type', 'admin.edit-identity-type']) ? 'active' : ''}}">
                                <a href="{{route('admin.identity-type')}}" class="nav-link" wire:navigate>
                                    Identity Type
                                </a>
                            </li>
                        @endcan
                        @can('address-list')
                            <li class="nav-item {{ isActiveRoute(['admin.address.index', 'admin.address.create', 'admin.address.edit']) ? 'active' : ''}}">
                                <a href="{{route('admin.address.index')}}" class="nav-link" wire:navigate>
                                    Address Management
                                </a>
                            </li>
                        @endcan
                        @can('seller_tag-list')
                            <li class="nav-item {{ isActiveRoute(['admin.seller-tag.index', 'admin.seller-tag.create', 'admin.seller-tag.edit']) ? 'active' : ''}}">
                                <a href="{{route('admin.seller-tag.index')}}" class="nav-link" wire:navigate>
                                    Seller Tag
                                </a>
                            </li>
                        @endcan
                        @can('credit_wallet_document_type-list')
                            <li class="nav-item {{ isActiveRoute(['admin.credit-wallet-document-type.index', 'admin.credit-wallet-document-type.create', 'admin.credit-wallet-document-type.edit']) ? 'active' : ''}}">
                                <a href="{{route('admin.credit-wallet-document-type.index')}}" class="nav-link" wire:navigate>
                                    Credit Wallet Document Type
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
                <!-- End of biznie set up -->
            @endcanany

            {{-- <!--Businesses-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#business" role="button"
                    aria-expanded="false" aria-controls="business">
                    <i class="bi bi-building"></i>
                    <span class="link-title">Business</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
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
            <!--End of business --> --}}

            {{-- <!--Messages-->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#message" role="button"
                    aria-expanded="false" aria-controls="message">
                    <i class="bi bi-chat-left"></i>
                    <span class="link-title">Messages</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
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
            <!--End of messages--> --}}

            @canany(['website_setup-general', 'website_setup-about', 'website_setup-setting', 'website_setup-returns', 'website_setup-terms', 'website_setup-privacy', 'website_setup-shop_on', 'website_setup-payment_methods', 'website_setup-logistics', 'website_setup-product_delivery_info', 'website_setup-product_terms', 'website_setup-product_add_process'])
                <!--App Setup-->
                <li class="nav-item {{ isActiveRoute(['admin.website_setup.general', 'admin.faq.index', 'admin.faq.create', 'admin.faq.edit', 'admin.website_setup.setting', 'admin.website_setup.about', 'admin.website_setup.returns', 'admin.website_setup.terms', 'admin.website_setup.privacy', 'admin.website_setup.shop_on', 'admin.website_setup.payment_methods', 'admin.website_setup.logistics', 'admin.website_setup.product_delivery_info', 'admin.website_setup.product_terms', 'admin.website_setup.product_add_process', 'admin.website_setup.notification_setting']) ? 'active' : ''}}">
                    <a class="nav-link" data-bs-toggle="collapse" href="#app-setup" role="button"
                        aria-expanded="false" aria-controls="app-setup">
                        <i class="bi bi-gear"></i>
                        <span class="link-title">Web Setup</span>
                        <i class="bi bi-chevron-down link-arrow"></i>
                    </a>
                </li>
                <div class="collapse {{ isActiveRoute(['admin.faq.index', 'admin.website_setup.general', 'admin.faq.create', 'admin.faq.edit', 'admin.website_setup.about', 'admin.website_setup.setting', 'admin.website_setup.about', 'admin.website_setup.returns', 'admin.website_setup.terms', 'admin.website_setup.privacy', 'admin.website_setup.shop_on', 'admin.website_setup.payment_methods', 'admin.website_setup.logistics', 'admin.website_setup.product_delivery_info', 'admin.website_setup.product_terms', 'admin.website_setup.product_add_process', 'admin.website_setup.notification_setting']) ? 'show' : ''}}" id="app-setup">
                    <ul class="nav sub-menu">
                        @can('website_setup-general')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.general']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.general')}}" class="nav-link" wire:navigate>
                                General Setup
                                </a>
                            </li>
                        @endcan
                        @can('faq-list')
                            <li class="nav-item {{ isActiveRoute(['admin.faq.index', 'admin.faq.create', 'admin.faq.edit']) ? 'active' : ''}}">
                                <a href="{{route('admin.faq.index')}}" class="nav-link" wire:navigate>
                                FAQ
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-about')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.about']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.about')}}" class="nav-link" wire:navigate>
                                    About Us
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-product_add_process')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.product_add_process']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.product_add_process')}}" class="nav-link" wire:navigate>
                                    Product Add Process
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-returns_policy')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.returns']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.returns')}}" class="nav-link" wire:navigate>
                                    Returns Policy
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-terms_condition')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.terms']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.terms')}}" class="nav-link" wire:navigate>
                                    Terms & Condition
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-privacy_policy')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.privacy']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.privacy')}}" class="nav-link" wire:navigate>
                                    Privacy Policy
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-shop_on')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.shop_on']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.shop_on')}}" class="nav-link" wire:navigate>
                                    Shop on Biznie
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-payment_methods')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.payment_methods']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.payment_methods')}}" class="nav-link" wire:navigate>
                                    Payment Method
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-logistics')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.logistics']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.logistics')}}" class="nav-link" wire:navigate>
                                    Logistics
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-product_delivery_info')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.product_delivery_info']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.product_delivery_info')}}" class="nav-link" wire:navigate>
                                    Product Delivery Info
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-product_terms_condition')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.product_terms']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.product_terms')}}" class="nav-link" wire:navigate>
                                    Product Terms & Condition
                                </a>
                            </li>
                        @endcan
                        @can('website_setup-setting')
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.setting']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.setting')}}" class="nav-link" wire:navigate>
                                    Setting
                                </a>
                            </li>
                            <li class="nav-item {{ isActiveRoute(['admin.website_setup.notification_setting']) ? 'active' : ''}}">
                                <a href="{{route('admin.website_setup.notification_setting')}}" class="nav-link" wire:navigate>
                                    Notification Setting
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </ul>
    </div>
</nav>
