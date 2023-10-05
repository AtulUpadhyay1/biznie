<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Product List</h4>
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
                            <form class="custom-search-bar">
                                <div class="input-group">
                                    <span class="input-group-text"> <i data-feather="search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search here...">
                                </div>
                            </form>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Sub Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Business Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Seller Type</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Sort By</option>
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
                                    <th>Thumbnail Image</th>
                                    <th>Product Name</th>
                                    <th>Business Name</th>
                                    <th>Number Of Sale</th>
                                    <th>Total Stock</th>
                                    <th>Base Price</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><img src="{{asset('admin_css/assets/images/avatar.png')}}" class="custom-table-img" alt=""></td>
                                    <td>U.neek Men's Plain Regular Fit Cotton Tshirt</td>
                                    <td><b class="text-primary">Shri Sai Textiles</b></td>
                                    <td>234</td>
                                    <td>18</td>
                                    <td><b class="text-sucess">RS 600.00</b></td>
                                    <td>3.6<i class="bi bi-star-fill text-warning ms-1"></i></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status_update" value="10" checked>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input featured_update" value="20" checked>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-copy icon-sm me-2"></i><span>Duplicate</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td><img src="{{asset('admin_css/assets/images/avatar.png')}}" class="custom-table-img" alt=""></td>
                                    <td>U.neek Men's Plain Regular Fit Cotton Tshirt</td>
                                    <td><b class="text-primary">Geetanjali Costmetics Store</b></td>
                                    <td>234</td>
                                    <td>18</td>
                                    <td><b class="text-sucess">RS 600.00</b></td>
                                    <td>3.6<i class="bi bi-star-fill text-warning ms-1"></i></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status_update" value="10" checked>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input featured_update" value="20" checked>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-copy icon-sm me-2"></i><span>Duplicate</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td><img src="{{asset('admin_css/assets/images/avatar.png')}}" class="custom-table-img" alt=""></td>
                                    <td>U.neek Men's Plain Regular Fit Cotton Tshirt</td>
                                    <td><b class="text-primary">Abhyuday Fire Works</b></td>
                                    <td>234</td>
                                    <td>18</td>
                                    <td><b class="text-sucess">RS 600.00</b></td>
                                    <td>3.6<i class="bi bi-star-fill text-warning ms-1"></i></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status_update" value="10" checked>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input featured_update" value="20" checked>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-copy icon-sm me-2"></i><span>Duplicate</span></a>
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
