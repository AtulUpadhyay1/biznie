<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Edit Product</h4>
                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.product-list') }}" wire:navigate>
                                <i class="bi bi-x-lg"></i>Cancel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <h5 class="card-heading-h5">Product Information:</h5>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="business-category">Business Category</label>
                                <select class="form-select" id="business-category">
                                    <option value="1">Business Category</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Product Category</label>
                                <select class="form-select" id="category">
                                    <option value="1">Product Catgeory</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sub-category" class="form-label">Product Sub Category</label>
                                <select class="form-select" id="sub-category">
                                    <option value="1">Product sub category</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sub-sub-category" class="form-label">Product Sub Sub Category</label>
                                <select class="form-select" id="sub-sub-category">
                                    <option value="1">Product sub sub category</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="business-name">Business name</label>
                                <input type="text" class="form-control" id="business-name"
                                    placeholder="Business name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="product-name">Product Name</label>
                                <input type="text" class="form-control" id="product-name" placeholder="Product name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="brand">Brand</label>
                                <select class="form-select" id="brand">
                                    <option value="1">Select Brand</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="unit">Unit</label>
                                <select class="form-select" id="unit">
                                    <option value="1">Select unit</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="hsn">HSN/SAC Code</label>
                                <input type="text" class="form-control" id="hsn" placeholder="HSN/SAC Code">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="icon">Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-circle"
                                            aria-hidden="true"></i></span>
                                    <input type="text" class="form-control" id="icon"
                                        placeholder="Enter icon class">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="tag">Tags</label>
                                <input type="text" class="form-control" id="tag" placeholder="Enter tags">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="refundable">Refundable</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="refundable" value="20"
                                        checked="">
                                </div>
                            </div>
                            <h5 class="card-heading-h5">Product Images</h5>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="product-thumbnail">Thumbnail Image</label>
                                <input type="file" id="product-thumbnail" class="form-control">
                                <label for="product-thumbnail">
                                    <img class="label-thumbnail"
                                        src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                </label>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="gallery">Gallery</label>
                                <div class="input-images">
                                    <div class="image-uploader has-files">
                                        <input type="file" id="gallery" name="images[]"
                                            accept=".jpg,.jpeg,.png,.gif,.svg" multiple="multiple">
                                        <div class="uploaded">
                                            <div class="uploaded-image" data-preloaded="true">
                                                <img  src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                                <button class="delete-image"><i class="bi bi-x-circle"></i></button>
                                                <input type="hidden" name="old[]" value="257">
                                            </div>
                                        </div>
                                        <div class="upload-text"><i class="bi bi-cloud-upload"></i><span>Drag &amp;
                                                Drop files here or click to browse</span></div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="card-heading-h5">Product Videos</h5>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="video">Video Source</label>
                                <input type="text" class="form-control" id="video" placeholder="eg. Youtube, Facebook, Instagram">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="video-link">Video Link</label>
                                <input type="url" class="form-control" id="video-link" placeholder="Enter video links">
                            </div>
                            <h5 class="card-heading-h5">Product Variation</h5>
                            <h5 class="card-heading-h5">Product Price + Stock</h5>
                            <h5 class="card-heading-h5">Product Description</h5>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="desc">Description</label>
                                <textarea class="form-control" id="desc" rows="6" placeholder="Product Description"></textarea>
                            </div>
                            <h5 class="card-heading-h5">Product Specifications</h5>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="specs">Specifications</label>
                                <textarea class="form-control" id="specs" rows="6" placeholder="Product Specifications"></textarea>
                            </div>
                            <h5 class="card-heading-h5">More Info</h5>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="info">More Info</label>
                                <textarea class="form-control" id="info" rows="6" placeholder="More Info"></textarea>
                            </div>
                            <h5 class="card-heading-h5">Product Shipping Cost</h5>
                            <div class="col-md-12 mb-3">
                                <table class="custom-table">
                                    <tbody>
                                        <tr>
                                            <td><b>Free Shipping</b></td>
                                            <td><label class="form-label mb-0" for="free-shipping">Status</label></td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="free-shipping" value="20">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Flat Rate</b></td>
                                            <td><label class="form-label mb-0" for="flat-rate">Status</label></td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="flat-rate" value="20" checked="">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="1"></td>
                                            <td><label class="form-label mb-0" for="Scost">Shipping Cost</label></td>
                                            <td>
                                                <input type="number" class="form-control" id="Scost" placeholder="0">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h5 class="card-heading-h5">PDF Specifications</h5>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="pdf">PDF Specificaions</label>
                                <input type="file" class="form-control" id="pdf">
                            </div>
                            <h5 class="card-heading-h5">SEO Section</h5>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="title">Meta Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Meta title">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="keyword">Meta Keyword</label>
                                <input type="text" class="form-control" id="keyword" placeholder="Meta keywords">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="meta-description">Meta Description</label>
                                <textarea class="form-control" id="meta-description" rows="5" placeholder="Meta description"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Submit" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
