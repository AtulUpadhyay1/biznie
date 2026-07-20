<div>
    <style>
        .table-sm>:not(caption)>*>* {
            padding: 0.25rem .55rem;
        }

        .border-red {
            border: 2px solid #fd7070;
        }
    </style>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <li class="list-group-item border-0 me-2">
                                    <div class="custom-search-bar">
                                        <div class="input-group">
                                            <span class="input-group-text"> <i data-feather="search"></i></span>
                                            <input type="text" class="form-control" placeholder="Search here..."
                                                wire:model.live="search">
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item border-0 me-2">
                                    <select class="form-select" wire:model.live="status">
                                        <option value="">Status</option>
                                        <option value="active">Active</option>
                                        <option value="in_active">In Active</option>
                                    </select>
                                </li>
                                {{-- <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                                    <span class="input-group-text input-group-addon bg-transparent border-danger"
                                        data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                                    <input type="text" class="form-control bg-transparent border-danger"
                                        placeholder="Select date" data-input>
                                </div>
                                <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                    <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                                    Download Report
                                </button> --}}
                                <a href="{{ route('admin.customer.create') }}"
                                    class="btn btn-danger btn-sm btn-icon-text mb-2 mb-md-0" wire:navigate>
                                    <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                    Add
                                </a>
                            </div>
                        </div>
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
                                    <tr class="border-red">
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Name:</b></td>
                                                        <td>{{ $data->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Company Name:</b></td>
                                                        <td><span
                                                                class="text-uppercase">{{ $data->getUserDetail ? $data->getUserDetail->company_name : '--' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>GSTIN:</b></td>
                                                        <td>{{ $data->getUserDetail ? $data->getUserDetail->gst_number : '--' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>PAN:</b></td>
                                                        <td>{{ $data->getUserDetail ? $data->getUserDetail->pan_number : '--' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>City:</b></td>
                                                        <td>{{ $data->getUserDetail ? $data->getUserDetail->city : '--' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td><i class="bi bi-telephone"></i></td>
                                                        <td>{{ $data->phone }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><i class="bi bi-envelope-at"></i></td>
                                                        <td>{{ $data->email }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Cash Wallet:</b></td>
                                                        <td>₹ {{ $data->cash_balance }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Credit Wallet:</b></td>
                                                        <td>₹ {{ formatIndianNumber($data->credit_balance) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Credit Limit:</b></td>
                                                        <td>₹ {{ formatIndianNumber($data->assign_credit_balance) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Registration Date:</b></td>
                                                        <td>{{ dateFormat($data->created_at) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Last Active:</b></td>
                                                        <td>{{ lastActive($data->id) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Status:</b></td>
                                                        <td>{!! $data->status == 'active'
                                                            ? '<span class="text-success fw-bolder">Active</span>'
                                                            : '<span class="text-danger fw-bolder">Inactive</span>' !!}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn">
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.customer-profile', $data->id) }}"
                                                    wire:navigate><i
                                                        class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.seller-request.create', $data->id) }}"
                                                    wire:navigate><i
                                                        class="bi bi-person-plus icon-sm me-2"></i><span>Seller
                                                        Request</span></a>
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="#"><i
                                                    class="bi bi-person-slash icon-sm me-2"></i><span>Block</span></a>
                                                <a href="javascript:;"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a> --}}
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
