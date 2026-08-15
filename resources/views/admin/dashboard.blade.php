<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @can('dashboard')
        @php
            $bzName = auth()->user()->name ?? 'there';
            $bzFirstName = explode(' ', trim($bzName))[0];
            $bzHour = (int) now()->format('G');
            $bzGreeting = $bzHour < 12 ? 'Good morning' : ($bzHour < 17 ? 'Good afternoon' : 'Good evening');

            // Component builds these newest-first; charts read left to right.
            $bzTrendDays = collect($last_7_days_customer)->reverse()->values();
            $bzTrendLabels = $bzTrendDays->map(fn ($d) => \Carbon\Carbon::parse($d['date'])->format('d M'))->all();
            $bzTrendBuyers = $bzTrendDays->pluck('count')->all();
            $bzTrendSellers = collect($last_7_days_seller)->reverse()->pluck('count')->all();
            $bzNewThisWeek = array_sum($bzTrendBuyers) + array_sum($bzTrendSellers);
        @endphp

        <div class="bz-page-head">
            <div>
                <h1 class="bz-page-head__title">{{ $bzGreeting }}, {{ $bzFirstName }}</h1>
                <p class="bz-page-head__sub">
                    Here's what's happening on Biznie today — {{ now()->format('l, d M Y') }}
                </p>
            </div>
            <div class="bz-page-head__actions">
                <a href="{{ route('admin.commodity-product-enquiry.index') }}" wire:navigate class="btn btn-secondary btn-sm">
                    <i class="bi bi-journal-check"></i> Enquiries
                </a>
                <a href="{{ route('admin.commodity-product-order.index') }}" wire:navigate class="btn btn-danger btn-sm">
                    <i class="bi bi-box-seam"></i> View Orders
                </a>
            </div>
        </div>

        {{-- ---------------------------------------------------------------
             Today
        ---------------------------------------------------------------- --}}
        <h2 class="bz-section-label">Today</h2>
        <div class="bz-stat-grid">
            <a class="bz-stat bz-stat--amber" href="{{ route('admin.commodity-product-enquiry.index') }}" wire:navigate>
                <div class="bz-stat__top">
                    <span class="bz-stat__label">Enquiries received</span>
                    <span class="bz-stat__icon"><i class="bi bi-chat-left-dots"></i></span>
                </div>
                <div class="bz-stat__value">{{ formatIndianNumber($today_enquiry) }}</div>
                <div class="bz-stat__foot"><i class="bi bi-clock-history"></i> Since midnight</div>
            </a>

            <a class="bz-stat bz-stat--blue" href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                <div class="bz-stat__top">
                    <span class="bz-stat__label">Orders placed</span>
                    <span class="bz-stat__icon"><i class="bi bi-box-seam"></i></span>
                </div>
                <div class="bz-stat__value">{{ formatIndianNumber($today_order) }}</div>
                <div class="bz-stat__foot"><i class="bi bi-clock-history"></i> Since midnight</div>
            </a>

            <a class="bz-stat bz-stat--green" href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                <div class="bz-stat__top">
                    <span class="bz-stat__label">Order value</span>
                    <span class="bz-stat__icon"><i class="bi bi-currency-rupee"></i></span>
                </div>
                <div class="bz-stat__value">₹{{ formatIndianNumber($today_order_amount) }}</div>
                <div class="bz-stat__foot"><i class="bi bi-clock-history"></i> Since midnight</div>
            </a>

            <div class="bz-stat bz-stat--violet">
                <div class="bz-stat__top">
                    <span class="bz-stat__label">New sign-ups this week</span>
                    <span class="bz-stat__icon"><i class="bi bi-person-plus"></i></span>
                </div>
                <div class="bz-stat__value">{{ formatIndianNumber($bzNewThisWeek) }}</div>
                <div class="bz-stat__foot"><i class="bi bi-calendar3"></i> Buyers + sellers, last 7 days</div>
            </div>
        </div>

        {{-- ---------------------------------------------------------------
             Trend + marketplace totals
        ---------------------------------------------------------------- --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-7">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5>Registrations — last 7 days</h5>
                            <p class="bz-card-sub mb-0">New buyer and seller accounts per day</p>
                        </div>
                        <div class="bz-legend">
                            <span class="bz-legend__item"><i style="background:#C81E1E"></i>Buyers</span>
                            <span class="bz-legend__item"><i style="background:#2563EB"></i>Sellers</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div wire:ignore class="flex-grow-1" style="min-height:300px">
                            <div id="bzSignupChart"
                                data-labels='@json($bzTrendLabels)'
                                data-buyers='@json($bzTrendBuyers)'
                                data-sellers='@json($bzTrendSellers)'
                                style="height:100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h5>Marketplace totals</h5>
                        <p class="bz-card-sub mb-0">All-time counts across the platform</p>
                    </div>
                    <div class="card-body">
                        <ul class="bz-metric-list">
                            <li>
                                <a href="{{ route('admin.customer-list') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-blue"><i class="bi bi-people"></i></span>
                                    <span class="bz-metric-list__label">Buyers</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_customer) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.seller.index') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-brand"><i class="bi bi-shop"></i></span>
                                    <span class="bz-metric-list__label">Sellers</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_seller) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.transporter.index') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-cyan"><i class="bi bi-truck"></i></span>
                                    <span class="bz-metric-list__label">Transporters</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_transporter) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.commodity-product.index') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-green"><i class="bi bi-boxes"></i></span>
                                    <span class="bz-metric-list__label">Active commodity products</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_commodity_product) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.brand') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-violet"><i class="bi bi-award"></i></span>
                                    <span class="bz-metric-list__label">Brands</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_brand) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.commodity-product-enquiry.index') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-amber"><i class="bi bi-journal-check"></i></span>
                                    <span class="bz-metric-list__label">Enquiries</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_enquiry) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.commodity-product-order.index') }}" wire:navigate>
                                    <span class="bz-metric-list__icon bz-tone-slate"><i class="bi bi-box-seam"></i></span>
                                    <span class="bz-metric-list__label">Orders</span>
                                    <span class="bz-metric-list__value">{{ formatIndianNumber($total_order) }}</span>
                                    <i class="bi bi-chevron-right bz-metric-list__chev"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---------------------------------------------------------------
             KYC queue
        ---------------------------------------------------------------- --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5>
                        Buyer KYC queue
                        <span class="badge bg-warning">{{ $customer_list->total() }} pending</span>
                    </h5>
                    <p class="bz-card-sub mb-0">Buyers awaiting verification or previously rejected</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="custom-search-bar">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search buyers…"
                                aria-label="Search buyers awaiting KYC"
                                wire:model.live.debounce.400ms="search">
                        </div>
                    </div>
                    <a href="{{ route('admin.customer-list') }}" wire:navigate class="btn btn-secondary btn-sm">View all</a>
                </div>
            </div>
            <div class="card-body">
                @if ($customer_list->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:44px">#</th>
                                    <th>Buyer</th>
                                    <th>Contact</th>
                                    <th>Tax identity</th>
                                    <th>Registered</th>
                                    <th style="width:190px">KYC status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer_list as $item)
                                    <tr wire:key="kyc-{{ $item->id }}">
                                        <td class="text-muted">{{ $customer_list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $item->name }}</div>
                                            <div class="bz-cell-sub">
                                                {{ $item->getUserDetail->company_name ?? '—' }}
                                            </div>
                                            @if ($item->getUserDetail?->company_address)
                                                <div class="bz-cell-sub">
                                                    <i class="bi bi-geo-alt"></i>
                                                    {{ Str::limit($item->getUserDetail->company_address, 48) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="bz-cell-sub"><i class="bi bi-telephone"></i> {{ $item->phone ?: '—' }}</div>
                                            <div class="bz-cell-sub"><i class="bi bi-envelope"></i> {{ $item->email ?: '—' }}</div>
                                        </td>
                                        <td>
                                            <div class="bz-cell-sub"><b>GSTIN</b> {{ $item->getUserDetail->gst_number ?? '—' }}</div>
                                            <div class="bz-cell-sub"><b>PAN</b> {{ $item->getUserDetail->pan_number ?? '—' }}</div>
                                        </td>
                                        <td class="text-nowrap">{{ dateTimeFormat($item->created_at) }}</td>
                                        <td>
                                            @if ($item->kyc_status == 'rejected')
                                                <span class="bz-status bz-status--danger mb-2">Rejected</span>
                                            @else
                                                <span class="bz-status bz-status--warning mb-2">Pending</span>
                                            @endif

                                            <select name="kyc_status" id="kycSelect{{ $item->id }}"
                                                class="form-select form-select-sm"
                                                aria-label="Change KYC status for {{ $item->name }}"
                                                wire:change="changeKycStatus({{ $item->id }}, $event.target.value)">
                                                <option value="pending" {{ $item->kyc_status == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                <option value="approved" {{ $item->kyc_status == 'approved' ? 'selected' : '' }}>Approve</option>
                                                <option value="rejected" {{ $item->kyc_status == 'rejected' ? 'selected' : '' }}>Reject</option>
                                            </select>

                                            @if ($item->kyc_status == 'rejected' && $item->kyc_description)
                                                <div class="bz-cell-sub mt-1">
                                                    <b>Reason</b> {{ Str::limit($item->kyc_description, 50) }}
                                                </div>
                                            @endif

                                            <!-- Rejection Modal -->
                                            <div class="modal fade" id="rejectionModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="rejectionModalLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectionModalLabel{{ $item->id }}">
                                                                Reject KYC — {{ $item->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close" wire:click="cancelRejection()"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label class="form-label" for="reason{{ $item->id }}">Reason for rejection</label>
                                                            <textarea id="reason{{ $item->id }}" class="form-control" rows="4"
                                                                placeholder="Explain what the buyer needs to correct…"
                                                                wire:model="rejectionReasons.{{ $item->id }}"></textarea>
                                                            <div class="form-text">This is shown to the buyer, so be specific.</div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal" wire:click="cancelRejection()">Cancel</button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                wire:click="submitRejection({{ $item->id }})"
                                                                data-bs-dismiss="modal">Reject KYC</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($customer_list->hasPages())
                        <div class="mt-3">{{ $customer_list->links() }}</div>
                    @endif
                @else
                    <div class="bz-empty">
                        <span class="bz-empty__icon"><i class="bi bi-patch-check"></i></span>
                        <span class="bz-empty__title">Nothing waiting on you</span>
                        <p class="bz-empty__text">
                            {{ $search ? 'No buyer matches that search.' : 'Every buyer registration has been reviewed.' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            You are not authorized to access this page.
        </div>
    @endcan
</div>

<script>
    (function () {
        function bzRenderSignupChart() {
            var el = document.getElementById('bzSignupChart');
            if (!el || typeof ApexCharts === 'undefined') { return; }
            if (el.dataset.rendered === '1') { return; }
            el.dataset.rendered = '1';

            var labels = JSON.parse(el.dataset.labels || '[]');
            var buyers = JSON.parse(el.dataset.buyers || '[]');
            var sellers = JSON.parse(el.dataset.sellers || '[]');

            // With an all-zero series Apex cannot derive a scale and prints "Infinity".
            var peak = Math.max(0, ...buyers, ...sellers);
            var yMax = peak > 0 ? undefined : 4;

            new ApexCharts(el, {
                chart: {
                    type: 'area',
                    height: '100%',
                    fontFamily: 'Inter, Roboto, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    parentHeightOffset: 0
                },
                series: [
                    { name: 'Buyers', data: buyers },
                    { name: 'Sellers', data: sellers }
                ],
                colors: ['#C81E1E', '#2563EB'],
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: .25, opacityTo: 0, stops: [0, 90] }
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                grid: {
                    borderColor: '#EEF1F5',
                    strokeDashArray: 4,
                    padding: { left: 4, right: 4, top: -8 }
                },
                xaxis: {
                    categories: labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#9AA3B2', fontSize: '11px' } }
                },
                yaxis: {
                    min: 0,
                    max: yMax,
                    tickAmount: Math.min(4, Math.max(2, peak || 4)),
                    labels: {
                        formatter: function (v) { return Math.round(v); },
                        style: { colors: '#9AA3B2', fontSize: '11px' }
                    }
                },
                tooltip: { theme: 'light', x: { show: true } }
            }).render();
        }

        document.addEventListener('livewire:navigated', bzRenderSignupChart);
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bzRenderSignupChart);
        } else {
            bzRenderSignupChart();
        }
    })();

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showRejectionModal', (data) => {
            const modal = new bootstrap.Modal(document.getElementById('rejectionModal' + data.userId));
            modal.show();
        });

        Livewire.on('resetSelectValue', () => {
            document.querySelectorAll('select[name="kyc_status"]').forEach(select => {
                const originalValue = select.querySelector('option[selected]')?.value || 'pending';
                select.value = originalValue;
            });
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function () {
                const userId = this.id.replace('rejectionModal', '');
                const selectElement = document.getElementById('kycSelect' + userId);
                if (selectElement) {
                    const originalValue = selectElement.querySelector('option[selected]')?.value || 'pending';
                    selectElement.value = originalValue;
                }
            });
        });
    });
</script>
