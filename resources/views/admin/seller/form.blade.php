<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Edit Seller</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.seller-list') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <h5 class="card-heading-h5">Seller Information:</h5>
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">Seller Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Seller name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Seller's email">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="number" class="form-label">Phone</label>
                                <input type="number" class="form-control" id="number" placeholder="Seller number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input type="text" class="form-control" id="password" placeholder="Password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="business-name">Business Name</label>
                                <input type="text" id="business-name" class="form-control" placeholder="Business name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="business-category">Business Category</label>
                                <select class="form-select" id="business-category">
                                    <option value="1">Select business category</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller-type">Seller Type</label>
                                <select class="form-select" id="seller-type">
                                    <option value="1">Select seller type</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="vendor-type">Vendor Type</label>
                                <select class="form-select" form="vendor-type">
                                    <option value="1">Select vendor type</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="area">Area</label>
                                <input type="text" class="form-control" id="area" placeholder="Address">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="landmark">Landmark</label>
                                <input type="text" class="form-control" id="landmark" placeholder="Landmark">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="pincode">Pincode</label>
                                <input type="number" class="form-control" id="pincode" placeholder="221010">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="city">City</label>
                                <select class="form-select" id="city">
                                    <option value="1">Varanasi</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="state">State</label>
                                <select class="form-select" id="state">
                                    <option value="1">Uttar Pradesh</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="country">Country</label>
                                <select class="form-select" id="scountry">
                                    <option value="1">India</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
