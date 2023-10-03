<div>
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Business Category</h4>
                        </div>
                        <div class="col-6 text-end">
                            <x-cancel-btn text="Cancel" function="cancel()" />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <div class="col-md-8 border-end">
                                <div class="row">
                                    <h5 class="card-heading-h5">Business Category Details:</h5>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" id="name" wire:model.defer="name" placeholder="Enter name">
                                        @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="icon">Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{!! $icon ? $icon : '<i class="fa fa-circle-o" aria-hidden="true"></i>'!!}</span>
                                            <input type="text" id="icon" class="form-control @error('icon') is-invalid @enderror"  wire:model.defer="icon" placeholder="Enter fa icon">
                                        </div>
                                        @error('icon') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <h5 class="card-heading-h5">SEO Section:</h5>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="title">Meta Title</label>
                                        <input type='text' id="title" class="form-control @error('meta_title') is-invalid @enderror" wire:model.defer='meta_title' placeholder="Enter title">
                                        @error('meta_title') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="keyword">Meta Keywords</label>
                                        <input type='text' id="keyword" class="form-control @error('meta_keywords') is-invalid @enderror" wire:model.defer='meta_keywords' placeholder="Enter keywords">
                                        @error('meta_keywords') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="FormControlTextarea" class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="FormControlTextarea" rows="5" wire:model.defer='meta_description' placeholder="Enter description"></textarea>
                                        @error('meta_description') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 border-start">
                                <div class="row">
                                    <h5 class="card-heading-h5">Images:</h5>
                                    <div class="col-md-12">
                                        <label class="form-label" for="business_category_thumbnail">Thumbnail Image</label>
                                        <input type='file' id="business_category_thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" wire:model.defer="thumbnail">
                                        <label for="business_category_thumbnail">
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
                                        <label class="form-label" for="business_category_banner">Banner Image</label>
                                        <input type='file' id="business_category_banner" class="form-control @error('banner') is-invalid @enderror" wire:model.defer="banner">
                                        <label for="business_category_banner">
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
                            <div class="col-md-12">
                                <x-submit-btn text="{{$hidden_id?'Update':'Save'}}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
