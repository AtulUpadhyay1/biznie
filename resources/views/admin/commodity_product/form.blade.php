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
                                <div wire:ignore>
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('category_id') is-invalid @enderror" id="category_id" wire:model="category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($category_list as $category_data)
                                            <option value="{{ $category_data->id }}"> {{ $category_data->name }} @if($category_data->attributes) (@foreach ($category_data->attributes as $attributes) {{getAttribute($attributes)->name}}@if(!$loop->last),@endif @endforeach)@endif </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sub_category" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                <select class="form-select select2 sub_category @error('sub_category') is-invalid @enderror" id="sub_category" wire:model="sub_category_id">
                                    <option>Select Sub Category</option>
                                    @foreach ($sub_category_list as $sub_category_data)
                                        <option value="{{$sub_category_data->id}}">{{$sub_category_data->name}} </option>
                                    @endforeach
                                </select>
                                @error('sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sub_sub_category" class="form-label">Sub Sub Category</label>
                                <select class="form-select select2 @error('sub_sub_category_id') is-invalid @enderror" id="sub_sub_category" wire:model="sub_sub_category_id">
                                    <option>Select Sub Sub Category</option>
                                    @foreach ($sub_sub_category_list as $sub_sub_category_data)
                                        <option value="{{$sub_sub_category_data->id}}">{{$sub_sub_category_data->name}}</option>
                                    @endforeach
                                </select>
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
                                <div wire:ignore>
                                    <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('unit_id') is-invalid @enderror" id="unit_id" wire:model="unit_id">
                                        <option>Select Unit</option>
                                        @foreach ($unit_list as $unit_data)
                                            <option value="{{ $unit_data->id }}">{{ $unit_data->short_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('unit_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label class="form-label" for="attribute">Attribute <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('attribute') is-invalid @enderror" id="attribute" wire:model="attribute" data-placeholder="Select Attribute" multiple @if($variation_count > 0) disabled @endif>
                                        @foreach($attribute_list as $attribute_data)
                                            <option value="{{$attribute_data->id}}">{{$attribute_data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('attribute') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="packaging_type" class="form-label">Packaging Type <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap">
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
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').on('change', function (e) {
                    let elementName = $(this).attr('id');
                    var data = $(this).select2("val");
                    @this.set(elementName, data);
                });
                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })
            });

            $('#category_id').on('change', function (e) {
                @this.setSubCategoryList();
            });

            $('#sub_category').on('change', function (e) {
                @this.setSubSubCategoryList();
            });
        </script>
    @endpush
</div>
