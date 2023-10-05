<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    @if ($formMode)
        @include('admin.seller_list.form')
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 card-title">
                                <h4>All Sellers</h4>
                            </div>
                            <div class="col-6 text-end">
                                <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                    <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                                        <span class="input-group-text input-group-addon bg-transparent border-danger"
                                            data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                                        <input type="text" class="form-control bg-transparent border-danger"
                                            placeholder="Select date" data-input>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud btn-icon-prepend"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                        Download Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-horizontal filter-list-group">
                            <li class="list-group-item border-0">
                                <form class="custom-search-bar">
                                    <div class="input-group">
                                        <span class="input-group-text"> <i data-feather="search"></i></span>
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
                                        <th style="width: 25%">Name</th>
                                        <th>Contact info</th>
                                        <th>Registration Date</th>
                                        <th>Updation Date</th>
                                        <th style="width: 10%">Last Active</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span>Sudhanshu Textile Indusrty</span>
                                            <br>
                                            <b>User Name:</b>
                                            <span>Sudhanshu Kumar Jhandewala</span>
                                        </td>
                                        <td>
                                            <i class="bi bi-telephone"></i><span class="ms-2">6390041900</span>
                                            <br>
                                            <i class="bi bi-envelope-at"></i><span class="ms-2">sudhanshu@gmail.com</span>
                                        </td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023</td>
                                        <td>03/10/2023, <br>5.45 Pm</td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn">
                                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                        class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a href="javascript:;" wire:click="edit()"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a href="javascript:;"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
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
    @endif
</div>
