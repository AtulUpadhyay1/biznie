<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="bz-page-head">
        <div>
            <h1 class="bz-page-head__title">{{ $page_title }}</h1>
            <p class="bz-page-head__sub">Manage and view product-wise seller and buyer information</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Enhanced Filter Card -->
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-funnel me-2"></i>
                        <h5 class="mb-0">Search Filters</h5>
                    </div>
                    @if ($product_id || $brand_id || $city || $order_by || $price_validity)
                        <div class="bz-toolbar">
                            <a class="btn btn-sm btn-secondary" href="{{route('admin.product-wise-seller-buyer.index')}}" wire:navigate>
                                <i class="bi bi-arrow-clockwise"></i>Reset Filters
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Enhanced Filter Grid -->
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="product_id">Commodity Product</label>
                            <select class="form-select" id="product_id" wire:model="product_id" wire:change="search()">
                                <option value="">Choose Product...</option>
                                @foreach ($product_list as $product_data)
                                    <option value="{{ $product_data->id }}">{{ $product_data->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="brand_id">Brand</label>
                            <select class="form-select" id="brand_id" wire:model="brand_id" wire:change="search()">
                                <option value="">Choose Brand...</option>
                                @foreach ($brand_list ?? [] as $brand_data)
                                    <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="city">City &amp; State</label>
                            <select class="form-select" id="city" wire:model="city" wire:change="search()">
                                <option value="">Choose Location...</option>
                                @foreach ($seller_commodity_product_state ?? [] as $seller_commodity_product_state_data)
                                    <option value="{{ $seller_commodity_product_state_data->id }}">
                                        {{ $seller_commodity_product_state_data->state }} - {{ $seller_commodity_product_state_data->city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="order_by">Sort Order</label>
                            <select class="form-select" id="order_by" wire:model="order_by" wire:change="search()">
                                <option value="">Choose Sorting...</option>
                                <option value="price_validity_asc">Price Validity Ascending</option>
                                <option value="price_validity_desc">Price Validity Descending</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="price_validity">Price Validity</label>
                            <select class="form-select" id="price_validity" wire:model="price_validity" wire:change="search()">
                                <option value="">All Validities...</option>
                                <option value="expired">Expired</option>
                                <option value="valid">Valid</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="quality">Quality</label>
                            <select class="form-select" id="quality" wire:model="quality" wire:change="search()">
                                <option value="">Choose Quality...</option>
                                @foreach ($quality_list as $quality)
                                    <option value="{{ $quality }}">{{ $quality }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Data Display Card -->
            <div class="card">
                <div class="card-body p-0">
                    <!-- Modern Tab Navigation -->
                    <div class="border-bottom">
                        <ul class="nav nav-tabs" id="dataTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="seller-tab" data-bs-toggle="tab" data-bs-target="#seller" type="button" role="tab" aria-controls="seller" aria-selected="true">
                                    <i class="bi bi-shop me-2"></i>
                                    Sellers
                                    <span class="badge bg-primary ms-2">{{ count($seller_list ?? []) }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="buyer-tab" data-bs-toggle="tab" data-bs-target="#buyer" type="button" role="tab" aria-controls="buyer" aria-selected="false">
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
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="seller_heading_{{ $seller_data->id }}">
                                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#seller_collapse_{{ $seller_data->id }}" aria-expanded="false" aria-controls="seller_collapse_{{ $seller_data->id }}">
                                                    <div class="w-100">
                                                        <!-- Business Info Row -->
                                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-building me-2"></i>
                                                                <strong>{{ $seller_data->getUser->getBusiness->name}}</strong>
                                                            </div>
                                                            <div class="d-flex align-items-center text-muted">
                                                                <i class="bi bi-person me-1"></i>
                                                                <small>{{$seller_data->getUser->name}} - {{$seller_data->getUser->phone}}</small>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                                <small>{{$seller_data->getUser?->getUserDetail?->priority ?? 0}}</small>
                                                            </div>
                                                            @if($seller_data->getCommodityProduct)
                                                                <span class="bz-status {{ $seller_data->getCommodityProduct->status == 'active' ? 'bz-status--success' : 'bz-status--danger' }}">
                                                                    {{ ucfirst($seller_data->getCommodityProduct->status) }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Details Row -->
                                                        <div class="row g-2">
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
                                                                        <td class="text-center bz-num">₹{{ number_format($state_price->price, 2) }}</td>
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
                                                                    <span class="bz-chip">{{ $quality }} - ₹ {{ $seller_data->quality_price[$key] ?? 0 }}</span>
                                                                @endforeach
                                                            </div>
                                                            <div class="col-2">
                                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal" title="View Calculation" wire:click="viewPriceCalculation({{ $seller_data->id }})">
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
                                <div class="bz-empty">
                                    <span class="bz-empty__icon"><i class="bi bi-shop"></i></span>
                                    <span class="bz-empty__title">No Sellers Found</span>
                                    <p class="bz-empty__text">Try adjusting your search filters to find sellers.</p>
                                </div>
                            @endif
                        </div>
                        <!-- Enhanced Buyer Tab -->
                        <div class="tab-pane fade p-4" id="buyer" role="tabpanel" aria-labelledby="buyer-tab">
                            @if(count($customer_list ?? []) > 0)
                                <div class="accordion" id="buyerAccordion">
                                    @foreach ($customer_list ?? [] as $customer_data)
                                        <div class="accordion-item mb-3">
                                            <h2 class="accordion-header" id="customer_heading_{{ $customer_data->id }}">
                                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#customer_collapse_{{ $customer_data->id }}" aria-expanded="false" aria-controls="customer_collapse_{{ $customer_data->id }}">
                                                    <div class="d-flex align-items-center w-100">
                                                        <i class="bi bi-person-circle me-3 fs-4"></i>
                                                        <div>
                                                            <div class="fw-bold">
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
                                                    <div class="bz-stat-grid">
                                                        <div class="bz-stat bz-stat--cyan">
                                                            <div class="bz-stat__top">
                                                                <span class="bz-stat__label">Total Enquiries</span>
                                                                <span class="bz-stat__icon"><i class="bi bi-chat-quote"></i></span>
                                                            </div>
                                                            <div class="bz-stat__value bz-num">{{ $customer_data->total_enquiry }}</div>
                                                        </div>

                                                        <div class="bz-stat bz-stat--brand">
                                                            <div class="bz-stat__top">
                                                                <span class="bz-stat__label">Total Orders</span>
                                                                <span class="bz-stat__icon"><i class="bi bi-cart-check"></i></span>
                                                            </div>
                                                            <div class="bz-stat__value bz-num">{{ $customer_data->total_order }}</div>
                                                        </div>

                                                        <div class="bz-stat bz-stat--green">
                                                            <div class="bz-stat__top">
                                                                <span class="bz-stat__label">Dispatched Orders</span>
                                                                <span class="bz-stat__icon"><i class="bi bi-truck"></i></span>
                                                            </div>
                                                            <div class="bz-stat__value bz-num">{{ $customer_data->total_dispatched_order }}</div>
                                                        </div>

                                                        <div class="bz-stat bz-stat--amber">
                                                            <div class="bz-stat__top">
                                                                <span class="bz-stat__label">Pending Orders</span>
                                                                <span class="bz-stat__icon"><i class="bi bi-hourglass"></i></span>
                                                            </div>
                                                            <div class="bz-stat__value bz-num">0</div>
                                                        </div>
                                                    </div>

                                                    <!-- Order Summary Chart Placeholder -->
                                                    <div class="bz-row-card p-3 mt-3">
                                                        <h6 class="bz-section-label mb-3">
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
                                <div class="bz-empty">
                                    <span class="bz-empty__icon"><i class="bi bi-people"></i></span>
                                    <span class="bz-empty__title">No Buyers Found</span>
                                    <p class="bz-empty__text">Try adjusting your search filters to find buyers.</p>
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
                            <span class="bz-spinner" role="status" aria-label="Loading"></span>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" wire:click="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Custom Styles -->
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
</div>
