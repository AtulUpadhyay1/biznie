<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.seller.seller_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Seller Kyc Detail</h4>
                    <div class="bz-toolbar">
                        @if (
                            ($data->getSellerKycDetail && $data->getSellerKycDetail->status == 'uploaded') ||
                                $data->getSellerKycDetail->status == 'pending')
                            <label class="bz-filter-label" for="kyc_status">KYC Status</label>
                            <select class="form-select" id="kyc_status" wire:model="status"
                                wire:change="updateStatus()">
                                <option value="pending" disabled>Pending</option>
                                <option value="uploaded" disabled>Uploaded</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        @endif
                        @if (isset($data->status) && $data->status === 'active')
                            <button type="button" class="btn btn-danger btn-sm" wire:click.prevent="openBlockModal">
                                <i class="bi bi-slash-circle"></i>
                                Block
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" wire:click.prevent="unblock">
                                <i class="bi bi-unlock"></i>
                                Unblock
                            </button>
                        @endif
                    </div>
                    <!-- Block Reason Modal -->
                    <div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel"
                        aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="blockModalLabel">Block Seller - Reason</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="blockReason" class="form-label">Reason</label>
                                        <textarea id="blockReason" class="form-control" rows="4" wire:model.defer="blockReason"
                                            placeholder="Enter reason for blocking the seller"></textarea>
                                        @error('blockReason')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        wire:click.prevent="confirmBlock">Confirm Block</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        window.addEventListener('open-block-modal', event => {
                            const modalEl = document.getElementById('blockModal');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                const modal = new bootstrap.Modal(modalEl);
                                modal.show();
                            }
                        });
                        window.addEventListener('close-block-modal', event => {
                            const modalEl = document.getElementById('blockModal');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                const instance = bootstrap.Modal.getInstance(modalEl);
                                if (instance) instance.hide();
                            }
                        });
                    </script>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6 class="bz-section-label">Seller Permissions</h6>
                        <div class="mb-4">
                            <div class="bz-toggle-row">
                                <div>
                                    <span class="bz-toggle-row__label">F.O.R Prices</span>
                                    <span class="bz-toggle-row__hint">
                                        When enabled, the seller can add and update their own city-wise
                                        F.O.R prices from their product page. Off by default — buyers
                                        then see the calculated F.O.R price (Ex Price + freight).
                                    </span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="forPriceAccess" wire:model="forPriceAccess"
                                        wire:change="updateForPriceAccess">
                                    <label class="form-check-label" for="forPriceAccess">
                                        {{ $forPriceAccess ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </div>
                            <div class="bz-toggle-row">
                                <div>
                                    <span class="bz-toggle-row__label">F.O.B Price</span>
                                    <span class="bz-toggle-row__hint">
                                        When enabled, the seller can add and update their own F.O.B
                                        prices from their product page. Off by default.
                                    </span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="fobPriceAccess" wire:model="fobPriceAccess"
                                        wire:change="updateFobPriceAccess">
                                    <label class="form-check-label" for="fobPriceAccess">
                                        {{ $fobPriceAccess ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <h6 class="bz-section-label">Seller Details</h6>
                        <div class="bz-panel mb-4">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Business Name</dt>
                                    <dd>{{ $data->getBusiness ? $data->getBusiness->name : '' }}</dd>
                                </div>
                                <div>
                                    <dt>GST Number</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->gst_number : '' }}</dd>
                                </div>
                                <div>
                                    <dt>PAN</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->identity_number : '' }}</dd>
                                </div>
                                <div>
                                    <dt>Customer Name</dt>
                                    <dd>{{ $data->name }}</dd>
                                </div>
                                <div>
                                    <dt>Mobile Number</dt>
                                    <dd>{{ $data->phone }}</dd>
                                </div>
                                <div>
                                    <dt>City</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->city : '' }}</dd>
                                </div>
                                <div>
                                    <dt>Email</dt>
                                    <dd>{{ $data->email }}</dd>
                                </div>
                            </dl>
                        </div>
                        <h6 class="bz-section-label">Address Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Address:</b><span
                                                class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->address : '' }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="bz-section-label">Bank Details</h6>
                        <div class="bz-panel mb-4">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Account Number</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->account_number : '' }}</dd>
                                </div>
                                <div>
                                    <dt>Account Name</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->account_holder_name : '' }}</dd>
                                </div>
                                <div>
                                    <dt>Bank Name</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->bank_name : '' }}</dd>
                                </div>
                                <div>
                                    <dt>IFSC Code</dt>
                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->ifsc_code : '' }}</dd>
                                </div>
                            </dl>
                            {{-- <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Account Number:</b><span
                                                class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->account_number : '' }}</span>
                                        </td>
                                        <td><b>Account Name:</b><span
                                                class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->account_holder_name : '' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Bank Name:</b><span
                                                class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->bank_name : '' }}</span>
                                        </td>
                                        <td><b>Ifsc Code:</b><span
                                                class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->ifsc_code : '' }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> --}}
                        </div>
                        <h6 class="bz-section-label">More Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered mt-2">
                                <tbody>
                                    <tr>
                                        <td><b>Identity Type:</b></td>
                                        <td>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->identity_type : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Identity Proof:</b></td>
                                        <td><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->identity_proof) : '' }}"
                                                alt="Identity Proof"
                                                onerror="this.onerror=null; this.src='{{ asset('admin_css/no-photo.png') }}'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Address Type:</b></td>
                                        <td>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->address_type : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Address Proof:</b></td>
                                        <td><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->address_proof) : '' }}"
                                                alt="Address Proof"
                                                onerror="this.onerror=null; this.src='{{ asset('admin_css/no-photo.png') }}'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Business Registration Number:</b></td>
                                        <td>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->business_registration_number : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Business Registration Certificate:</b></td>
                                        <td><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->business_registration_certificate) : '' }}"
                                                alt="Business Registration Certificate"
                                                onerror="this.onerror=null; this.src='{{ asset('admin_css/no-photo.png') }}'">
                                        </td>
                                    </tr>
                                    @if ($data->getSellerKycDetail && $data->getSellerKycDetail->trademark_registration_proof)
                                        <tr>
                                            <td><b>Trademark Registration Proof:</b></td>
                                            <td><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->trademark_registration_proof) : '' }}"
                                                    alt="Trademark Registration Proof"
                                                    onerror="this.onerror=null; this.src='{{ asset('admin_css/no-photo.png') }}'">
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><b>GST Type:</b></td>
                                        <td>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->gst_type : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>GST Number:</b></td>
                                        <td>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->gst_number : '' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
