<div>
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Sub Sub Category</h4>
                        </div>
                        <div class="col-6 text-end">
                            <x-cancel-btn text="Cancel" function="cancel()" />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="forms-sample" wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="business-category" class="form-label">Business Category</label>
                                <select class="form-select mb-4 mb-md-0 @error('business_category_id') is-invalid @enderror" id="business-category"  wire:model.defer="business_category_id" wire:change="setProductCategoryList()">
                                    <option selected="">Select Business Category</option>
                                    @foreach($business_category_list as $business_category)
                                    <option value="{{$business_category->id}}">{{$business_category->name}}</option>
                                    @endforeach
                                </select>
                                @error('business_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="business-product-category" class="form-label">Product Category</label>
                                <select class="form-select @error('product_category_id') is-invalid @enderror" id="business-product-category"  wire:model.defer="product_category_id" wire:change="setProductSubCategoryList()">
                                    <option selected="">Select Product Category</option>
                                    @foreach($product_category_list as $product_category)
                                    <option value="{{$product_category->id}}">{{$product_category->name}}</option>
                                    @endforeach
                                </select>
                                @error('product_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="product-sub-category" class="form-label">Product Sub Category</label>
                                <select class="form-select @error('product_sub_category_id') is-invalid @enderror" id="product-sub-category" wire:model.defer="product_sub_category_id">
                                    <option selected>Select Product Sub Category</option>
                                    @foreach ($product_sub_category_list as $product_sub_category)
                                        <option value="{{$product_sub_category->id}}">{{$product_sub_category->name}}</option>
                                    @endforeach
                                </select>
                                @error('product_sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mt-3 mt-md-0" for="name">Name</label>
                                <input type="text" id="name" class="form-control mb-3 mb-md-0 @error('name') is-invalid @enderror" wire:model.defer="name">
                                @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="icon">Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text">{!! $icon ? $icon : '<i class="fa fa-circle-o" aria-hidden="true"></i>'!!}</span>
                                    <input type="text" id="icon" class="form-control @error('icon') is-invalid @enderror" wire:model.defer="icon">
                                </div>
                                @error('icon') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail Image</label>
                                <input type='file' id="product_subsubcategory_thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" wire:model.defer="thumbnail">
                                <label for="product_subsubcategory_thumbnail">
                                    @if($thumbnail)
                                        <img src="{{$thumbnail->temporaryUrl()}}" class="label-thumbnail">
                                    @elseif ($showThumbnail)
                                        <img src="{{asset('storage/'.$showThumbnail)}}" class="label-thumbnail">
                                    @else
                                        <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                    @endif
                                </label>
                                @error('thumbnail') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Banner Image</label>
                                <input type='file' id="product_subsubcategory_banner" class="form-control @error('banner') is-invalid @enderror" wire:model.defer="banner">
                                <label for="product_subsubcategory_banner">
                                    @if($banner)
                                        <img src="{{$banner->temporaryUrl()}}" class="label-banner">
                                    @elseif ($showBanner)
                                        <img src="{{asset('storage/'.$showBanner)}}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{asset('admin_css/assets/images/others/placeholder.jpg')}}" >
                                    @endif
                                </label>
                                @error('banner') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="title">Meta Title</label>
                                <input type='text' id="title" class="form-control mb-3 mb-md-0 @error('meta_title') is-invalid @enderror" wire:model.defer='meta_title'>
                                @error('meta_title') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="keyword">Meta Keywords</label>
                                <input type='text' id="keyword" class="form-control mb-3 mb-md-0 @error('meta_keywords') is-invalid @enderror" wire:model.defer='meta_keywords'>
                                @error('meta_keywords') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="FormControlTextarea" class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" id="FormControlTextarea" rows="5" wire:model.defer='meta_description'></textarea>
                                @error('meta_description') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{$hidden_id?'Update':'Save'}}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
