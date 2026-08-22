@php
    $isLive     = $bidding->isLive($rfq);
    $isOrdered  = strtolower((string) $rfq->status) === 'ordered';
    $unit       = $rfq->unit_label ?: 'Unit';
@endphp

<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="bz-page-head">
        <div>
            <a href="{{ route('admin.commodity-product-enquiry.liveBidding') }}" class="bz-cell-sub d-inline-flex align-items-center gap-1" wire:navigate>
                <i class="bi bi-arrow-left"></i> Back to Live RFQs
            </a>
            <h1 class="bz-page-head__title">
                {{ $page_title }}
                @if ($isOrdered)
                    <span class="bz-status bz-status--success">Ordered</span>
                @elseif ($isLive)
                    <span class="bz-status bz-status--danger">Live</span>
                @else
                    <span class="bz-status bz-status--muted">Closed</span>
                @endif
            </h1>
            <p class="bz-page-head__sub">Prices shown are F.O.R (doorstep) price per {{ $unit }}.</p>
        </div>
        <div class="bz-page-head__actions">
            <a href="{{ route('admin.commodity-product-enquiry.show', $rfq->id) }}" class="btn btn-secondary btn-sm" wire:navigate>
                <i class="bi bi-eye"></i>Enquiry
            </a>
            <a href="{{ route('admin.commodity-product-enquiry.history', $rfq->id) }}" class="btn btn-secondary btn-sm" wire:navigate>
                <i class="bi bi-clock-history"></i>History
            </a>
            @if ($rfq->status === 'Seller Marked')
                <a href="{{ route('admin.commodity-product-enquiry.convertToOrder', $rfq->id) }}" class="btn btn-danger btn-sm" wire:navigate>
                    <i class="bi bi-cart-check"></i>Convert To Order
                </a>
            @endif
        </div>
    </div>

    {{-- RFQ meta strip --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="bz-panel">
                <dl class="bz-kv-list">
                    <div><dt>RFQ ID</dt><dd>{{ $rfq->unique_id }}</dd></div>
                    <div><dt>Product</dt><dd>{{ $rfq->getCommodityProduct?->name ?? '--' }}{{ $rfq->size_label ? ' ' . $rfq->size_label : '' }}</dd></div>
                    <div><dt>Quantity</dt><dd>{{ $rfq->quantity ? formatIndianNumber($rfq->quantity) . ' ' . $unit : '--' }}</dd></div>
                    <div><dt>Delivery City</dt><dd>{{ $rfq->delivery_city ?? '--' }}</dd></div>
                    <div><dt>Buyer</dt><dd>{{ $rfq->getUser?->name ?? '--' }}</dd></div>
                    <div><dt>Created On</dt><dd>{{ dateTimeFormat($rfq->created_at) }}</dd></div>
                    <div>
                        <dt>Time Left</dt>
                        <dd>
                            @if ($isLive)
                                <span class="bz-num" data-bz-countdown="{{ $rfq->bidding_ends_at->timestamp }}">--</span>
                            @else
                                <span class="text-muted">Bidding closed</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Sellers on this RFQ --------------------------------------------- --}}
        <div class="col-lg-4 grid-margin">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Sellers in this RFQ <span class="badge bg-secondary rounded-pill">{{ $sellers->count() }}</span></h4>
                </div>
                <div class="card-body">
                    <div class="bz-toolbar-field bz-toolbar-field--lg mb-3">
                        <label class="bz-filter-label" for="seller_search">Search sellers</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="seller_search" class="form-control" placeholder="Search seller by name or city"
                                   wire:model.live.debounce.300ms="seller_search">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @forelse ($sellers as $seller)
                                    <tr wire:key="seller-{{ $seller->id }}"
                                        class="{{ (int) $this->selected_bid_id === (int) $seller->id ? 'table-active' : '' }}">
                                        <td>
                                            <button type="button" class="btn btn-sm btn-inverse-primary w-100 text-start"
                                                    wire:click="loadSeller({{ $seller->id }})">
                                                <span class="d-block">
                                                    <span class="bz-cell-title">
                                                        {{ $seller->getUser?->getUserDetail?->company_name ?: ($seller->getUser?->name ?? 'Seller') }}
                                                    </span>
                                                    <span class="bz-cell-sub">
                                                        {{ $seller->ex_works_city ?: 'City not set' }}{{ $seller->ex_works_state ? ', ' . $seller->ex_works_state : '' }}
                                                    </span>
                                                </span>
                                                <span class="d-block mt-1">
                                                    @if ($seller->for_price === null)
                                                        <span class="bz-status bz-status--warning">Awaiting price</span>
                                                    @elseif (($seller->price_source ?? 'app') === 'manual')
                                                        <span class="bz-status bz-status--warning">Manual Entry</span>
                                                    @else
                                                        <span class="bz-status bz-status--success">Via App</span>
                                                    @endif
                                                    @if ($seller->for_price !== null)
                                                        <span class="bz-num ms-1">₹{{ formatIndianNumber($seller->for_price) }}</span>
                                                    @endif
                                                </span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="1" title="No sellers"
                                        text="No seller matches this search." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-inverse-primary w-100" data-bs-toggle="modal" data-bs-target="#addSellerModal">
                        <i class="bi bi-plus-lg"></i>Add New Seller Price (Manual)
                    </button>
                </div>
            </div>
        </div>

        {{-- Manual price entry --------------------------------------------- --}}
        <div class="col-lg-8 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>
                        @if ($selected)
                            Manual Price Entry — {{ $selected->getUser?->getUserDetail?->company_name ?: ($selected->getUser?->name ?? 'Seller') }}
                        @else
                            Manual Price Entry
                        @endif
                    </h4>
                    @if ($selected)
                        <span class="bz-status {{ ($selected->price_source ?? 'app') === 'manual' ? 'bz-status--warning' : 'bz-status--success' }}">
                            {{ ($selected->price_source ?? 'app') === 'manual' ? 'Manual Entry' : 'Via App' }}
                        </span>
                    @endif
                </div>

                <div class="card-body">
                    @if (! $selected)
                        <div class="bz-empty">
                            <div class="bz-empty__icon"><i class="bi bi-people"></i></div>
                            <div class="bz-empty__title">No seller selected</div>
                            <p class="bz-empty__text">Pick a seller on the left, or add one, to key in a price.</p>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            You are entering a price on behalf of the seller. It counts towards bidding and the F.O.R ranking exactly like an app submission.
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="ex_works_price">Ex-Works Price (Per {{ $unit }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" id="ex_works_price" wire:model.live.debounce.400ms="ex_works_price"
                                       class="form-control @error('ex_works_price') is-invalid @enderror" placeholder="0.00">
                                @error('ex_works_price') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="freight_charges">Freight Charges (Per {{ $unit }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" id="freight_charges" wire:model.live.debounce.400ms="freight_charges"
                                       class="form-control @error('freight_charges') is-invalid @enderror" placeholder="0.00">
                                @error('freight_charges') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="other_charges">Other Charges (Per {{ $unit }})</label>
                                <input type="number" step="0.01" min="0" id="other_charges" wire:model.live.debounce.400ms="other_charges"
                                       class="form-control @error('other_charges') is-invalid @enderror" placeholder="0.00">
                                <small class="bz-cell-sub">e.g. loading, transit insurance.</small>
                                @error('other_charges') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="ex_works_city">Ex-Works City</label>
                                <input type="text" id="ex_works_city" wire:model="ex_works_city"
                                       class="form-control @error('ex_works_city') is-invalid @enderror" placeholder="Loading city">
                                @error('ex_works_city') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="ex_works_state">Ex-Works State</label>
                                <input type="text" id="ex_works_state" wire:model="ex_works_state" class="form-control" placeholder="Loading state">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="freight_type">Freight Type</label>
                                <select id="freight_type" class="form-select" wire:model="freight_type">
                                    <option value="doorstep">Doorstep (F.O.R)</option>
                                    <option value="ex_works">Ex-Works (buyer arranges)</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="bz-panel bz-panel--plain">
                                    <div class="bz-panel__title">F.O.R Price (Calculated)</div>
                                    <div class="bz-stat__value">₹{{ formatIndianNumber(number_format($this->forPrice, 2, '.', '')) }}</div>
                                    <div class="bz-cell-sub">
                                        per {{ $unit }} — ex-works + freight + other charges
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="remarks">Remarks (Optional)</label>
                                <textarea id="remarks" rows="2" wire:model="remarks"
                                          class="form-control @error('remarks') is-invalid @enderror" placeholder="Add remarks if any…"></textarea>
                                @error('remarks') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-danger btn-sm" wire:click="savePrice" wire:loading.attr="disabled" @disabled($isOrdered)>
                                <i class="bi bi-check2"></i>Save &amp; Update Price
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelEdit">Cancel</button>
                            <span wire:loading wire:target="savePrice" class="bz-spinner bz-spinner--sm"></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Live board ------------------------------------------------------- --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card" wire:poll.10s>
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Live Bidding — All Sellers <span class="bz-card-sub">F.O.R price per {{ $unit }}</span></h4>
                    <span class="bz-cell-sub"><i class="bi bi-arrow-repeat"></i> Auto refresh every 10s</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Seller (Hidden)</th>
                                    <th>Seller City</th>
                                    <th>Ex-Works (₹/{{ $unit }})</th>
                                    <th>Freight (₹/{{ $unit }})</th>
                                    <th>Other Charges (₹/{{ $unit }})</th>
                                    <th>F.O.R Price (₹/{{ $unit }})</th>
                                    <th>Source</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($board as $row)
                                    <tr wire:key="board-{{ $row['id'] }}"
                                        class="{{ (int) $this->selected_bid_id === (int) $row['id'] ? 'table-active' : '' }}">
                                        <td>{{ $row['rank'] }}</td>
                                        <td>
                                            {{ $row['label'] }}
                                            @if ($row['is_marked'])
                                                <span class="bz-status bz-status--success">Marked</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['seller_city'] ?: '--' }}</td>
                                        <td class="bz-num">₹{{ formatIndianNumber($row['ex_works_price']) }}</td>
                                        <td class="bz-num">₹{{ formatIndianNumber($row['freight_charges']) }}</td>
                                        <td class="bz-num">₹{{ formatIndianNumber($row['other_charges']) }}</td>
                                        <td class="bz-num">₹{{ formatIndianNumber($row['for_price']) }}</td>
                                        <td>
                                            <span class="bz-status {{ $row['source'] === 'manual' ? 'bz-status--warning' : 'bz-status--info' }}">
                                                {{ $row['source'] === 'manual' ? 'Manual Entry' : 'Via App' }}
                                            </span>
                                        </td>
                                        <td>{{ $row['updated_at'] ? dateTimeFormat($row['updated_at']) : '--' }}</td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="9" title="No prices yet"
                                        text="No seller has submitted a rate on this RFQ." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="bz-stat-grid bz-section-gap">
                        <div class="bz-stat bz-stat--green">
                            <div class="bz-stat__top">
                                <span class="bz-stat__label">Current Best F.O.R Price</span>
                                <span class="bz-stat__icon"><i class="bi bi-trophy"></i></span>
                            </div>
                            <div class="bz-stat__value">{{ $stats['best'] !== null ? '₹' . formatIndianNumber($stats['best']) : '--' }}</div>
                            <div class="bz-stat__foot">per {{ $unit }}</div>
                        </div>

                        <div class="bz-stat bz-stat--blue">
                            <div class="bz-stat__top">
                                <span class="bz-stat__label">Average F.O.R Price</span>
                                <span class="bz-stat__icon"><i class="bi bi-bar-chart"></i></span>
                            </div>
                            <div class="bz-stat__value">{{ $stats['average'] !== null ? '₹' . formatIndianNumber($stats['average']) : '--' }}</div>
                            <div class="bz-stat__foot">{{ $stats['responses'] }} of {{ $stats['invited'] }} sellers quoted</div>
                        </div>

                        <div class="bz-stat bz-stat--violet">
                            <div class="bz-stat__top">
                                <span class="bz-stat__label">Price Difference (Best vs Highest)</span>
                                <span class="bz-stat__icon"><i class="bi bi-arrows-expand-vertical"></i></span>
                            </div>
                            <div class="bz-stat__value">{{ $stats['spread'] !== null ? '₹' . formatIndianNumber($stats['spread']) : '--' }}</div>
                            <div class="bz-stat__foot">per {{ $unit }}</div>
                        </div>

                        <div class="bz-stat bz-stat--amber">
                            <div class="bz-stat__top">
                                <span class="bz-stat__label">Quick Actions</span>
                                <span class="bz-stat__icon"><i class="bi bi-lightning-charge"></i></span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-inverse-primary" wire:click="notifyAllSellers" wire:loading.attr="disabled">
                                    <i class="bi bi-bell"></i>Notify All Sellers
                                </button>
                                <button type="button" class="btn btn-sm btn-inverse-primary" wire:click="extendTimer" wire:loading.attr="disabled" @disabled($isOrdered)>
                                    <i class="bi bi-clock"></i>Extend Timer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add seller modal ------------------------------------------------- --}}
    <div class="modal fade" id="addSellerModal" tabindex="-1" aria-labelledby="addSellerModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSellerModalLabel">Add New Seller Price (Manual)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" for="new_seller_id">Seller <span class="text-danger">*</span></label>
                    <select id="new_seller_id" class="form-select @error('new_seller_id') is-invalid @enderror" wire:model="new_seller_id">
                        <option value="">Select a seller…</option>
                        @foreach ($candidates as $candidate)
                            <option value="{{ $candidate->id }}">
                                {{ $candidate->getUserDetail?->company_name ?: $candidate->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('new_seller_id') <small class="text-danger">{{ $message }}</small> @enderror
                    <p class="bz-cell-sub mt-2">
                        Lists sellers who stock this product but are not on the board yet.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="addSeller">
                        <i class="bi bi-plus-lg"></i>Add Seller
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
