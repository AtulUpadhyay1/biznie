<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Product Category</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.product-category') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <div class="col-md-8 border-end">
                                <div class="row">
                                    <h5 class="card-heading-h5">Product Category Details:</h5>
                                    <div class="col-md-12 mb-3">
                                        <label for="business-category" class="form-label">Business Category</label>
                                        <select class="form-select @error('business_category_id') is-invalid @enderror" id="business-category"  wire:model="business_category_id">
                                            <option selected="">Select Business Category</option>
                                            @foreach($business_category_list as $business_category)
                                            <option value="{{$business_category->id}}">{{$business_category->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('business_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter name">
                                        @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="icon">Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $icon ? $icon : '<i class="fa fa-circle-o" aria-hidden="true"></i>'!!}</span>
                                            <input type="text" id="icon" class="form-control @error('icon') is-invalid @enderror" wire:model="icon" placeholder="Enter fa icon">
                                        </div>
                                        @error('icon') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <h5 class="card-heading-h5">SEO Section:</h5>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="title">Meta Title</label>
                                        <input type='text' id="title" class="form-control @error('meta_title') is-invalid @enderror" wire:model='meta_title' placeholder="Enter title">
                                        @error('meta_title') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="keyword">Meta Keywords</label>
                                        <input type='text' id="keyword" class="form-control @error('meta_keywords') is-invalid @enderror" wire:model='meta_keywords' placeholder="Enter keywords">
                                        @error('meta_keywords') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="FormControlTextarea" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="FormControlTextarea" rows="5" wire:model='meta_description' placeholder="Enter description"></textarea>
                                        @error('meta_description') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 border-start">
                                <div class="row">
                                    <h5 class="card-heading-h5">Images:</h5>
                                    <div class="col-md-12">
                                        <label class="form-label" for="product_category_thumbnail">Thumbnail Image</label>
                                        <input type='file' id="product_category_thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" wire:model="thumbnail">
                                        <label for="product_category_thumbnail">
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
                                    <div class="col-md-12">
                                        <label class="form-label" for="product_category_banner">Banner Image</label>
                                        <input type='file' id="product_category_banner" class="form-control @error('banner') is-invalid @enderror" wire:model="banner">
                                        <label for="product_category_banner">
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
