<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <label class="bz-filter-label" for="search">Search</label>
                                <input id="search" type="text" class="form-control" placeholder="Search here..."
                                    wire:model.live="search">
                            </div>
                        </div>
                        <label class="bz-filter-label" for="status">Status</label>
                        <select id="status" class="form-select" wire:model.live="status">
                            <option value="">Status</option>
                            <option value="active">Active</option>
                            <option value="in_active">In Active</option>
                        </select>
                        <a href="{{ route('admin.customer.create') }}" class="btn btn-danger btn-sm" wire:navigate>
                            <i class="bi bi-plus-lg"></i>
                            Add
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Detail</th>
                                    <th>Contact Info</th>
                                    <th>Balance Info</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Name</dt>
                                                    <dd>{{ $data->name }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Company Name</dt>
                                                    <dd class="text-uppercase">{{ $data->getUserDetail->company_name ?? '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>GSTIN</dt>
                                                    <dd>{{ $data->getUserDetail->gst_number ?? '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>PAN</dt>
                                                    <dd>{{ $data->getUserDetail->pan_number ?? '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>City</dt>
                                                    <dd>{{ $data->getUserDetail->city ?? '--' }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt><i class="bi bi-telephone"></i> Phone</dt>
                                                    <dd>{{ $data->phone ?: '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt><i class="bi bi-envelope-at"></i> Email</dt>
                                                    <dd>{{ $data->email ?: '--' }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Cash Wallet</dt>
                                                    <dd class="bz-num">₹ {{ $data->cash_balance }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Credit Wallet</dt>
                                                    <dd class="bz-num">₹ {{ formatIndianNumber($data->credit_balance) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Credit Limit</dt>
                                                    <dd class="bz-num">₹ {{ formatIndianNumber($data->assign_credit_balance) }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Registration Date</dt>
                                                    <dd>{{ dateFormat($data->created_at) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Last Active</dt>
                                                    <dd>{{ lastActive($data->id) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Status</dt>
                                                    <dd>
                                                        @if ($data->status == 'active')
                                                            <span class="bz-status bz-status--success">Active</span>
                                                        @else
                                                            <span class="bz-status bz-status--muted">Inactive</span>
                                                        @endif
                                                    </dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" class="bz-row-action" id="actionBtn{{ $data->id }}" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="actionBtn{{ $data->id }}">
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.customer-profile', $data->id) }}"
                                                    wire:navigate><i
                                                        class="bi bi-eye me-2"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.seller-request.create', $data->id) }}"
                                                    wire:navigate><i
                                                        class="bi bi-person-plus me-2"></i><span>Seller
                                                        Request</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
