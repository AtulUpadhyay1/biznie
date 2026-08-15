<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>All Sellers</h4>
                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <label class="bz-filter-label" for="seller_search">Search sellers</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="seller_search" class="form-control"
                                    placeholder="Search here..." wire:model.live="search">
                            </div>
                        </div>
                        {{-- <div class="input-group flatpickr" id="dashboardDate">
                            <span class="input-group-text input-group-addon bg-transparent border-danger"
                                data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                            <input type="text" class="form-control bg-transparent border-danger"
                                placeholder="Select date" data-input>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm">
                            <i class="bi bi-cloud-download"></i>
                            Download Report
                        </button> --}}
                        <a href="{{ route('admin.seller.create') }}" class="btn btn-danger btn-sm"
                            wire:navigate>
                            <i class="bi bi-plus-lg"></i>
                            Add Seller
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <ul class="list-group list-group-horizontal filter-list-group float-end">
                        <li class="list-group-item border-0">
                            <div class="custom-search-bar">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Type</option>
                                <option>Dairy</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>City</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>State</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>Pincode</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>Status</option>
                            </select>
                        </li>
                    </ul> --}}
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th style="width: 25%">Seller Details</th>
                                    <th style="width: 30%;">Business Details</th>
                                    <th>Activity Details</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data )
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>User Name</dt>
                                                    <dd>{{ $data->name }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Phone</dt>
                                                    <dd>{{ $data->phone }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Email</dt>
                                                    <dd>{{ $data->email }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Priority</dt>
                                                    <dd>{{ $data->getUserDetail->priority ?? 0 }}⭐</dd>
                                                </div>
                                            </dl>
                                            {{-- <b>User Name:</b>
                                            <span>{{ $data->name }}</span>
                                            <br>
                                            <i class="bi bi-telephone"></i><span
                                                class="ms-2">{{ $data->phone }}</span>
                                            <br>
                                            <i class="bi bi-envelope-at"></i><span
                                                class="ms-2">{{ $data->email }}</span>
                                            <br>
                                            {{ $data->getUserDetail->priority ?? 0 }}⭐ --}}
                                        </td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span class="text-danger text-uppercase"> {{ $data->getBusiness->name }}
                                            </span>
                                            @if ($data->getSellerKycDetail)
                                                @if ($data->getSellerKycDetail->status == 'uploaded' || $data->getSellerKycDetail->status == 'pending')
                                                    <i class="bi bi-stopwatch-fill text-warning"></i>
                                                @elseif ($data->getSellerKycDetail->status == 'approved')
                                                    <i class="bi bi-patch-check-fill text-success"></i>
                                                @elseif ($data->getSellerKycDetail->status == 'rejected')
                                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                                @endif
                                            @endif
                                            <br>
                                            {{-- <b>Business Category:</b>
                                            <span class="text-success">Textile</span>
                                            <br> --}}
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Cash Balance"><i class="bi bi-cash-stack"></i> : <b>
                                                    <i class="bi bi-currency-rupee"></i>
                                                    {{ formatIndianNumber($data->cash_balance) }}</b>
                                            </span>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Credit Balance"><i class="bi bi-credit-card-2-front"></i> : <b>
                                                    <i class="bi bi-currency-rupee"></i>
                                                    {{ formatIndianNumber($data->credit_balance) }}</b>
                                            </span>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Product Enquiry"><i class="bi bi-cart"></i> :<b>
                                                    {{ $data->getSellerProductEnquiries->count() }}</b></span>
                                            <span class="pe-2" data-bs-toggle="tooltip" title="Total Orders"><i
                                                    class="bi bi-cart-check"></i>: <b>
                                                    {{ $data->getSellerOrders->count() }} </b></span>
                                            <br>
                                            <hr />
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Company Name</dt>
                                                    <dd>{{ $data->getUserDetail ? $data->getUserDetail->company_name : '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>GSTIN</dt>
                                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->gst_number : '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>PAN</dt>
                                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->identity_number : '--' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>City</dt>
                                                    <dd>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->city : '--' }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Registration On</dt>
                                                    <dd>{{ dateFormat($data->getBusiness->created_at) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Updation On</dt>
                                                    <dd>{{ dateFormat($data->getBusiness->updated_at) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Last Active</dt>
                                                    <dd>{{ lastActive($data->id) }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td>
                                            {{ $data->getSellerKycDetail ? $data->getSellerKycDetail->address : '' }}
                                        </td>
                                        <td class="text-center">
                                            <a type="button" class="bz-row-action" id="actionBtn{{ $data->id }}"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="actionBtn{{ $data->id }}">
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.seller-kyc-detail', $data->id) }}"
                                                    wire:navigate><i
                                                        class="bi bi-eye me-2"></i><span>View</span></a>
                                                {{-- <a href="{{route('admin.edit-seller')}}" wire:navigate
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-pencil-square me-2"></i><span>Edit</span></a> --}}
                                                <a href="{{ route('admin.tag-priority', $data->id) }}" wire:navigate
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-bookmarks me-2"></i><span>Tag &
                                                        Priority</span></a>
                                                {{-- <a href="javascript:;"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-trash me-2"></i><span>Delete</span></a> --}}
                                                <a href="{{ route('admin.seller-product.index', $data->id) }}"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-box me-2"></i><span>Product
                                                        Section</span></a>
                                                <a href="{{ route('admin.seller-product-request.create', $data->id) }}"
                                                    wire:navigate
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-clipboard-plus me-2"></i><span>Product
                                                        Request</span></a>
                                            </div>
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
