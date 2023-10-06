<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Brands</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.brand') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-8 border-end">
                                <div class="row">
                                    <h5 class="card-heading-h5">Brand Details:</h5>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control" id="name"
                                            placeholder="Enter name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="icon">Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-circle-o"
                                                    aria-hidden="true"></i></span>
                                            <input type="text" class="form-control" id="icon"
                                                placeholder="Enter fa icon">
                                        </div>
                                    </div>
                                    <h5 class="card-heading-h5">SEO Section:</h5>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="title">Meta Title</label>
                                        <input type="text" class="form-control" id="title" placeholder="Enter title">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="keyword">Meta Keywords</label>
                                        <input type="text" class="form-control" id="keyword" placeholder="Enter keywords">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label" for="desc">Meta Description</label>
                                        <textarea class="form-control" id="desc" rows="5" placeholder="Enter description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 border-start">
                                <div class="row">
                                    <h5 class="card-heading-h5">Images:</h5>
                                    <div class="col-md-12">
                                        <label class="form-label" for="brand_thumbnail">Thumbnail Image</label>
                                        <input type="file" class="form-control" id="brand_thummbnail">
                                        <label for="brand_thumbnail">
                                            <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                        </label>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="brand_banner">Banner Image</label>
                                        <input type="file" class="form-control" id="brand_bannner">
                                        <label for="brand_banner">
                                            <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                        </label>
                                    </div>
                                </div>
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
