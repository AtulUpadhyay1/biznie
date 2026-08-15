<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Product Requests</h4>
                    <div class="bz-toolbar">
                        <label class="bz-filter-label" for="request_search">Search</label>
                        <input type="text" class="form-control" id="request_search" placeholder="Search product / seller" wire:model.live="search">
                        <label class="bz-filter-label" for="request_status">Status</label>
                        <select class="form-select" id="request_status" wire:model.live="status">
                            <option value="pending_review">Pending Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $item)
                                    <tr>
                                        <td>{{ $item->request_reference ?? 'Draft' }}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $item->name ?? '--' }}</div>
                                            <div class="bz-cell-sub">{{ $item->hsn_code ? 'HSN: ' . $item->hsn_code : '' }}</div>
                                        </td>
                                        <td>
                                            <div class="bz-cell-title">{{ $item->getUser?->name ?? '--' }}</div>
                                            <div class="bz-cell-sub">{{ $item->getUser?->phone ?? '--' }}</div>
                                        </td>
                                        <td>{{ $item->getCategory?->name ?? '--' }}</td>
                                        <td><span class="bz-status bz-status--{{ $item->request_status === 'approved' ? 'success' : ($item->request_status === 'rejected' ? 'danger' : 'warning') }}">{{ strtoupper(str_replace('_', ' ', $item->request_status)) }}</span></td>
                                        <td>{{ dateFormat($item->updated_at) }}</td>
                                        <td>
                                            <a href="{{ route('admin.seller-product-request.show', $item->id) }}" class="btn btn-danger btn-sm" wire:navigate><i class="bi bi-eye"></i>Review</a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $list->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
