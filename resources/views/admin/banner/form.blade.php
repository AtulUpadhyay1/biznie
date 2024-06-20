<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.banner.index') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ isset($hidden_id) ? 'update()' : 'save()' }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="banner_type" class="form-label">Banner Type</label>
                                    <select id="banner_type" class="form-select @error('banner_type') is-invalid @enderror" wire:model="banner_type" >
                                        <option value="Main Banner">Main Banner</option>
                                        <option value="Popup Banner">Popup Banner</option>
                                        <option value="Footer Banner">Footer Banner</option>
                                        <option value="Main Section Banner">Main Section Banner</option>
                                    </select>
                                    @error('banner_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="banner_url" class="form-label">Banner Url</label>
                                    <input type="text" class="form-control @error('url') is-invalid @enderror" id="banner_url" wire:model="url">
                                    @error('url')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="resource_type" class="form-label">Resource Type</label>
                                    <select id="resource_type" class="form-select @error('resource_type') is-invalid @enderror" wire:model.live="resource_type">
                                        <option value="" selected>Select</option>
                                        <option value="product">Product</option>
                                        <option value="category">Category</option>
                                        <option value="business">Business</option>
                                        <option value="brand">Brand</option>
                                    </select>
                                    @error('resource_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                @if ($resource_type == 'product')
                                    <div class="mb-3">
                                        <label for="resource_id" class="form-label">Product</label>
                                        <select id="resource_id" class="form-select @error('resource_id') is-invalid @enderror" wire:model="resource_id">
                                            <option value="" selected>Select Product</option>
                                            @foreach ($product_list as $product)
                                                <option value="{{ $product->id }}"> {{ $product->name }} </option>
                                            @endforeach
                                        </select>
                                        @error('resource_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif
                                @if ($resource_type == 'category')
                                    <div class="mb-3">
                                        <label for="resource_id" class="form-label">Category</label>
                                        <select id="resource_id" class="form-select @error('resource_id') is-invalid @enderror" wire:model="resource_id">
                                            <option value="" selected>Select Category</option>
                                            @foreach ($category_list as $category)
                                                <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                            @endforeach
                                        </select>
                                        @error('resource_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif
                                @if ($resource_type == 'business')
                                    <div class="mb-3">
                                        <label for="resource_id" class="form-label">Business</label>
                                        <select id="resource_id" class="form-select @error('resource_id') is-invalid @enderror" wire:model="resource_id">
                                            <option value="" selected>Select Business</option>
                                            @foreach ($busienss_list as $busienss)
                                                <option value="{{ $busienss->id }}"> {{ $busienss->name }} </option>
                                            @endforeach
                                        </select>
                                        @error('resource_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif
                                @if ($resource_type == 'brand')
                                    <div class="mb-3">
                                        <label for="resource_id" class="form-label">Brand</label>
                                        <select id="resource_id" class="form-select @error('resource_id') is-invalid @enderror" wire:model="resource_id">
                                            <option value="" selected>Select Brand</option>
                                            @foreach ($brand_list as $brand)
                                                <option value="{{ $brand->id }}"> {{ $brand->name }} </option>
                                            @endforeach
                                        </select>
                                        @error('resource_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="photo">
                                    Banner
                                    <br>
                                    @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" height="150" width="300">
                                    @elseif ($showPhoto)
                                        <img src="{{ $showPhoto }}" height="150" width="300">
                                    @else
                                        <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                    @endif

                                </label>
                                <input type="file" id="photo" wire:model="photo" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                @error('photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{ isset($hidden_id) ? 'Update' : 'Save' }}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
