<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @can('dashboard')
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
            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 mb-3">
                <div class="card border border-danger">
                    <div class="card-header bg-transparent border-danger d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            New Buyer Registrations
                            <span class="badge bg-danger">{{ $customer_list->total() }}</span>
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="custom-search-bar me-2">
                                <div class="input-group">
                                    <span class="input-group-text"> <i data-feather="search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                                </div>
                            </div>
                            <a href="{{ route('admin.customer-list') }}" wire:navigate class="btn btn-sm btn-danger">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($customer_list->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Detail</th>
                                            <th scope="col">Contact Info</th>
                                            <th scope="col">Registered At</th>
                                            <th scope="col">KYC Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customer_list as $item)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>
                                                    <b>Name: </b> {{ $item->name }} <br>
                                                    <b>Company Name: </b> {{ $item->getUserDetail ? $item->getUserDetail->company_name : '--' }} <br>
                                                    <b>Address :</b> {{ $item->getUserDetail ? $item->getUserDetail->company_address : '--' }}
                                                </td>
                                                <td>
                                                    <i class="bi bi-telephone"></i><span class="ms-2">{{ $item->phone }}</span>
                                                    <br>
                                                    <i class="bi bi-envelope-at"></i><span class="ms-2">{{ $item->email }}</span>
                                                </td>
                                                <td>{{ dateTimeFormat($item->created_at) }}</td>
                                                <td>
                                                    <select name="kyc_status" id="kycSelect{{ $item->id }}" class="form-select form-select-sm" wire:change="changeKycStatus({{ $item->id }}, $event.target.value)">
                                                        <option value="pending" {{ $item->kyc_status == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                                        <option value="approved" {{ $item->kyc_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="rejected" {{ $item->kyc_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>

                                                    @if($item->kyc_status == 'rejected' && $item->kyc_description)
                                                        <small class="text-muted d-block mt-1">
                                                            <strong>Reason:</strong> {{ Str::limit($item->kyc_description, 50) }}
                                                        </small>
                                                    @endif

                                                    <!-- Rejection Modal -->
                                                    <div class="modal fade" id="rejectionModal{{ $item->id }}" tabindex="-1" aria-labelledby="rejectionModalLabel{{ $item->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="rejectionModalLabel{{ $item->id }}">Rejection Reason</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cancelRejection()"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <textarea class="form-control" rows="4" placeholder="Enter rejection reason..." wire:model="rejectionReasons.{{ $item->id }}"></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cancelRejection()">Cancel</button>
                                                                    <button type="button" class="btn btn-danger" wire:click="submitRejection({{ $item->id }})" data-bs-dismiss="modal">Submit</button>
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
                            <div class="mt-3">
                                {{ $customer_list->links() }}
                            </div>
                        @else
                            <p class="text-center">No new buyers found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            You are not authorized to access this page.
        </div>
    @endcan
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    // Listen for the showRejectionModal event
    Livewire.on('showRejectionModal', (data) => {
        const modal = new bootstrap.Modal(document.getElementById('rejectionModal' + data.userId));
        modal.show();
    });

    // Listen for resetSelectValue event
    Livewire.on('resetSelectValue', () => {
        // Reset all select dropdowns to their original values
        document.querySelectorAll('select[name="kyc_status"]').forEach(select => {
            const originalValue = select.querySelector('option[selected]')?.value || 'pending';
            select.value = originalValue;
        });
    });

    // Handle modal close events to reset select dropdown
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            // Get the user ID from modal ID
            const modalId = this.id;
            const userId = modalId.replace('rejectionModal', '');
            const selectElement = document.getElementById('kycSelect' + userId);

            if (selectElement) {
                // Reset to original value
                const originalValue = selectElement.querySelector('option[selected]')?.value || 'pending';
                selectElement.value = originalValue;
            }
        });
    });
});
</script>
