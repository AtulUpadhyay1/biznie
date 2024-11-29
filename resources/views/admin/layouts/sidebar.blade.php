<!-- partial:partials/_sidebar.html -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{route('admin.dashboard')}}" class="sidebar-brand">
            <img src="{{asset('admin_css/assets/images/logo.png')}}" style="width: 70%;">
        </a>
        <div class="sidebar-toggler not-active">
            <i class="bi bi-list fs-3 text-white"></i>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item {{ isActiveRoute(['admin.dashboard']) ? 'active' : '' }}">
                <a href="{{route('admin.dashboard')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-speedometer"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <!--Sellers-->
            <li class="nav-item {{ isActiveRoute(['admin.seller.index', 'admin.business-listing', 'admin.edit-business', 'admin.edit-seller', 'admin.seller-kyc-detail', 'admin.tag-priority', 'admin.seller.create', 'admin.seller-product.index', 'admin.seller-product.edit', 'admin.seller-product.variation', 'admin.seller-product.price', 'admin.seller-product.stock', 'admin.seller-product.add']) ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#sellers" role="button"
                    aria-expanded="false" aria-controls="sellers">
                    <i class="bi bi-person-plus"></i>
                    <span class="link-title">Sellers</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
                </a>
            </li>
            <div class="collapse {{ isActiveRoute(['admin.seller.index', 'admin.business-listing', 'admin.edit-business', 'admin.edit-seller', 'admin.seller-kyc-detail', 'admin.tag-priority', 'admin.seller.create', 'admin.seller-product.index', 'admin.seller-product.edit', 'admin.seller-product.variation', 'admin.seller-product.price', 'admin.seller-product.stock', 'admin.seller-product.add']) ? 'show' : '' }}" id="sellers">
                <ul class="nav sub-menu">
                    <li class="nav-item {{ isActiveRoute(['admin.seller.index', 'admin.edit-seller', 'admin.seller-kyc-detail', 'admin.tag-priority', 'admin.seller.create', 'admin.seller-product.index', 'admin.seller-product.edit', 'admin.seller-product.variation', 'admin.seller-product.price', 'admin.seller-product.stock', 'admin.seller-product.add']) ? 'active' : '' }}">
                        <a href="{{route('admin.seller.index')}}" class="nav-link" wire:navigate>
                           All Sellers
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

            <!--Customers-->
            <li class="nav-item {{ isActiveRoute(['admin.customer-list', 'admin.customer-profile',
            'admin.customer-orders-list', 'admin.customer-payment-list', 'admin.edit-customer-info', 'admin.customer-enquiry-list']) ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#customer" role="button"
                    aria-expanded="false" aria-controls="customer">
                    <i class="bi bi-people"></i>
                    <span class="link-title">Customers</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
                </a>
            </li>
            <div class="collapse {{ isActiveRoute(['admin.customer-list', 'admin.customer-profile',
                'admin.customer-orders-list', 'admin.customer-payment-list', 'admin.edit-customer-info', 'admin.customer-enquiry-list']) ? 'show' : '' }}" id="customer">
                <ul class="nav sub-menu">
                    <li class="nav-item {{ isActiveRoute(['admin.customer-list', 'admin.customer-profile',
                        'admin.customer-orders-list', 'admin.customer-payment-list', 'admin.edit-customer-info', 'admin.customer-enquiry-list']) ? 'active' : '' }}">
                        <a href="{{route('admin.customer-list')}}" class="nav-link" wire:navigate>
                            All Customers
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of customer -->

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

            <!--Transporter-->
            <li class="nav-item {{ isActiveRoute(['admin.transporter.index', 'admin.transporter.create', 'admin.transporter.edit', 'admin.transporter.vehicle', 'admin.transporter.product', 'admin.transporter.addressPrice']) ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#vehicle" role="button"
                    aria-expanded="false" aria-controls="vehicle">
                    <i class="bi bi-truck-front"></i>
                    <span class="link-title">Transporters</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
                </a>
            </li>
            <div class="collapse {{ isActiveRoute(['admin.transporter.index', 'admin.transporter.create', 'admin.transporter.edit', 'admin.transporter.vehicle', 'admin.transporter.product', 'admin.transporter.addressPrice']) ? 'show' : '' }}" id="vehicle">
                <ul class="nav sub-menu">
                    <li class="nav-item {{ isActiveRoute(['admin.transporter.index', 'admin.transporter.create', 'admin.transporter.edit', 'admin.transporter.vehicle', 'admin.transporter.product', 'admin.transporter.addressPrice']) ? 'active' : '' }}">
                        <a href="{{route('admin.transporter.index')}}" class="nav-link" wire:navigate>
                            All Transporters
                        </a>
                    </li>
                </ul>
            </div>
            <!--End of Transporter -->

            <li class="nav-item nav-category">Product Management</li>
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

            <li class="nav-item {{ isActiveRoute(['admin.brand', 'admin.create-brand', 'admin.edit-brand']) ? 'active' : ''}}">
                <a href="{{route('admin.brand')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-bing"></i>
                    <span class="link-title">Brand</span>
                </a>
            </li>

            <!--products-->
            <li class="nav-item {{ isActiveRoute(['admin.product-list', 'admin.edit-product',
            'admin.product-category', 'admin.create-product-category', 'admin.edit-product-category',
            'admin.product-sub-category', 'admin.create-product-sub-category', 'admin.create-product-sub-subcategory',
            'admin.edit-product-sub-category', 'admin.product-sub-subcategory', 'admin.edit-product-sub-subcategory', 'admin.commodity-product.index', 'admin.commodity-product.create', 'admin.commodity-product.edit', 'admin.commodity-product.price', 'admin.commodity-product.variation', 'admin.commodity-product.quality', 'admin.commodity-product.show', 'admin.commodity-product.statePrice']) ? 'active' : '' }}">
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
            'admin.edit-product-sub-category', 'admin.product-sub-subcategory', 'admin.edit-product-sub-subcategory', 'admin.commodity-product.index', 'admin.commodity-product.create', 'admin.commodity-product.edit', 'admin.commodity-product.price', 'admin.commodity-product.variation', 'admin.commodity-product.quality', 'admin.commodity-product.show', 'admin.commodity-product.statePrice']) ? 'show' : '' }}" id="products">
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
                    <li class="nav-item {{ isActiveRoute(['admin.product-category','admin.create-product-category', 'admin.edit-product-category']) ? 'active' : '' }}">
                        <a href="{{route('admin.product-category')}}" class="nav-link" wire:navigate>
                            Category
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.product-sub-category', 'admin.create-product-sub-category', 'admin.edit-product-sub-category']) ? 'active' : '' }}">
                        <a href="{{route('admin.product-sub-category')}}" class="nav-link" wire:navigate>
                            Sub Category
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.product-sub-subcategory', 'admin.create-product-sub-subcategory', 'admin.edit-product-sub-subcategory']) ? 'active' : '' }}">
                        <a href="{{route('admin.product-sub-subcategory')}}" class="nav-link" wire:navigate>
                            Sub Sub Category
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.commodity-product.index', 'admin.commodity-product.create', 'admin.commodity-product.edit', 'admin.commodity-product.price', 'admin.commodity-product.variation', 'admin.commodity-product.quality', 'admin.commodity-product.show', 'admin.commodity-product.statePrice', 'admin.commodity-product.sellerPrice']) ? 'active' : '' }}">
                        <a href="{{route('admin.commodity-product.index')}}" class="nav-link" wire:navigate>
                            Commodity Product
                        </a>
                    </li>
                </ul>
            </div>

            <li class="nav-item {{ isActiveRoute(['admin.commodity-product-enquiry.index', 'admin.commodity-product-enquiry.create', 'admin.commodity-product-enquiry.variation', 'admin.commodity-product-enquiry.show', 'admin.commodity-product-enquiry.sellerReply', 'admin.commodity-product-enquiry.history', 'admin.commodity-product-enquiry.convertToOrder']) ? 'active' : '' }}">
                <a href="{{route('admin.commodity-product-enquiry.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-journal-check"></i>
                    <span class="link-title">Product Enquiry</span>
                </a>
            </li>
            <!--End of product -->

            <li class="nav-item {{ isActiveRoute(['admin.commodity-product-order.index', 'admin.commodity-product-order.show', 'admin.commodity-product-order-driver.create', 'admin.commodity-product-order-driver.show', 'admin.commodity-product-order-driver.edit', 'admin.commodity-product-order.status', 'admin.commodity-product-order.history']) ? 'active' : ''}}">
                <a href="{{route('admin.commodity-product-order.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-bootstrap"></i>
                    <span class="link-title">Orders</span>
                </a>
            </li>

            <li class="nav-item nav-category">Promotion Management</li>
            <li class="nav-item {{ isActiveRoute(['admin.banner.index', 'admin.banner.create', 'admin.banner.edit']) ? 'active' : '' }}">
                <a href="{{route('admin.banner.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-image"></i>
                    <span class="link-title">Banners</span>
                </a>
            </li>

            <li class="nav-item {{ isActiveRoute(['admin.market-news.index', 'admin.market-news.create', 'admin.market-news.edit']) ? 'active' : '' }}">
                <a href="{{route('admin.market-news.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-newspaper"></i>
                    <span class="link-title">Market News</span>
                </a>
            </li>

            <li class="nav-item {{ isActiveRoute(['admin.testimonial.index', 'admin.testimonial.create', 'admin.testimonial.edit']) ? 'active' : '' }}">
                <a href="{{route('admin.testimonial.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-chat-right-quote-fill"></i>
                    <span class="link-title">Testimonial</span>
                </a>
            </li>

            <li class="nav-item nav-category">Wallet Management</li>

            <li class="nav-item {{ isActiveRoute(['admin.cash-wallet.index']) ? 'active' : ''}}">
                <a href="{{route('admin.cash-wallet.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-cash-stack"></i>
                    <span class="link-title">Cash Wallet</span>
                </a>
            </li>

            <li class="nav-item {{ isActiveRoute(['admin.credit-wallet-request.index', 'admin.credit-wallet-request.create', 'admin.credit-wallet-request.show']) ? 'active' : ''}}">
                <a href="{{route('admin.credit-wallet-request.index')}}" class="nav-link" wire:navigate>
                    <i class="bi bi-credit-card-2-front"></i>
                    <span class="link-title">Credit Wallet Request</span>
                </a>
            </li>

            <li class="nav-item nav-category">Setup</li>

            <!--Biznie Set up-->
            <li class="nav-item {{ isActiveRoute(['admin.business-category', 'admin.create-business-category', 'admin.edit-business-category', 'admin.business-type', 'admin.create-business-type', 'admin.edit-business-type', 'admin.seller-type', 'admin.create-seller-type', 'admin.edit-seller-type', 'admin.gst-type', 'admin.create-gst-type', 'admin.edit-gst-type', 'admin.product-unit', 'admin.create-product-unit', 'admin.edit-product-unit', 'admin.tax-type', 'admin.create-tax-type', 'admin.edit-tax-type', 'admin.identity-type', 'admin.create-identity-type', 'admin.edit-identity-type', 'admin.seller-tag.index', 'admin.seller-tag.create', 'admin.seller-tag.edit', 'admin.credit-wallet-document-type.index', 'admin.credit-wallet-document-type.create', 'admin.credit-wallet-document-type.edit']) ? 'active' : ''}}">
                <a class="nav-link" data-bs-toggle="collapse" href="#master-setting" role="button"
                    aria-expanded="false" aria-controls="master-setting">
                    <i class="bi bi-building-gear"></i>
                    <span class="link-title">Biznie Setup</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
                </a>
            </li>
            <div class="collapse {{ isActiveRoute(['admin.business-category', 'admin.create-business-category', 'admin.edit-business-category', 'admin.vendor-type', 'admin.seller-type', 'admin.brand', 'admin.gst-type', 'admin.product-unit', 'admin.tax-type', 'admin.identity-type', 'admin.business-type', 'admin.create-business-type', 'admin.edit-business-type', 'admin.seller-type', 'admin.create-seller-type', 'admin.edit-seller-type', 'admin.gst-type', 'admin.create-gst-type', 'admin.edit-gst-type', 'admin.product-unit', 'admin.create-product-unit', 'admin.edit-product-unit', 'admin.tax-type', 'admin.create-tax-type', 'admin.edit-tax-type', 'admin.identity-type', 'admin.create-identity-type', 'admin.edit-identity-type', 'admin.seller-tag.index', 'admin.seller-tag.create', 'admin.seller-tag.edit', 'admin.credit-wallet-document-type.index', 'admin.credit-wallet-document-type.create', 'admin.credit-wallet-document-type.edit']) ? 'show' : ''}}" id="master-setting">
                <ul class="nav sub-menu">
                    <li class="nav-item {{ isActiveRoute(['admin.business-category', 'admin.create-business-category', 'admin.edit-business-category']) ? 'active' : ''}}">
                        <a href="{{route('admin.business-category')}}" class="nav-link" wire:navigate>
                           Business Category
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.business-type', 'admin.create-business-type', 'admin.edit-business-type']) ? 'active' : ''}}">
                        <a href="{{route('admin.business-type')}}" class="nav-link" wire:navigate>
                            Business Type
                        </a>
                    </li>
                    <li class="nav-item  {{ isActiveRoute(['admin.seller-type', 'admin.create-seller-type', 'admin.edit-seller-type']) ? 'active' : ''}}">
                        <a href="{{route('admin.seller-type')}}" class="nav-link" wire:navigate>
                           Seller Type
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.gst-type', 'admin.create-gst-type', 'admin.edit-gst-type']) ? 'active' : ''}} ">
                        <a href="{{route('admin.gst-type')}}" class="nav-link" wire:navigate>
                           GST Type
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.product-unit', 'admin.create-product-unit', 'admin.edit-product-unit']) ? 'active' : ''}}">
                        <a href="{{route('admin.product-unit')}}" class="nav-link" wire:navigate>
                           Product Unit
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.tax-type', 'admin.create-tax-type', 'admin.edit-tax-type']) ? 'active' : ''}}">
                        <a href="{{route('admin.tax-type')}}" class="nav-link" wire:navigate>
                           Tax Type
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.identity-type', 'admin.create-identity-type', 'admin.edit-identity-type']) ? 'active' : ''}}">
                        <a href="{{route('admin.identity-type')}}" class="nav-link" wire:navigate>
                            Identity Type
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.address.index', 'admin.address.create', 'admin.address.edit']) ? 'active' : ''}}">
                        <a href="{{route('admin.address.index')}}" class="nav-link" wire:navigate>
                            Address Management
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.seller-tag.index', 'admin.seller-tag.create', 'admin.seller-tag.edit']) ? 'active' : ''}}">
                        <a href="{{route('admin.seller-tag.index')}}" class="nav-link" wire:navigate>
                            Seller Tag
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.credit-wallet-document-type.index', 'admin.credit-wallet-document-type.create', 'admin.credit-wallet-document-type.edit']) ? 'active' : ''}}">
                        <a href="{{route('admin.credit-wallet-document-type.index')}}" class="nav-link" wire:navigate>
                            Credit Wallet Document Type
                        </a>
                    </li>
                </ul>
            </div>
            <!-- End of biznie set up -->

            <!--Businesses-->
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
            <!--End of business -->

            <!--Messages-->
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
            <!--End of messages-->

            <!--App Setup-->
            <li class="nav-item {{ isActiveRoute(['admin.faq.index', 'admin.faq.create', 'admin.faq.edit', 'admin.website_setup.setting']) ? 'active' : ''}}">
                <a class="nav-link" data-bs-toggle="collapse" href="#app-setup" role="button"
                    aria-expanded="false" aria-controls="app-setup">
                    <i class="bi bi-gear"></i>
                    <span class="link-title">Web Setup</span>
                    <i class="bi bi-chevron-down link-arrow"></i>
                </a>
            </li>
            <div class="collapse {{ isActiveRoute(['admin.faq.index', 'admin.faq.create', 'admin.faq.edit', 'admin.website_setup.setting']) ? 'show' : ''}}" id="app-setup">
                <ul class="nav sub-menu">
                    <li class="nav-item {{ isActiveRoute(['admin.faq.index', 'admin.faq.create', 'admin.faq.edit']) ? 'active' : ''}}">
                        <a href="{{route('admin.faq.index')}}" class="nav-link">
                           FAQ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.website_setup.about')}}" class="nav-link">
                            About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.website_setup.returns')}}" class="nav-link">
                            Returns Policy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.website_setup.terms')}}" class="nav-link">
                            Terms & Condition
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.website_setup.privacy')}}" class="nav-link">
                            Privacy Policy
                        </a>
                    </li>
                    <li class="nav-item {{ isActiveRoute(['admin.website_setup.setting']) ? 'active' : ''}}">
                        <a href="{{route('admin.website_setup.setting')}}" class="nav-link">
                            Setting
                        </a>
                    </li>
                </ul>
            </div>
        </ul>
    </div>
</nav>
