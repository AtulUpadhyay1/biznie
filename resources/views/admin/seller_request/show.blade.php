<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Seller Request Review</h4>
                    <a href="{{ route('admin.seller-request.index') }}" class="btn btn-danger btn-sm" wire:navigate>Back</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                                        <div>
                                            <div class="text-muted small">Reference</div>
                                            <div class="fw-semibold">{{ $requestRecord->request_reference ?? 'Draft' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Status</div>
                                            <div class="fw-semibold text-uppercase">{{ str_replace('_', ' ', $requestRecord->request_status) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Current Step</div>
                                            <div class="fw-semibold">{{ $requestRecord->current_step }}/6</div>
                                        </div>
                                    </div>

                                    <h5 class="mb-2">Business Information</h5>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6"><strong>Business Name:</strong> {{ $requestRecord->company_name }}</div>
                                        <div class="col-md-6"><strong>GST:</strong> {{ $requestRecord->gst_number }}</div>
                                        <div class="col-md-6"><strong>PAN:</strong> {{ $requestRecord->pan_number ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Business Type:</strong> {{ $requestRecord->business_type ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Constitution:</strong> {{ $requestRecord->constitution_type ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Years in Business:</strong> {{ $requestRecord->years_in_business ?? '--' }}</div>
                                        <div class="col-12"><strong>Address:</strong> {{ $requestRecord->company_address ?? '--' }}</div>
                                        <div class="col-md-4"><strong>City:</strong> {{ $requestRecord->city ?? '--' }}</div>
                                        <div class="col-md-4"><strong>State:</strong> {{ $requestRecord->state ?? '--' }}</div>
                                        <div class="col-md-4"><strong>Country:</strong> {{ $requestRecord->country ?? '--' }}</div>
                                        <div class="col-md-4"><strong>Pincode:</strong> {{ $requestRecord->pincode ?? '--' }}</div>
                                    </div>

                                    <h5 class="mb-2">Contact Person</h5>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6"><strong>Contact Person:</strong> {{ $requestRecord->contact_person ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Designation:</strong> {{ $requestRecord->designation ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Mobile:</strong> {{ $requestRecord->mobile ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Alternate Mobile:</strong> {{ $requestRecord->alternate_mobile ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Email:</strong> {{ $requestRecord->email ?? '--' }}</div>
                                    </div>

                                    <h5 class="mb-2">Bank & Product</h5>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6"><strong>Account Holder:</strong> {{ $requestRecord->account_holder_name ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Bank:</strong> {{ $requestRecord->bank_name ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Account Number:</strong> {{ $requestRecord->account_number ?? '--' }}</div>
                                        <div class="col-md-6"><strong>IFSC:</strong> {{ $requestRecord->ifsc_code ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Account Type:</strong> {{ $requestRecord->account_type ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Branch:</strong> {{ $requestRecord->branch ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Category:</strong> {{ $requestRecord->category ?? '--' }}</div>
                                        <div class="col-md-6"><strong>Turnover:</strong> {{ $requestRecord->turnover ?? '--' }}</div>
                                        <div class="col-12"><strong>Products:</strong> {{ $requestRecord->products ?? '--' }}</div>
                                    </div>

                                    <h5 class="mb-2">Documents</h5>
                                    <div class="row g-2">
                                        <div class="col-md-4"><a href="{{ $requestRecord->gst_certificate_path ? asset('storage/'.$requestRecord->gst_certificate_path) : '#' }}" target="_blank">GST Certificate</a></div>
                                        <div class="col-md-4"><a href="{{ $requestRecord->pan_document_path ? asset('storage/'.$requestRecord->pan_document_path) : '#' }}" target="_blank">PAN Document</a></div>
                                        <div class="col-md-4"><a href="{{ $requestRecord->registration_certificate_path ? asset('storage/'.$requestRecord->registration_certificate_path) : '#' }}" target="_blank">Registration Certificate</a></div>
                                        <div class="col-md-4"><a href="{{ $requestRecord->address_proof_path ? asset('storage/'.$requestRecord->address_proof_path) : '#' }}" target="_blank">Address Proof</a></div>
                                        <div class="col-md-4"><a href="{{ $requestRecord->cancelled_cheque_path ? asset('storage/'.$requestRecord->cancelled_cheque_path) : '#' }}" target="_blank">Cancelled Cheque</a></div>
                                        <div class="col-md-4"><a href="{{ $requestRecord->other_documents_path ? asset('storage/'.$requestRecord->other_documents_path) : '#' }}" target="_blank">Other Documents</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <h5 class="mb-3">Review Action</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Review Note / Rejection Reason</label>
                                        <textarea class="form-control" rows="5" wire:model.live="reviewNote" placeholder="Add note for approve or reject."></textarea>
                                        @error('reviewNote') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" wire:click="approve" @disabled($requestRecord->request_status === 'approved')>Approve & Create Seller</button>
                                        <button type="button" class="btn btn-danger" wire:click="reject">Reject Request</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <h5 class="mb-3">Timeline</h5>
                                    <div class="timeline-list">
                                        @forelse(($requestRecord->timeline ?? []) as $event)
                                            <div class="border-start ps-3 mb-3 position-relative">
                                                <div class="fw-semibold">{{ $event['label'] ?? $event['event'] }}</div>
                                                <div class="small text-muted">{{ $event['at'] ?? '' }}</div>
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
</div>
