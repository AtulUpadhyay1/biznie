<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>All Customers</h4>
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
                                    <i class="btn-icon-prepend" data-feather="download-cloud"></i>
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
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th  style="width: 15%">Email Address</th>
                                    <th>Registration Date</th>
                                    <th>Last Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Vishal Aaryan</td>
                                    <td>6390041900</td>
                                    <td>sudhanshukumar1234@gmail.com</td>
                                    <td>03/10/2023</td>
                                    <td>03/10/2023, <br>6.45 Am</td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i
                                                class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                            <a class="dropdown-item d-flex align-items-center" href=""><i
                                                class="bi bi-person-slash icon-sm me-2"></i><span>Block</span></a>
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
</div>
