<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @php
        $status = strtolower((string) ($requestRecord->request_status ?? 'draft'));
        $statusBadgeClass = match ($status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending_review' => 'warning',
            default => 'secondary',
        };
        $images = is_array($requestRecord->images ?? null) ? $requestRecord->images : [];
        $physical = is_array($requestRecord->physical_specification ?? null) ? $requestRecord->physical_specification : [];
        $chemical = is_array($requestRecord->chemical_specification ?? null) ? $requestRecord->chemical_specification : [];
        $variants = is_array($requestRecord->variation ?? null) ? $requestRecord->variation : [];
        $packagingTypes = is_array($requestRecord->packaging_type ?? null) ? $requestRecord->packaging_type : [];
        $packagingPrices = is_array($requestRecord->packaging_type_price ?? null) ? $requestRecord->packaging_type_price : [];
        $chargeNames = is_array($requestRecord->charge_name ?? null) ? $requestRecord->charge_name : [];
        $chargePrices = is_array($requestRecord->charge_price ?? null) ? $requestRecord->charge_price : [];
        $quality = is_array($requestRecord->quality ?? null) ? implode(', ', $requestRecord->quality) : ($requestRecord->quality ?? '--');
    @endphp

    <style>
        .sr-gradient-card { border: 0; border-radius: 16px; background: linear-gradient(135deg, #f8fff8 0%, #f2f8ff 100%); box-shadow: 0 8px 24px rgba(20, 33, 61, 0.08); }
        .sr-soft-card { border: 1px solid #e8edf3; border-radius: 14px; box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04); }
        .sr-section-title { font-size: 1rem; font-weight: 700; color: #15223b; margin-bottom: 14px; }
        .sr-kv { border: 1px solid #eef1f6; border-radius: 10px; padding: 10px 12px; height: 100%; background: #fff; }
        .sr-kv-label { font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .02em; margin-bottom: 3px; }
        .sr-kv-value { font-size: .92rem; color: #0f172a; font-weight: 600; line-height: 1.35; word-break: break-word; }
        .sr-thumb { width: 84px; height: 84px; object-fit: cover; border-radius: 10px; border: 1px solid #e8edf3; }
        .sr-timeline-item { position: relative; padding-left: 22px; }
        .sr-timeline-item::before { content: ''; position: absolute; left: 0; top: 6px; width: 10px; height: 10px; border-radius: 50%; background: #198754; }
        .sr-timeline-item::after { content: ''; position: absolute; left: 4px; top: 20px; width: 2px; height: calc(100% - 12px); background: #dbe3ee; }
        .sr-timeline-item:last-child::after { display: none; }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card sr-gradient-card mb-3">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Product Request Review</h3>
                            <div class="text-muted">Review the submitted product and approve or reject it.</div>
                        </div>
                        <a href="{{ route('admin.seller-product-request.index') }}" class="btn btn-outline-danger btn-sm" wire:navigate>
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Reference</div><div class="sr-kv-value">{{ $requestRecord->request_reference ?? 'Draft' }}</div></div></div>
                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Status</div><div class="sr-kv-value"><span class="badge text-bg-{{ $statusBadgeClass }} text-uppercase px-3 py-2">{{ str_replace('_', ' ', $requestRecord->request_status ?? 'draft') }}</span></div></div></div>
                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Seller</div><div class="sr-kv-value">{{ $requestRecord->getUser?->name ?? '--' }}</div></div></div>
                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Submitted</div><div class="sr-kv-value">{{ $requestRecord->submitted_at ? dateFormat($requestRecord->submitted_at) : '--' }}</div></div></div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fa fa-check-circle me-1"></i> {{ session('success') }}</div>
                    @endif

                    <div class="row g-3">
                        <div class="col-xl-8">
                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Basic Information</h5>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Product Name</div><div class="sr-kv-value">{{ $requestRecord->name ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Product Type</div><div class="sr-kv-value">{{ ucwords(str_replace('_', ' ', $requestRecord->product_type ?? '--')) }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">HSN Code</div><div class="sr-kv-value">{{ $requestRecord->hsn_code ?? '--' }}</div></div></div>
                                        <div class="col-md-6"><div class="sr-kv"><div class="sr-kv-label">Category</div><div class="sr-kv-value">{{ $requestRecord->getCategory?->name ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Sub Category</div><div class="sr-kv-value">{{ $requestRecord->getSubCategory?->name ?? '--' }}</div></div></div>
                                        <div class="col-md-3"><div class="sr-kv"><div class="sr-kv-label">Tax Rate (%)</div><div class="sr-kv-value">{{ $requestRecord->tax_rate ?? '--' }}</div></div></div>
                                        <div class="col-12"><div class="sr-kv"><div class="sr-kv-label">Short Description</div><div class="sr-kv-value">{{ $requestRecord->short_description ?? '--' }}</div></div></div>
                                        <div class="col-12"><div class="sr-kv"><div class="sr-kv-label">Detailed Description</div><div class="sr-kv-value">{{ $requestRecord->description ?? '--' }}</div></div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Media</h5>
                                    @if(count($images))
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($images as $imgId)
                                                @php $url = imageUrl($imgId); @endphp
                                                @if($url)<a href="{{ $url }}" target="_blank"><img src="{{ $url }}" class="sr-thumb" alt="product"></a>@endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted">No images uploaded.</div>
                                    @endif
                                    @if($requestRecord->video_url)
                                        <div class="mt-2"><a href="{{ $requestRecord->video_url }}" target="_blank">{{ $requestRecord->video_url }}</a></div>
                                    @endif
                                </div>
                            </div>

                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Quality, Brand & Packaging</h5>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Quality</div><div class="sr-kv-value">{{ $quality ?: '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Quality Charge (₹)</div><div class="sr-kv-value">{{ $requestRecord->quality_charge ?? '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Brand / Make</div><div class="sr-kv-value">{{ $requestRecord->brand_name ?? '--' }} / {{ $requestRecord->make ?? '--' }}</div></div></div>
                                        <div class="col-12"><div class="sr-kv"><div class="sr-kv-label">Quality Description</div><div class="sr-kv-value">{{ $requestRecord->quality_description ?? '--' }}</div></div></div>
                                    </div>
                                    @if(count($packagingTypes))
                                        <div class="table-responsive mt-2">
                                            <table class="custom-table"><thead><tr><th>Packaging Type</th><th>Charge (₹)</th></tr></thead><tbody>
                                                @foreach($packagingTypes as $i => $pt)
                                                    <tr><td>{{ $pt ?? '--' }}</td><td>{{ $packagingPrices[$i] ?? '--' }}</td></tr>
                                                @endforeach
                                            </tbody></table>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Specifications & Weight</h5>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Unit</div><div class="sr-kv-value">{{ $requestRecord->weight_unit ?? '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Net Weight / Qty</div><div class="sr-kv-value">{{ $requestRecord->net_weight ?? '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Tolerance</div><div class="sr-kv-value">{{ $requestRecord->tolerance ?? '--' }}</div></div></div>
                                    </div>
                                    @foreach(['Physical' => $physical, 'Chemical' => $chemical] as $label => $specs)
                                        @if(count($specs))
                                            <div class="fw-semibold mt-2 mb-1">{{ $label }} Specifications</div>
                                            <div class="table-responsive">
                                                <table class="custom-table"><thead><tr><th>Parameter</th><th>Value</th><th>Image</th></tr></thead><tbody>
                                                    @foreach($specs as $spec)
                                                        <tr>
                                                            <td>{{ $spec['parameter'] ?? '--' }}</td>
                                                            <td>{{ $spec['value'] ?? '--' }}</td>
                                                            <td>@php $su = !empty($spec['image']) ? imageUrl($spec['image']) : null; @endphp @if($su)<a href="{{ $su }}" target="_blank">View</a>@else -- @endif</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody></table>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="card sr-soft-card mb-3">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Size Variants & Pricing</h5>
                                    @if(count($variants))
                                        <div class="table-responsive">
                                            <table class="custom-table"><thead><tr><th>Size / Variant</th><th>Unit</th><th>Charge (₹)</th><th>Stock</th></tr></thead><tbody>
                                                @foreach($variants as $v)
                                                    <tr><td>{{ $v['size'] ?? '--' }}</td><td>{{ $v['unit'] ?? '--' }}</td><td>{{ $v['charge'] ?? '--' }}</td><td>{{ $v['stock'] ?? '--' }}</td></tr>
                                                @endforeach
                                            </tbody></table>
                                        </div>
                                    @else
                                        <div class="text-muted">No variants added.</div>
                                    @endif
                                </div>
                            </div>

                            <div class="card sr-soft-card">
                                <div class="card-body">
                                    <h5 class="sr-section-title">Loading, MOQ & Charges</h5>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Loading City</div><div class="sr-kv-value">{{ $requestRecord->city ?? '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Loading State</div><div class="sr-kv-value">{{ $requestRecord->state ?? '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Country</div><div class="sr-kv-value">{{ $requestRecord->country ?? '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">MOQ</div><div class="sr-kv-value">{{ $requestRecord->moq ?? '--' }} {{ $requestRecord->moq_unit ?? '' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Publish On</div><div class="sr-kv-value">{{ $requestRecord->publish_on ? dateFormat($requestRecord->publish_on) : '--' }}</div></div></div>
                                        <div class="col-md-4"><div class="sr-kv"><div class="sr-kv-label">Desired Status</div><div class="sr-kv-value">{{ ucfirst($requestRecord->product_status ?? '--') }}</div></div></div>
                                    </div>
                                    @if(count($chargeNames))
                                        <div class="table-responsive mt-2">
                                            <table class="custom-table"><thead><tr><th>Charge</th><th>Amount</th></tr></thead><tbody>
                                                @foreach($chargeNames as $i => $cn)
                                                    <tr><td>{{ $cn ?? '--' }}</td><td>{{ $chargePrices[$i] ?? '--' }}</td></tr>
                                                @endforeach
                                            </tbody></table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card sr-soft-card mb-3 sticky-top" style="top: 90px;">
                                <div class="card-body">
                                    <h5 class="sr-section-title mb-2">Review Action</h5>
                                    <p class="text-muted small mb-3">Add a clear reason for approval or rejection for better audit history.</p>
                                    <div class="mb-3">
                                        <label class="form-label">Review Note / Rejection Reason</label>
                                        <textarea class="form-control" rows="5" wire:model.live="reviewNote" placeholder="Write your note here..."></textarea>
                                        @error('reviewNote') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" wire:click="approve" @disabled(($requestRecord->request_status ?? '') === 'approved')>
                                            <i class="fa fa-check me-1"></i> Approve & Publish
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" wire:click="reject">
                                            <i class="fa fa-times me-1"></i> Reject Product
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
                                            <div class="small text-muted">{{ !empty($event['at']) ? dateTimeFormat($event['at']) : '--' }}</div>
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
