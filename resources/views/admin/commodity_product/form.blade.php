<div>
    @section('title', config('app.name') . ' | '.$page_title)
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.commodity-product.index')}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter product name" wire:model="name">
                            @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="hsn_code" class="form-label">HSN Code</label>
                            <input type="text" class="form-control @error('hsn_code') is-invalid @enderror" id="hsn_code" placeholder="Enter hsn code" wire:model="hsn_code">
                            @error('hsn_code') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" cols="30" rows="5" placeholder="Enter product description" wire:model="description"></textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="specification_notes" class="form-label">Specification Notes</label>
                            <textarea class="form-control @error('specification_notes') is-invalid @enderror" id="specification_notes" cols="30" rows="5" placeholder="Enter product specification notes" wire:model="specification_notes"></textarea>
                            @error('specification_notes') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>General setup</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="min_order_qty" class="form-label">Min Order Qty <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('min_order_qty') is-invalid @enderror" id="min_order_qty" placeholder="Enter product min order qty" wire:model="min_order_qty">
                                @error('min_order_qty') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="order_amount_type" class="form-label">Order Amount Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('order_amount_type') is-invalid @enderror" id="order_amount_type" wire:model.live="order_amount_type">
                                    <option value="percent">Percent</option>
                                    <option value="flat">Flat</option>
                                </select>
                                @error('order_amount_type') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="required_order_amount" class="form-label">Required Order Amount (/MT)<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="{{ $order_amount_type == 'percent' ? 'bi bi-percent' : 'bi bi-currency-rupee'}}"></i></span>
                                    <input type="number" class="form-control @error('required_order_amount') is-invalid @enderror" id="required_order_amount" placeholder="Enter product min order qty" wire:model="required_order_amount" @if($order_amount_type == 'percent') step="0.01" max="100" @endif>
                                </div>
                                @error('required_order_amount') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                {{-- wire:ignore keeps select2's own DOM alive across Livewire re-renders.
                                     Because Blade can no longer refresh the <option>s, the component pushes
                                     them with a `bz-options` event (see ProductLookupQuickAdd). --}}
                                <div class="input-group bz-select-group" wire:ignore>
                                    <select class="form-select bz-select2 @error('category_id') is-invalid @enderror"
                                        id="category_id" data-prop="category_id" data-placeholder="Select Category">
                                        <option value="">Select Category</option>
                                        @foreach ($category_list as $category_data)
                                            <option value="{{ $category_data->id }}" @selected($category_id == $category_data->id)>{{ $category_data->name }}@if($category_data->attributes) (@foreach ($category_data->attributes as $attributes){{ getAttribute($attributes)?->name }}@if(!$loop->last), @endif @endforeach)@endif</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary btn-icon" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qaCategoryModal" title="Add new category">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sub_category_id" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                <div class="input-group bz-select-group" wire:ignore>
                                    <select class="form-select bz-select2 @error('sub_category_id') is-invalid @enderror"
                                        id="sub_category_id" data-prop="sub_category_id" data-placeholder="Select Sub Category">
                                        <option value="">Select Sub Category</option>
                                        @foreach ($sub_category_list as $sub_category_data)
                                            <option value="{{ $sub_category_data->id }}" @selected($sub_category_id == $sub_category_data->id)>{{ $sub_category_data->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary btn-icon" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qaSubCategoryModal" title="Add new sub category">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                @error('sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sub_sub_category_id" class="form-label">Sub Sub Category</label>
                                <div class="input-group bz-select-group" wire:ignore>
                                    <select class="form-select bz-select2 @error('sub_sub_category_id') is-invalid @enderror"
                                        id="sub_sub_category_id" data-prop="sub_sub_category_id" data-placeholder="Select Sub Sub Category">
                                        <option value="">Select Sub Sub Category</option>
                                        @foreach ($sub_sub_category_list as $sub_sub_category_data)
                                            <option value="{{ $sub_sub_category_data->id }}" @selected($sub_sub_category_id == $sub_sub_category_data->id)>{{ $sub_sub_category_data->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary btn-icon" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qaSubSubCategoryModal" title="Add new sub sub category">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                @error('sub_sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            {{-- <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id" data-placeholder="Select brand" multiple>
                                        @foreach ($brand_list as $brand_data)
                                            <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div> --}}

                            <div class="col-md-4 mb-3">
                                <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                                <div class="input-group bz-select-group" wire:ignore>
                                    <select class="form-select bz-select2 @error('unit_id') is-invalid @enderror"
                                        id="unit_id" data-prop="unit_id" data-placeholder="Select Unit">
                                        <option value="">Select Unit</option>
                                        @foreach ($unit_list as $unit_data)
                                            <option value="{{ $unit_data->id }}" @selected($unit_id == $unit_data->id)>{{ $unit_data->short_name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary btn-icon" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qaUnitModal" title="Add new unit">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                @error('unit_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="attribute">Attribute <span class="text-danger">*</span></label>
                                <div class="input-group bz-select-group" wire:ignore>
                                    <select class="form-select bz-select2 @error('attribute') is-invalid @enderror"
                                        id="attribute" data-prop="attribute" data-placeholder="Select Attribute" multiple
                                        @if($variation_count > 0) disabled @endif>
                                        @foreach($attribute_list as $attribute_data)
                                            <option value="{{ $attribute_data->id }}" @selected(in_array($attribute_data->id, (array) $attribute))>{{ $attribute_data->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary btn-icon" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qaAttributeModal" title="Add new attribute"
                                        @disabled($variation_count > 0)>
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                @error('attribute') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <label for="packaging_type" class="form-label mb-0">Packaging Type <span class="text-danger">*</span></label>
                                    <button class="btn btn-secondary btn-sm btn-icon" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qaPackagingTypeModal" title="Add new packaging type">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                <div class="d-flex flex-wrap mt-2">
                                    @foreach ($packaging_type_list as $packaging_type_data)
                                        <div class="form-check me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" id="packaging_type_{{ $packaging_type_data->id }}" value="{{ $packaging_type_data->id }}" wire:model.live="packaging_type">
                                            <label class="form-check-label" for="packaging_type_{{ $packaging_type_data->id }}">
                                                {{ $packaging_type_data->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('packaging_type') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            @foreach ($packaging_type_name as $packaging_types)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="packaging_type_price_{{$loop->iteration}}">{{$packaging_types}} Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{$packaging_types}}</span>
                                        <input type="number" id="packaging_type_price_{{$loop->iteration}}" class="form-control" placeholder="Enter {{$packaging_types}} Price" wire:model="packaging_type_price.{{$loop->iteration}}">
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card card-body">
                            <label class="form-label" for="thumbnail">
                                Product Thumbnail <span class="text-danger">*</span>
                                <br>
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" height="200" width="200">
                                @elseif ($show_thumbnail)
                                    <img src="{{asset($show_thumbnail)}}" height="200" width="200">
                                @else
                                    <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                @endif

                            </label>
                            <input type="file" id="thumbnail" wire:model="thumbnail" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            @error('thumbnail') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-body">
                            <label class="form-label" for="images">
                                Product Images
                                <br>
                                @if ($images)
                                    <img src="{{ $images->temporaryUrl() }}" height="200" width="200">
                                @elseif ($show_image)
                                    <img src="{{asset($show_image)}}" height="200" width="200">
                                @else
                                    <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                @endif
                            </label>
                            <input type="file" id="images" wire:model="images" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Product video</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="youtube_link" class="form-label">Youtube Video Link </label>
                                <span class="text-info"> (Optional please provide embed link not direct link.)</span>
                                <input type="text" class="form-control @error('video_url') is-invalid @enderror" id="youtube_link" placeholder="Enter youtube video embed link" wire:model="video_url">
                                @error('video_url') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Seo section</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" placeholder="Enter meta title" wire:model="meta_title">
                                    @error('meta_title') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" cols="30" rows="5" placeholder="Enter meta description" wire:model="meta_description"></textarea>
                                    @error('meta_description') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4 ps-5">
                                <label class="form-label" for="meta_image">
                                    Meta Image
                                    <br>
                                    @if ($meta_image)
                                        <img src="{{ $meta_image->temporaryUrl() }}" height="200" width="200">
                                    @elseif ($show_meta_image)
                                        <img src="{{asset($show_meta_image)}}" height="200" width="200">
                                    @else
                                        <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                    @endif

                                </label>
                                <input type="file" id="meta_image" wire:model="meta_image" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text=" Save" />
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- ---------------------------------------------------------------------
         Quick-add modals. They sit outside the <form> so a nested submit can
         never fire the product save. `wire:ignore.self` protects the modal
         element's own attributes (Bootstrap toggles .show / style on it) while
         still letting Livewire morph the fields inside.
         ------------------------------------------------------------------ --}}

    <div class="modal fade" id="qaCategoryModal" tabindex="-1" aria-labelledby="qaCategoryModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="qa_business_category_id">Business Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('qa_business_category_id') is-invalid @enderror" id="qa_business_category_id" wire:model="qa_business_category_id">
                            <option value="">Select Business Category</option>
                            @foreach ($business_category_list as $business_category_data)
                                <option value="{{ $business_category_data->id }}">{{ $business_category_data->name }}</option>
                            @endforeach
                        </select>
                        @error('qa_business_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="qa_category_name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('qa_category_name') is-invalid @enderror" id="qa_category_name" placeholder="Enter category name" wire:model="qa_category_name">
                        @error('qa_category_name') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <x-submit-btn text="Save Category" function="saveQuickCategory" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qaSubCategoryModal" tabindex="-1" aria-labelledby="qaSubCategoryModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaSubCategoryModalLabel">Add Sub Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $qaParentCategory = collect($category_list)->first(fn ($c) => $c->id == $category_id);
                    @endphp
                    <div class="mb-3">
                        <label class="form-label" for="qa_sub_category_parent">Category</label>
                        <input type="text" class="form-control" id="qa_sub_category_parent" readonly
                            value="{{ $qaParentCategory?->name ?? 'No category selected yet' }}">
                        <small class="text-muted">The sub category will be created under this category.</small>
                    </div>
                    <label class="form-label" for="qa_sub_category_name">Sub Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('qa_sub_category_name') is-invalid @enderror" id="qa_sub_category_name" placeholder="Enter sub category name" wire:model="qa_sub_category_name">
                    @error('qa_sub_category_name') <small class="text-danger">{{ $message }}</small>@enderror
                    @error('category_id') <small class="text-danger d-block">{{ $message }}</small>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <x-submit-btn text="Save Sub Category" function="saveQuickSubCategory" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qaSubSubCategoryModal" tabindex="-1" aria-labelledby="qaSubSubCategoryModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaSubSubCategoryModalLabel">Add Sub Sub Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $qaParentCategory2 = collect($category_list)->first(fn ($c) => $c->id == $category_id);
                        $qaParentSubCategory = collect($sub_category_list)->first(fn ($c) => $c->id == $sub_category_id);
                    @endphp
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="qa_ssc_parent_category">Category</label>
                            <input type="text" class="form-control" id="qa_ssc_parent_category" readonly
                                value="{{ $qaParentCategory2?->name ?? 'No category selected yet' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="qa_ssc_parent_sub_category">Sub Category</label>
                            <input type="text" class="form-control" id="qa_ssc_parent_sub_category" readonly
                                value="{{ $qaParentSubCategory?->name ?? 'No sub category selected yet' }}">
                        </div>
                    </div>
                    <label class="form-label" for="qa_sub_sub_category_name">Sub Sub Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('qa_sub_sub_category_name') is-invalid @enderror" id="qa_sub_sub_category_name" placeholder="Enter sub sub category name" wire:model="qa_sub_sub_category_name">
                    @error('qa_sub_sub_category_name') <small class="text-danger">{{ $message }}</small>@enderror
                    @error('sub_category_id') <small class="text-danger d-block">{{ $message }}</small>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <x-submit-btn text="Save Sub Sub Category" function="saveQuickSubSubCategory" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qaUnitModal" tabindex="-1" aria-labelledby="qaUnitModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaUnitModalLabel">Add Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="qa_unit_name">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('qa_unit_name') is-invalid @enderror" id="qa_unit_name" placeholder="e.g. Metric Tonne" wire:model="qa_unit_name">
                        @error('qa_unit_name') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="qa_unit_short_name">Short Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('qa_unit_short_name') is-invalid @enderror" id="qa_unit_short_name" placeholder="e.g. MT" wire:model="qa_unit_short_name">
                        @error('qa_unit_short_name') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <x-submit-btn text="Save Unit" function="saveQuickUnit" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qaAttributeModal" tabindex="-1" aria-labelledby="qaAttributeModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaAttributeModalLabel">Add Attribute</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" for="qa_attribute_name">Attribute Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('qa_attribute_name') is-invalid @enderror" id="qa_attribute_name" placeholder="Enter attribute name" wire:model="qa_attribute_name">
                    @error('qa_attribute_name') <small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <x-submit-btn text="Save Attribute" function="saveQuickAttribute" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qaPackagingTypeModal" tabindex="-1" aria-labelledby="qaPackagingTypeModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qaPackagingTypeModalLabel">Add Packaging Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label" for="qa_packaging_type_name">Packaging Type Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('qa_packaging_type_name') is-invalid @enderror" id="qa_packaging_type_name" placeholder="Enter packaging type name" wire:model="qa_packaging_type_name">
                    @error('qa_packaging_type_name') <small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <x-submit-btn text="Save Packaging Type" function="saveQuickPackagingType" />
                </div>
            </div>
        </div>
    </div>
</div>
