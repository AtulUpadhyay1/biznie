<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>All Business Listings</h4>
                    <div class="bz-toolbar">
                        <label class="bz-filter-label" for="dashboardDateInput">Filter by date</label>
                        <div class="input-group flatpickr" id="dashboardDate">
                            <span class="input-group-text input-group-addon" data-toggle><i class="bi bi-calendar"></i></span>
                            <input type="text" id="dashboardDateInput" class="form-control" placeholder="Select date" data-input>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm">
                            <i class="bi bi-cloud-arrow-down"></i>Download Report
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-horizontal filter-list-group">
                        <li class="list-group-item border-0">
                            <form class="custom-search-bar">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search here...">
                                </div>
                            </form>
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th style="width: 25%;">Business Details</th>
                                    <th>Image</th>
                                    <th style="width:20%;">Contact info</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data )
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span> {{ $data->name }} </span>
                                            <i class="bi bi-patch-check-fill text-danger"></i>
                                            <br>
                                            <b>Business Category:</b>
                                            <span>Textile</span>
                                            <br>
                                            <b>User Name:</b>
                                            <span> {{ $data->getUser->name }} </span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Total orders"><i class="bi bi-bag-plus"></i> : <b>0</b></span>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Total users"><i class="bi bi-people"></i> : <b>273</b></span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Total earnings"><i class="bi bi-wallet2"></i> : <i
                                                    class="bi bi-currency-rupee"></i><b>5L</b></span>
                                            <span class="pe-2" data-bs-toggle="tooltip" title="Total messages"><i
                                                    class="bi bi-chat-square-dots"></i>: <b>0</b></span>
                                        </td>
                                        <td><img src="{{ asset('admin_css/assets/images/avatar.png') }}"
                                                class="custom-table-img" alt="user" data-bs-toggle="tooltip"
                                                title="Business Image"></td>
                                        <td>
                                            <i class="bi bi-telephone"></i><span class="ms-2">{{ $data->getUser->phone }}</span>
                                            <br>
                                            <i class="bi bi-envelope-at"></i><span class="ms-2">{{ $data->getUser->email }}</span>
                                        </td>
                                        <td>
                                            <b>Pincode:</b>
                                            <span>{{ $data->getSellerKycDetail->postal_code }}</span>
                                            <br>
                                            <b>Area:</b>
                                            <span>{{ $data->getSellerKycDetail->address }}</span>
                                            <br>
                                            <b>City:</b>
                                            <span>{{ $data->getSellerKycDetail->city }}</span>
                                            <br>
                                            <b>State:</b>
                                            <span>{{ $data->getSellerKycDetail->state }}</span>
                                            <br>
                                            <b>Country</b>
                                            <span>{{ $data->getSellerKycDetail->country }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="custom-dropdown">
                                                <a class="custom-status-btn rounded text-white" href="javascript:;"
                                                    role="button" id="dropdownMenuLink{{ $data->id }}" data-bs-toggle="dropdown"
                                                    title="Status" aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{ $data->id }}">
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox"
                                                                class="form-check-input status_update">
                                                        </div>
                                                        <span>Featured</span>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox"
                                                                class="form-check-input status_update">
                                                        </div>
                                                        <span>Verified</span>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox"
                                                                class="form-check-input status_update">
                                                        </div>
                                                        <span>Top Sellers</span>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox"
                                                                class="form-check-input status_update">
                                                        </div>
                                                        <span>Inactive</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="mt-3"><span class="bz-status bz-status--success">Active</span></div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('admin.edit-business')}}" class="custom-edit-btn rounded text-white"
                                                title="edit" wire:navigate>
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
