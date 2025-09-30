<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <!-- Enhanced Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                <i class="bi bi-diagram-3 me-2"></i>{{$page_title}}
            </h2>
            <p class="text-muted mb-0">Manage and view product-wise seller and buyer information</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Enhanced Filter Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-funnel text-primary me-2 fs-5"></i>
                            <h5 class="mb-0 fw-semibold">Search Filters</h5>
                        </div>
                        @if ($product_id || $brand_id || $city || $order_by || $price_validity)
                            <a class="btn btn-sm btn-outline-primary" href="{{route('admin.product-wise-seller-buyer.index')}}" wire:navigate>
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filters
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Enhanced Filter Grid -->
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="product_id" wire:model="product_id" wire:change="search()">
                                    <option value="">Choose Product...</option>
                                    @foreach ($product_list as $product_data)
                                        <option value="{{ $product_data->id }}">{{ $product_data->name }}</option>
                                    @endforeach
                                </select>
                                <label for="product_id">
                                    <i class="bi bi-box-seam me-1"></i>Commodity Product
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="brand_id" wire:model="brand_id" wire:change="search()">
                                    <option value="">Choose Brand...</option>
                                    @foreach ($brand_list ?? [] as $brand_data)
                                        <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                    @endforeach
                                </select>
                                <label for="brand_id">
                                    <i class="bi bi-tag me-1"></i>Brand
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="city" wire:model="city" wire:change="search()">
                                    <option value="">Choose Location...</option>
                                    @foreach ($seller_commodity_product_state ?? [] as $seller_commodity_product_state_data)
                                        <option value="{{ $seller_commodity_product_state_data->id }}">
                                            {{ $seller_commodity_product_state_data->state }} - {{ $seller_commodity_product_state_data->city }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="city">
                                    <i class="bi bi-geo-alt me-1"></i>City & State
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="order_by" wire:model="order_by" wire:change="search()">
                                    <option value="">Choose Sorting...</option>
                                    <option value="price_validity_asc">
                                        <i class="bi bi-sort-up"></i>Price Validity Ascending
                                    </option>
                                    <option value="price_validity_desc">
                                        <i class="bi bi-sort-down"></i>Price Validity Descending
                                    </option>
                                </select>
                                <label for="order_by">
                                    <i class="bi bi-sort-alpha-down me-1"></i>Sort Order
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="price_validity" wire:model="price_validity" wire:change="search()">
                                    <option value="">All Validities...</option>
                                    <option value="expired">
                                        <i class="bi bi-exclamation-triangle"></i>Expired
                                    </option>
                                    <option value="valid">
                                        <i class="bi bi-check-circle"></i>Valid
                                    </option>
                                </select>
                                <label for="price_validity">
                                    <i class="bi bi-calendar-check me-1"></i>Price Validity
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="quality" wire:model="quality" wire:change="search()">
                                    <option value="">Choose Quality...</option>
                                    @foreach ($quality_list as $quality)
                                        <option value="{{ $quality }}">{{ $quality }}</option>
                                    @endforeach
                                </select>
                                <label for="quality">
                                    <i class="bi bi-box-seam me-1"></i>Quality
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Data Display Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <!-- Modern Tab Navigation -->
                    <div class="border-bottom">
                        <ul class="nav nav-tabs border-0" id="dataTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3 fw-semibold" id="seller-tab" data-bs-toggle="tab" data-bs-target="#seller" type="button" role="tab" aria-controls="seller" aria-selected="true">
                                    <i class="bi bi-shop me-2"></i>
                                    Sellers
                                    <span class="badge bg-primary ms-2">{{ count($seller_list ?? []) }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3 fw-semibold" id="buyer-tab" data-bs-toggle="tab" data-bs-target="#buyer" type="button" role="tab" aria-controls="buyer" aria-selected="false">
                                    <i class="bi bi-people me-2"></i>
                                    Buyers
                                    <span class="badge bg-secondary ms-2">{{ count($customer_list ?? []) }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="dataTabContent">
                        <!-- Enhanced Seller Tab -->
                        <div class="tab-pane fade show active p-4" id="seller" role="tabpanel" aria-labelledby="seller-tab">
                            @if(count($seller_list ?? []) > 0)
                                <div class="accordion" id="sellerAccordion">
                                    @foreach ($seller_list ?? [] as $seller_data)
                                        <div class="accordion-item border rounded mb-3 shadow-sm">
                                            <h2 class="accordion-header" id="seller_heading_{{ $seller_data->id }}">
                                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#seller_collapse_{{ $seller_data->id }}" aria-expanded="false" aria-controls="seller_collapse_{{ $seller_data->id }}">
                                                    <div class="w-100">
                                                        <!-- Business Info Row -->
                                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-building text-primary me-2"></i>
                                                                <strong class="text-dark">{{ $seller_data->getUser->getBusiness->name}}</strong>
                                                            </div>
                                                            <div class="d-flex align-items-center text-muted">
                                                                <i class="bi bi-person me-1"></i>
                                                                <small>{{$seller_data->getUser->name}} - {{$seller_data->getUser->phone}}</small>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                                <small class="fw-semibold">{{$seller_data->getUser?->getUserDetail?->priority ?? 0}}</small>
                                                            </div>
                                                            @if($seller_data->getCommodityProduct)
                                                                <span class="badge {{ $seller_data->getCommodityProduct->status == 'active' ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                                                    {{ ucfirst($seller_data->getCommodityProduct->status) }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Details Row -->
                                                        <div class="row g-2 text-sm">
                                                            <div class="col-md-3">
                                                                <i class="bi bi-tag text-info me-1"></i>
                                                                <strong>Brand:</strong> {{ $seller_data->getBrand->name }}
                                                            </div>
                                                            <div class="col-md-3">
                                                                <i class="bi bi-geo-alt text-success me-1"></i>
                                                                <strong>Location:</strong> {{ $seller_data->getStatePrice[0]->state }}, {{ $seller_data->getStatePrice[0]->city }}
                                                            </div>
                                                            <div class="col-md-3">
                                                                <i class="bi bi-currency-rupee text-warning me-1"></i>
                                                                <strong>Base Price:</strong> ₹{{ number_format($seller_data->base_price ?? 0, 2) }}
                                                            </div>
                                                            @if($seller_data->quantity)
                                                            <div class="col-md-3">
                                                                <i class="bi bi-box text-secondary me-1"></i>
                                                                <strong>Quantity:</strong> {{ $seller_data->quantity ?? 0 }}
                                                            </div>
                                                            @endif
                                                        </div>

                                                        @if($seller_data->price_validity)
                                                        <div class="mt-2">
                                                            <i class="bi bi-calendar-event text-danger me-1"></i>
                                                            <strong>Valid Until:</strong>
                                                            <span class="text-danger">{{ dateTimeFormat($seller_data->price_validity) }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="seller_collapse_{{ $seller_data->id }}" class="accordion-collapse collapse" aria-labelledby="seller_heading_{{ $seller_data->id }}" data-bs-parent="#sellerAccordion">
                                                <div class="accordion-body bg-light">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">#</th>
                                                                    @php
                                                                        $attributes = $seller_data->getStatePrice[0]->value;
                                                                    @endphp
                                                                    @foreach ($attributes as $attribute)
                                                                        <th class="text-center">{{$attribute['name']}}</th>
                                                                    @endforeach
                                                                    <th class="text-center">
                                                                        <i class="bi bi-currency-rupee me-1"></i>Gauge Difference
                                                                    </th>
                                                                    <th class="text-center">
                                                                        <i class="bi bi-boxes me-1"></i>Stock
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($seller_data->getStatePrice as $state_price)
                                                                    <tr>
                                                                        <td class="text-center fw-bold">
                                                                            {{ $loop->iteration }}
                                                                            @if($state_price->is_selected)
                                                                                <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                                                            @endif
                                                                        </td>
                                                                        @foreach ($state_price->value as $price_value)
                                                                            <td class="text-center">{{ $price_value['value'] }}</td>
                                                                        @endforeach
                                                                        <td class="text-center fw-semibold text-primary">₹{{ number_format($state_price->price, 2) }}</td>
                                                                        <td class="text-center">{{ $state_price->stock ?? 0 }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="table-secondary">
                                                                <tr>
                                                                    <th colspan="{{count($attributes)+2}}" class="text-end">
                                                                        <i class="bi bi-truck me-1"></i>Loading Charge
                                                                    </th>
                                                                    <td class="text-center fw-bold">₹{{ number_format($seller_data->loading_charge ?? 0, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="{{count($attributes)+2}}" class="text-end">
                                                                        <i class="bi bi-shield-check me-1"></i>Insurance Charge
                                                                    </th>
                                                                    <td class="text-center fw-bold">₹{{ number_format($seller_data->insurance_charge ?? 0, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="{{count($attributes)+2}}" class="text-end">
                                                                        <i class="bi bi-award me-1"></i>Quality Charge
                                                                    </th>
                                                                    <td class="text-center fw-bold">₹{{ number_format($seller_data->quality_charge ?? 0, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="{{count($attributes)+2}}" class="text-end">
                                                                        <i class="bi bi-receipt me-1"></i>GST
                                                                    </th>
                                                                    <td class="text-center fw-bold">{{ $seller_data->gst ?? 0 }}%</td>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="{{count($attributes)+2}}" class="text-end">
                                                                        <i class="bi bi-calculator me-1"></i>TCS
                                                                    </th>
                                                                    <td class="text-center fw-bold">{{ $seller_data->tcs ?? 0 }}%</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-10">
                                                                <h5>Quality</h5>
                                                                @foreach ($seller_data->quality as $key => $quality)
                                                                    <span class="badge bg-info text-dark rounded-pill me-1 mb-1">{{ $quality }} - ₹ {{ $seller_data->quality_price[$key] ?? 0 }}</span>
                                                                @endforeach
                                                            </div>
                                                            <div class="col-2">
                                                                <button type="button" class="btn btn-outline-danger btn-xs btn-icon" data-bs-toggle="modal" data-bs-target="#exampleModal" title="View Calculation" wire:click="viewPriceCalculation({{ $seller_data->id }})">
                                                                    <i class="bi bi-info-circle"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-shop display-1 text-muted"></i>
                                    <h5 class="text-muted mt-3">No Sellers Found</h5>
                                    <p class="text-muted">Try adjusting your search filters to find sellers.</p>
                                </div>
                            @endif
                        </div>
                        <!-- Enhanced Buyer Tab -->
                        <div class="tab-pane fade p-4" id="buyer" role="tabpanel" aria-labelledby="buyer-tab">
                            @if(count($customer_list ?? []) > 0)
                                <div class="accordion" id="buyerAccordion">
                                    @foreach ($customer_list ?? [] as $customer_data)
                                        <div class="accordion-item border rounded mb-3 shadow-sm">
                                            <h2 class="accordion-header" id="customer_heading_{{ $customer_data->id }}">
                                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#customer_collapse_{{ $customer_data->id }}" aria-expanded="false" aria-controls="customer_collapse_{{ $customer_data->id }}">
                                                    <div class="d-flex align-items-center w-100">
                                                        <i class="bi bi-person-circle text-primary me-3 fs-4"></i>
                                                        <div>
                                                            <div class="fw-bold text-dark">
                                                                {{ $customer_data->getUserDetail ? $customer_data->getUserDetail->company_name : 'N/A' }}
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="bi bi-person me-1"></i>{{ $customer_data->name }}
                                                                <i class="bi bi-telephone ms-2 me-1"></i>{{ $customer_data->phone }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="customer_collapse_{{ $customer_data->id }}" class="accordion-collapse collapse" aria-labelledby="customer_heading_{{ $customer_data->id }}" data-bs-parent="#buyerAccordion">
                                                <div class="accordion-body bg-light">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="card border-0 bg-white h-100">
                                                                <div class="card-body text-center">
                                                                    <i class="bi bi-chat-quote display-6 text-info mb-2"></i>
                                                                    <h4 class="fw-bold text-info mb-1">{{ $customer_data->total_enquiry }}</h4>
                                                                    <p class="text-muted mb-0">Total Enquiries</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card border-0 bg-white h-100">
                                                                <div class="card-body text-center">
                                                                    <i class="bi bi-cart-check display-6 text-primary mb-2"></i>
                                                                    <h4 class="fw-bold text-primary mb-1">{{ $customer_data->total_order }}</h4>
                                                                    <p class="text-muted mb-0">Total Orders</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card border-0 bg-white h-100">
                                                                <div class="card-body text-center">
                                                                    <i class="bi bi-truck display-6 text-success mb-2"></i>
                                                                    <h4 class="fw-bold text-success mb-1">{{ $customer_data->total_dispatched_order }}</h4>
                                                                    <p class="text-muted mb-0">Dispatched Orders</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card border-0 bg-white h-100">
                                                                <div class="card-body text-center">
                                                                    <i class="bi bi-hourglass display-6 text-warning mb-2"></i>
                                                                    <h4 class="fw-bold text-warning mb-1">0</h4>
                                                                    <p class="text-muted mb-0">Pending Orders</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Order Summary Chart Placeholder -->
                                                    <div class="mt-4 p-3 bg-white rounded border">
                                                        <h6 class="text-muted mb-3">
                                                            <i class="bi bi-bar-chart me-2"></i>Order Summary
                                                        </h6>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="progress flex-grow-1 me-3" style="height: 20px;">
                                                                @php
                                                                    $total_orders = $customer_data->total_order;
                                                                    $dispatched_percentage = $total_orders > 0 ? ($customer_data->total_dispatched_order / $total_orders) * 100 : 0;
                                                                @endphp
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $dispatched_percentage }}%" aria-valuenow="{{ $dispatched_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                                    {{ round($dispatched_percentage, 1) }}% Completed
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">{{ $customer_data->total_dispatched_order }}/{{ $customer_data->total_order }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-people display-1 text-muted"></i>
                                    <h5 class="text-muted mt-3">No Buyers Found</h5>
                                    <p class="text-muted">Try adjusting your search filters to find buyers.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Calculation</h5>
                </div>
                <div class="modal-body">
                    @if($detail)
                        <b>Base Price:</b> Rs {{ formatIndianNumber($detail['base_price']) }} <br>
                        <b>Guage Difference:</b> + Rs {{ formatIndianNumber($detail['default_variation_price']) }} <br>
                        @if($detail['loading_charge'] > 0)
                            <b>Loading Charge:</b> + Rs {{ formatIndianNumber($detail['loading_charge']) }} <br>
                        @endif
                        @if($detail['insurance_charge'] > 0)
                            <b>Insurance Charge:</b> + Rs {{ formatIndianNumber($detail['insurance_charge']) }} <br>
                        @endif
                        @if ($detail['quality_charge'] > 0)
                            <b>Quality Charge: </b>+ Rs {{ formatIndianNumber($detail['quality_charge']) }} <br>
                        @endif

                        @foreach ($detail['other_charges'] as $charge)
                            <b>{{ $charge['name'] }}:</b> {{ $charge['operator'] }} Rs {{ formatIndianNumber($charge['price']) }} <br>
                        @endforeach


                        <br>
                        <b>Total:</b> Rs {{ formatIndianNumber($detail['total_amount']) }} <br>
                        <b>GST:</b> + {{ $detail['gst'] }} % <br>
                        <b>Ex Price:</b> Rs {{ formatIndianNumber($detail['ex_price']) }}
                    @else
                        <div class="text-center">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal" wire:click="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Custom Styles -->
    <style>
        .form-floating > .form-select {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .form-floating > label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(13, 110, 253, 0.1);
            border-color: rgba(13, 110, 253, 0.25);
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            background: none;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: #0d6efd;
            background: none;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .custom-shadow {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .hover-lift {
            transition: transform 0.2s ease-in-out;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>
</div>
