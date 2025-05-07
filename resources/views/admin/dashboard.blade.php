<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Your Dashboard!</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                <span class="input-group-text input-group-addon bg-transparent border-danger" data-toggle>
                    <i class="bi bi-calendar text-danger"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-danger" placeholder="Select date"
                    data-input>
            </div>
            {{-- <button type="button" class="btn btn-outline-danger btn-icon-text me-2 mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="printer"></i>
                Print
            </button> --}}
            <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0" wire:click="notificationTest()">
                <i class="bi bi-cloud-download btn-icon-prepend"></i>
                Download Report
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.customer-list') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Buyers</h6>
                        </div>
                        <h3 class="mb-2">{{ $total_customer }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.seller.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Sellers</h6>
                        </div>
                        <h3 class="mb-2">{{ $total_seller }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.transporter.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Transporters</h6>
                        </div>
                        <h3 class="mb-2">{{ $total_transporter }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.brand') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Brand</h6>
                        </div>
                        <h3 class="mb-2">{{ $total_brand }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.commodity-product-enquiry.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Enquiry</h6>
                        </div>
                        <h3 class="mb-2">{{ $total_enquiry }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Order</h6>
                        </div>
                        <h3 class="mb-2">{{ $total_order }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Today Enquiry</h6>
                        </div>
                        <h3 class="mb-2">{{ $today_enquiry }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Today Orders</h6>
                        </div>
                        <h3 class="mb-2">{{ $today_order }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Today Orders Amount</h6>
                        </div>
                        <h3 class="mb-2">{{ formatIndianNumber($today_order_amount) }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a class="text-dark" href="{{ route('admin.commodity-product.index') }}" wire:navigate>
                <div class="card border border-danger">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Total Commodity Product</h6>
                        </div>
                        <h3 class="mb-2">{{ formatIndianNumber($total_commodity_product) }}</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
