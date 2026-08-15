<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @php
        $status = strtolower((string) ($requestRecord->request_status ?? 'draft'));
        $statusBadgeClass = match ($status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'submitted', 'under_review' => 'warning',
            default => 'muted',
        };
    @endphp

    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}

    <div class="row">
        <div class="col-12">
            <div class="card sr-gradient-card mb-3">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Seller Request Review</h3>
                            <div class="text-muted">Review application details and take a clear action.</div>
                        </div>
                        <a href="{{ route('admin.seller-request.index') }}" class="btn btn-secondary btn-sm" wire:navigate>
                            <i class="bi bi-arrow-left"></i>Back to List
                        </a>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="sr-kv">
                                <div class="sr-kv-label">Reference</div>
                                <div class="sr-kv-value">{{ $requestRecord->request_reference ?? 'Draft' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="sr-kv">
                                <div class="sr-kv-label">Status</div>
                                <div class="sr-kv-value">
                                    <span class="bz-status bz-status--{{ $statusBadgeClass }}">
                                        {{ str_replace('_', ' ', $requestRecord->request_status ?? 'draft') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="sr-kv">
                                <div class="sr-kv-label">Current Step</div>
                                <div class="sr-kv-value">{{ $requestRecord->current_step ?? 0 }}/6</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="sr-kv">
                                <div class="sr-kv-label">Requested By</div>
                                <div class="sr-kv-value">{{ $requestRecord->getUser?->name ?? '--' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, (int) (($requestRecord->current_step ?? 0) / 6 * 100)) }}%" aria-valuenow="{{ min(100, (int) (($requestRecord->current_step ?? 0) / 6 * 100)) }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-xl-8">
                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Business Information</h5>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Business Name</div><div class="sr-kv-value">{{ $requestRecord->company_name ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">GST Number</div><div class="sr-kv-value">{{ $requestRecord->gst_number ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">PAN Number</div><div class="sr-kv-value">{{ $requestRecord->pan_number ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Business Type</div><div class="sr-kv-value">{{ $requestRecord->business_type ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Constitution</div><div class="sr-kv-value">{{ $requestRecord->constitution_type ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Years in Business</div><div class="sr-kv-value">{{ $requestRecord->years_in_business ?? '--' }}</div></div></div>
                                        <div class="col-12"><div class="sr-kv"><div class="sr-kv-label">Address</div><div class="sr-kv-value">{{ $requestRecord->company_address ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">City</div><div class="sr-kv-value">{{ $requestRecord->city ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">State</div><div class="sr-kv-value">{{ $requestRecord->state ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Country</div><div class="sr-kv-value">{{ $requestRecord->country ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Pincode</div><div class="sr-kv-value">{{ $requestRecord->pincode ?? '--' }}</div></div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Contact & Bank Details</h5>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Contact Person</div><div class="sr-kv-value">{{ $requestRecord->contact_person ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Designation</div><div class="sr-kv-value">{{ $requestRecord->designation ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Mobile</div><div class="sr-kv-value">{{ $requestRecord->mobile ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Alternate Mobile</div><div class="sr-kv-value">{{ $requestRecord->alternate_mobile ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Email</div><div class="sr-kv-value">{{ $requestRecord->email ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Account Holder</div><div class="sr-kv-value">{{ $requestRecord->account_holder_name ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Bank Name</div><div class="sr-kv-value">{{ $requestRecord->bank_name ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Account Number</div><div class="sr-kv-value">{{ $requestRecord->account_number ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">IFSC Code</div><div class="sr-kv-value">{{ $requestRecord->ifsc_code ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Account Type</div><div class="sr-kv-value">{{ $requestRecord->account_type ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Branch</div><div class="sr-kv-value">{{ $requestRecord->branch ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Category</div><div class="sr-kv-value">{{ $requestRecord->category ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Turnover</div><div class="sr-kv-value">{{ $requestRecord->turnover ?? '--' }}</div></div></div>
                                        <div class="col-12"><div class="sr-kv"><div class="sr-kv-label">Products</div><div class="sr-kv-value">{{ $requestRecord->products ?? '--' }}</div></div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card sr-soft-card">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Documents</h5>
                                    <div class="row g-2">
                                        <div class="col-md-6 col-lg-4">
                                            @if($requestRecord->gst_certificate_path)
                                                <a href="{{ asset('storage/' . $requestRecord->gst_certificate_path) }}" target="_blank" class="btn btn-secondary btn-sm sr-doc-btn">GST Certificate</a>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm sr-doc-btn" disabled>GST Certificate (Not uploaded)</button>
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            @if($requestRecord->pan_document_path)
                                                <a href="{{ asset('storage/' . $requestRecord->pan_document_path) }}" target="_blank" class="btn btn-secondary btn-sm sr-doc-btn">PAN Document</a>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm sr-doc-btn" disabled>PAN Document (Not uploaded)</button>
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            @if($requestRecord->registration_certificate_path)
                                                <a href="{{ asset('storage/' . $requestRecord->registration_certificate_path) }}" target="_blank" class="btn btn-secondary btn-sm sr-doc-btn">Registration Certificate</a>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm sr-doc-btn" disabled>Registration Certificate (Not uploaded)</button>
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            @if($requestRecord->address_proof_path)
                                                <a href="{{ asset('storage/' . $requestRecord->address_proof_path) }}" target="_blank" class="btn btn-secondary btn-sm sr-doc-btn">Address Proof</a>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm sr-doc-btn" disabled>Address Proof (Not uploaded)</button>
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            @if($requestRecord->cancelled_cheque_path)
                                                <a href="{{ asset('storage/' . $requestRecord->cancelled_cheque_path) }}" target="_blank" class="btn btn-secondary btn-sm sr-doc-btn">Cancelled Cheque</a>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm sr-doc-btn" disabled>Cancelled Cheque (Not uploaded)</button>
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            @if($requestRecord->other_documents_path)
                                                <a href="{{ asset('storage/' . $requestRecord->other_documents_path) }}" target="_blank" class="btn btn-secondary btn-sm sr-doc-btn">Other Documents</a>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm sr-doc-btn" disabled>Other Documents (Not uploaded)</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card sr-soft-card mb-3 sticky-top" style="top: 90px;">
                                <div class="card-body">
                                    <h5 class="sr-section-title mb-2">Review Action</h5>
                                    <p class="text-muted small mb-3">Add a clear reason for approval or rejection for better audit history.</p>
                                    <div class="mb-3">
                                        <label class="form-label" for="reviewNote">Review Note / Rejection Reason</label>
                                        <textarea id="reviewNote" class="form-control" rows="5" wire:model.live="reviewNote" placeholder="Write your note here..."></textarea>
                                        @error('reviewNote') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-danger" wire:click="approve" @disabled(($requestRecord->request_status ?? '') === 'approved')>
                                            <i class="bi bi-check-lg"></i>Approve &amp; Create Seller
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" wire:click="reject">
                                            <i class="bi bi-x-lg"></i>Reject Request
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card sr-soft-card">
                                <div class="card-body">
                                    <h5 class="sr-section-title mb-3">Timeline</h5>
                                    @forelse(($requestRecord->timeline ?? []) as $event)
                                        <div class="sr-timeline-item mb-3">
                                            <div class="fw-semibold">{{ $event['label'] ?? $event['event'] ?? 'Update' }}</div>
                                            <div class="small text-muted">{{ dateTimeFormat($event['at']) ?? '--' }}</div>
                                            @if(!empty($event['note']))
                                                <div class="small mt-1">{{ $event['note'] }}</div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-muted">No timeline yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
