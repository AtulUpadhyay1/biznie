<div>
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Sub Category</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-icon-text float-end align-items-center" wire:click="cancel()">
                                <i class="bi bi-x-lg me-1"></i>Cancel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="forms-sample" wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="business-product-category" class="form-label">Business Category:</label>
                                <select class="form-select mb-4 mb-md-0" id="business-category"  wire:model.defer="business_category_id" wire:change="setProductCategoryList()">
                                    <option selected="">Select Business Category</option>
                                    @foreach($business_category_list as $business_category)
                                    <option value="{{$business_category->id}}">{{$business_category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="business-product-category" class="form-label">Product Category:</label>
                                <select class="form-select" id="business-product-category"  wire:model.defer="product_category_id">
                                    <option selected="">Select Product Category</option>
                                    @foreach($product_category_list as $product_category)
                                    <option value="{{$product_category->id}}">{{$product_category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Name:</label>
                                <input type="text" class="form-control mb-4 mb-md-0" wire:model.defer="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Icon:</label>
                                <div class="input-group">
                                    <span class="input-group-text">{!! $icon ? $icon : '<i class="fa fa-circle-o" aria-hidden="true"></i>'!!}</span>
                                    <input type="text" class="form-control" wire:model.defer="icon">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail Image:</label>
                                <input type='file' id="product_subcategory_thumbnail" class="form-control" wire:model.defer="thumbnail">
                                <label for="product_subcategory_thumbnail">
                                    @if($thumbnail)
                                        <img src="{{$thumbnail->temporaryUrl()}}" class="label-thumbnail">
                                    @elseif ($showThumbnail)
                                        <img src="{{asset('storage/'.$showThumbnail)}}" class="label-thumbnail">
                                    @endif
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Banner Image:</label>
                                <input type='file' id="product_subcategory_banner" class="form-control" wire:model.defer="banner">
                                <label for="product_subcategory_banner">
                                    @if($banner)
                                        <img src="{{$banner->temporaryUrl()}}" class="label-banner">
                                    @elseif ($showBanner)
                                        <img src="{{asset('storage/'.$showBanner)}}" class="label-banner">
                                    @endif
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Meta Title:</label>
                                <input type='text' class="form-control mb-3 mb-md-0" wire:model.defer='meta_title'>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords:</label>
                                <input type='text' class="form-control mb-3 mb-md-0" wire:model.defer='meta_keywords'>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="FormControlTextarea" class="form-label">Meta Description:</label>
                                <textarea class="form-control" id="FormControlTextarea" rows="5" wire:model.defer='meta_description'></textarea>
                           </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-warning">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

