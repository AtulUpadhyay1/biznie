<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-4 card-title"><h4>Seller Requests</h4></div>
                        <div class="col-md-8 text-end">
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <input type="text" class="form-control form-control-sm" style="max-width:260px" placeholder="Search request" wire:model.live="search">
                                <select class="form-select form-select-sm" style="max-width:200px" wire:model.live="status">
                                    <option value="pending_review">Pending Review</option>
                                    <option value="draft">Draft</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="approved">Approved</option>
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
                                    <th>Contact</th>
                                    <th>Business</th>
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
                                            <div class="fw-bold">{{ $item->contact_person ?? '--' }}</div>
                                            <div class="small text-muted">{{ $item->mobile ?? '--' }}</div>
                                            <div class="small text-muted">{{ $item->email ?? '--' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $item->company_name ?? '--' }}</div>
                                            <div class="small text-muted">{{ $item->business_type ?? '--' }}</div>
                                        </td>
                                        <td><span class="badge bg-{{ $item->request_status === 'approved' ? 'success' : ($item->request_status === 'rejected' ? 'danger' : 'warning') }}">{{ strtoupper(str_replace('_', ' ', $item->request_status)) }}</span></td>
                                        <td>{{ dateFormat($item->updated_at) }}</td>
                                        <td>
                                            <a href="{{ route('admin.seller-request.show', $item->id) }}" class="btn btn-danger btn-sm" wire:navigate>Review</a>
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
