<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="card position-sticky customer-profile-card fixed-top">
                <div class="card-header">
                    <div class="text-center">
                        <img src="{{asset('admin_css/assets/images/avatar.png')}}" alt="" class="w-25 h-25">
                        <h5 class="text-dark mt-3">Sushanshu Kumar</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="custom-un-li">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.customer-profile')}}" wire:navigate>Customer Profile
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.customer-orders-list')}}" wire:navigate>Order Details
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">Payment History
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">RFQ
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-6 card-title">
                            <h5 class="mt-2">All Orders</h5>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud btn-icon-prepend"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                    Download Report
                                </button>
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
                                    <th>Order Code</th>
                                    <th>No Of products</th>
                                    <th style="width: 20%">Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>#000134e8263826823</td>
                                    <td>12</td>
                                    <td><span class="text-danger fw-bolder">RS 14000</span></td>
                                    <td class="text-primary fw-bolder">Pending</td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down icon-sm me-2"></i><span>Download</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>#000134e8263826823</td>
                                    <td>12</td>
                                    <td><span class="text-danger fw-bolder">RS 14000</span></td>
                                    <td class="text-success fw-bolder">Completed</td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down icon-sm me-2"></i><span>Download</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>#000134e8263826823</td>
                                    <td>12</td>
                                    <td><span class="text-danger fw-bolder">RS 14000</span></td>
                                    <td class="text-danger fw-bolder">Cancelled</td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down icon-sm me-2"></i><span>Download</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>#000134e8263826823</td>
                                    <td>12</td>
                                    <td><span class="text-danger fw-bolder">RS 14000</span></td>
                                    <td class="text-warning fw-bolder">Dispatched</td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down icon-sm me-2"></i><span>Download</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
