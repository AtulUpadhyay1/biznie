<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.customer_list.customer_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h5 class="mt-2">Customer Profile</h5>
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
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Customer name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" id="phone" placeholder="Customer mobile number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email Id">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" placeholder="Password">
                                    <span class="input-group-text bg-white"><i class="bi bi-eye-fill"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <h5 class="card-heading-h5">Primary Address</h5>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="address1">Address Line 1</label>
                                <input type="text" class="form-control" id="address1" placeholder="Address line 1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="address2">Address Line 2</label>
                                <input type="text" class="form-control" id="address2" placeholder="Address line 2">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="landmark">Landmark</label>
                                <input type="text" class="form-control" id="landmark" placeholder="Landmark">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pincode">Pincode</label>
                                <input type="number" class="form-control" id="pincode" placeholder="Pincode">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="city">City</label>
                                <select class="form-select" id="city">
                                    <option>Select City</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="state">State</label>
                                <select class="form-select" id="state">
                                    <option>Select State</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="country">Country</label>
                                <select class="form-select" id="country">
                                    <option>Select country</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Update" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
