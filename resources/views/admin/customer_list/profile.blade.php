<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="card position-sticky customer-profile-card fixed-top">
                        <div class="card-header">
                            <div class="text-center">
                                <img src="{{asset('admin_css/assets/images/avatar.png')}}" alt="" class="w-25 h-25">
                                <h6 class="text-dark mt-3">Sushanshu Kumar</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="custom-un-li">
                                <li class="nav-item">
                                    <a class="nav-link" href="" wire:navigate>Customer Profile
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="">Order Details
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
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="card-heading-h5">Customer Profile</h5>
                                </div>
                            </div>
                            <div class="px-3">
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
        </div>
    </div>
</div>
