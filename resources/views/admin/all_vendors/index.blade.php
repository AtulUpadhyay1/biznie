<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>All Vendors</h4>
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
                    <ul class="list-group list-group-horizontal vendors-list-group">
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>Biz Category</option>
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
                        <table class="vendors-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Business Name</th>
                                    <th>Image</th>
                                    <th>Contact</th>
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
                                        <hr class="m-0">
                                        <span class="px-2 border-end"><i class="bi bi-bag-plus"></i> : <b>0</b></span>
                                        <span class="px-2 border-end"><i class="bi bi-people"></i> : <b>273</b></span>
                                        <span class="px-2 border-end"><i class="bi bi-wallet2"></i> : <i
                                                class="bi bi-currency-rupee"></i><b>5L</b></span>
                                        <span class="px-2"><i class="bi bi-chat-square-dots"></i>: <b>0</b></span>
                                    </td>
                                    <td>Image</td>
                                    <td>Village Pratapgarh Uttar Pradesh 222001</td>
                                    <td>Varanasi Uttarpradesh 222001</td>
                                    <td class="text-center">
                                        <div class="spinner-border text-success"></div>
                                        <span class="text-success">Active</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:;" class="vendor-edit-btn rounded">
                                            <i class="bi bi-pencil-square icon-lg text-white"></i>
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
</div>
