<div wire:poll.15s>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="bz-page-head">
        <div>
            <h1 class="bz-page-head__title">{{ $page_title }}</h1>
            <p class="bz-page-head__sub">Auctions in progress. Open one to key in a price on a seller's behalf.</p>
        </div>
        <div class="bz-page-head__actions">
            <a href="{{ route('admin.commodity-product-enquiry.index') }}" class="btn btn-secondary btn-sm" wire:navigate>
                <i class="bi bi-list-ul"></i>All Enquiries
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="bz-tabs">
                        <button type="button" class="bz-tab {{ $filter === 'live' ? 'is-active' : '' }}" wire:click="setFilter('live')">
                            Live <span class="badge bg-secondary rounded-pill">{{ $counts['live'] }}</span>
                        </button>
                        <button type="button" class="bz-tab {{ $filter === 'closed' ? 'is-active' : '' }}" wire:click="setFilter('closed')">
                            Completed <span class="badge bg-secondary rounded-pill">{{ $counts['closed'] }}</span>
                        </button>
                        <button type="button" class="bz-tab {{ $filter === 'awarded' ? 'is-active' : '' }}" wire:click="setFilter('awarded')">
                            Ordered <span class="badge bg-secondary rounded-pill">{{ $counts['awarded'] }}</span>
                        </button>
                        <button type="button" class="bz-tab {{ $filter === 'all' ? 'is-active' : '' }}" wire:click="setFilter('all')">
                            All <span class="badge bg-secondary rounded-pill">{{ $counts['all'] }}</span>
                        </button>
                    </div>

                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <label class="bz-filter-label" for="live_rfq_search">Search RFQs</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="live_rfq_search" class="form-control" placeholder="RFQ ID, product, buyer…"
                                       wire:model.live.debounce.400ms="search">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>RFQ</th>
                                    <th>Requirement</th>
                                    <th>Delivery</th>
                                    <th>Sellers</th>
                                    <th>Best F.O.R</th>
                                    <th>Time Left</th>
                                    <th style="width:70px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $data)
                                    @php
                                        $isLive = $bidding->isLive($data);
                                        $sellerCount = $invited[$data->id] ?? 0;
                                        $replyCount  = $responded[$data->id] ?? 0;
                                    @endphp
                                    <tr wire:key="live-rfq-{{ $data->id }}">
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->unique_id }}</div>
                                            <div class="bz-cell-sub">{{ $data->getUser?->name ?? '--' }} · {{ dateTimeFormat($data->created_at) }}</div>
                                        </td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->getCommodityProduct?->name ?? '--' }}{{ $data->size_label ? ' ' . $data->size_label : '' }}</div>
                                            <div class="bz-cell-sub">
                                                {{ $data->quantity ? formatIndianNumber($data->quantity) . ' ' . ($data->unit_label ?? '') : '--' }}
                                                @if ($data->getBrand) · {{ $data->getBrand?->name }} @endif
                                            </div>
                                        </td>
                                        <td>{{ $data->delivery_city ?? '--' }}</td>
                                        <td>
                                            <span class="bz-num">{{ $replyCount }}</span> / <span class="bz-num">{{ $sellerCount }}</span>
                                            <div class="bz-cell-sub">quoted</div>
                                        </td>
                                        <td>
                                            @if ($data->best_for_price !== null)
                                                <span class="bz-num">₹{{ formatIndianNumber($data->best_for_price) }}</span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($isLive)
                                                {{-- Epoch, not a rendered duration: the ticker owns the digits so
                                                     a row cached for 15s does not show a stale clock. --}}
                                                <span class="bz-num" data-bz-countdown="{{ $data->bidding_ends_at->timestamp }}">--</span>
                                                <div><span class="bz-status bz-status--danger">Live</span></div>
                                            @elseif (strtolower((string) $data->status) === 'ordered')
                                                <span class="bz-status bz-status--success">Ordered</span>
                                            @else
                                                <span class="bz-status bz-status--muted">Closed</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.commodity-product-enquiry.manualPriceEntry', $data->id) }}"
                                               class="btn btn-danger btn-sm" wire:navigate>
                                                <i class="bi bi-pencil-square"></i>Prices
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="8" title="No RFQs here"
                                        text="Nothing matches this filter yet." />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="bz-pagination">
                            {{ $list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
