<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-4 card-title"><h4>Product Requests</h4></div>
                        <div class="col-md-8 text-end">
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <input type="text" class="form-control form-control-sm" style="max-width:260px" placeholder="Search product / seller" wire:model.live="search">
                                <select class="form-select form-select-sm" style="max-width:200px" wire:model.live="status">
                                    <option value="pending_review">Pending Review</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
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
                                            <div class="fw-bold">{{ $item->name ?? '--' }}</div>
                                            <div class="small text-muted">{{ $item->hsn_code ? 'HSN: ' . $item->hsn_code : '' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $item->getUser?->name ?? '--' }}</div>
                                            <div class="small text-muted">{{ $item->getUser?->phone ?? '--' }}</div>
                                        </td>
                                        <td>{{ $item->getCategory?->name ?? '--' }}</td>
                                        <td><span class="badge bg-{{ $item->request_status === 'approved' ? 'success' : ($item->request_status === 'rejected' ? 'danger' : 'warning') }}">{{ strtoupper(str_replace('_', ' ', $item->request_status)) }}</span></td>
                                        <td>{{ dateFormat($item->updated_at) }}</td>
                                        <td>
                                            <a href="{{ route('admin.seller-product-request.show', $item->id) }}" class="btn btn-danger btn-sm" wire:navigate>Review</a>
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
