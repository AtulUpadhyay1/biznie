<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Vendor Details</h4>
                        </div>
                        <div class="col-6 text-end">
                            <x-cancel-btn text="Cancel" function="cancel()" />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <form>
                            <div class="row">
                                <h5 class="card-heading-h5">Business Information</h5>
                                <div class="col-md-6 mb-3">
                                    <label for="business-name" class="form-label">Business Name</label>
                                    <input type="text" class="form-control" id="business-name" placeholder="Business Name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="vendor-type" class="form-label">Vendor Type</label>
                                    <select class="form-select" id="vendor-type">
                                        <option value="1">Vendor Type</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_category" class="form-label">Business Category</label>
                                    <select class="form-select" id="business-category">
                                        <option value="1">Business Category</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="product-category" class="form-label">Product Category</label>
                                    <select class="form-select" id="product-category">
                                        <option value="1">Product Category</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="product-subcategory" class="form-label">Product Sub Category</label>
                                    <select class="form-select" id="product-subcategory">
                                        <option value="1">Product Sub Category</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="joining-date" class="form-label">Joining Date</label>
                                    <input type="date" class="form-control" id="joining-date" onfocus="(this.type='date')">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Thumbnail Image</label>
                                    <input type="file" id="thumbnail-img" class="form-control">
                                    <label for="thumbnail-img">
                                        <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                    </label>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cover Image</label>
                                    <input type="file" id="cover-img" class="form-control">
                                    <label for="cover-img">
                                        <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                    </label>
                                </div>
                                <h5 class="card-heading-h5">Vendor Information</h5>
                                <div class="col-md-6 mb-3">
                                    <label for="vendor-name" class="form-label">Vendor Name</label>
                                    <input type="text" class="form-control" id="vendor-name" placeholder="Vendor Name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="gender">Gender</label>
                                    <select class="form-select" id="gender">
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="phone">Phone Number</label>
                                    <input type="number" class="form-control" id="phone" placeholder="6390041900">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="sudhanshu@gmail.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="area">Area</label>
                                    <input type="text" class="form-control" id="area" placeholder="Address">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="landmark">Landmark</label>
                                    <input type="text" class="form-control" id="landmark" placeholder="Landmark">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="pincode">Pincode</label>
                                    <input type="number" class="form-control" id="pincode" placeholder="221010">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="city">City</label>
                                    <select class="form-select" id="city">
                                        <option value="1">Varanasi</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="state">State</label>
                                    <select class="form-select" id="state">
                                        <option value="1">Uttar Pradesh</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="country">Country</label>
                                    <select class="form-select" id="scountry">
                                        <option value="1">India</option>
                                    </select>
                                </div>
                                <h5 class="card-heading-h5">KYC Information</h5>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="gst-type">Company Under GST Type ?</label>
                                    <select class="form-select" id="gst-type">
                                        <option value="1">Composite</option>
                                        <option value="2">Regular</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="gst-no">GST Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle-fill text-success"></i></span>
                                        <input type="text" class="form-control" id="gst-no" placeholder="1234ABCD89000">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="identity-type">Indentity Type</label>
                                    <select class="form-select" id="identity-type">
                                        <option value="1">Pan Card</option>
                                        <option value="2">Aadhar Card</option>
                                        <option value="3">Others</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="identity-no">Indentity Number</label>
                                    <input type="text" class="form-control" id="identity-no" placeholder="abcd5686xyz">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Identity Proof</label>
                                    <input type="file" id="indentity-proof" class="form-control">
                                    <label for="indentity-proof">
                                        <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                    </label>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Identity With Selfie</label>
                                    <input type="file" id="identity-selfie" class="form-control">
                                    <label for="identity-selfie">
                                        <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                    </label>
                                </div>
                                <h5 class="card-heading-h5">SEO Section</h5>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" placeholder="Meta Title">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="meta_keywords">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" placeholder="Meta Keywords">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" rows="5" placeholder="Meta Description"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-submit-btn text="Update" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
