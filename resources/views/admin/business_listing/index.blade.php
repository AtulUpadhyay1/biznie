<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @if ($formMode)
        @include('admin.business_listing.form')
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 card-title">
                                <h4>All Business Listings</h4>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                    <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                                        <span class="input-group-text input-group-addon bg-transparent border-danger"
                                            data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                                        <input type="text" class="form-control bg-transparent border-danger"
                                            placeholder="Select date" data-input>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                        <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                                        Download Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-horizontal filter-list-group">
                            <li class="list-group-item border-0">
                                <select class="form-select">
                                    <option>Textile</option>
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
                                    <tr>
                                        <td><b>1</b></td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span class="text-primary">Shri Sai Murti Dairy Form</span>
                                            <i class="bi bi-patch-check-fill text-danger"></i>
                                            <br>
                                            <b>Business Category:</b>
                                            <span class="text-success">Textile</span>
                                            <br>
                                            <b>User Name:</b>
                                            <span>Sudhanhsu Kumar</span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip" title="Total orders"><i
                                                    class="bi bi-bag-plus"></i> : <b>0</b></span>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip" title="Total users"><i
                                                    class="bi bi-people"></i> : <b>273</b></span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip" title="Total earnings"><i
                                                    class="bi bi-wallet2"></i> : <i
                                                    class="bi bi-currency-rupee"></i><b>5L</b></span>
                                            <span class="pe-2" data-bs-toggle="tooltip" title="Total messages"><i
                                                    class="bi bi-chat-square-dots"></i>: <b>0</b></span>
                                        </td>
                                        <td><img src="{{ asset('admin_css/assets/images/avatar.png') }}"
                                                class="seller-user-img" alt="user" data-bs-toggle="tooltip"
                                                title="Business Image"></td>
                                        <td>
                                            <i class="bi bi-telephone"></i><span class="ms-2">6390041900</span>
                                            <br>
                                            <i class="bi bi-envelope-at"></i><span class="ms-2">admin@gmail.com</span>
                                        </td>
                                        <td>
                                            <b>Pincode:</b>
                                            <span>221010</span>
                                            <br>
                                            <b>Area:</b>
                                            <span>Bhelupur, Sai baba mandir, Kamachha, Vinayaka Hospital</span>
                                            <br>
                                            <b>City:</b>
                                            <span>Varanasi</span>
                                            <br>
                                            <b>State:</b>
                                            <span>Uttar Pradesh</span>
                                            <br>
                                            <b>Country</b>
                                            <span>India</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="custom-dropdown">
                                                <a class="custom-status-btn rounded text-white" href="javascript:;"
                                                    role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" title="Status"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input status_update">
                                                        </div>
                                                        <span>Featured</span>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input status_update">
                                                        </div>
                                                        <span>Verified</span>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input status_update">
                                                        </div>
                                                        <span>Top Sellers</span>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input status_update">
                                                        </div>
                                                        <span>Inactive</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="mt-3 fw-bolder text-success">Active</div>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" class="custom-edit-btn rounded text-white"
                                                title="edit" wire:click="create()">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
