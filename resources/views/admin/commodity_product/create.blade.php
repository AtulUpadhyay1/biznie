<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <style>
        .select2-container--default .select2-selection--single{
            height: 42px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .select2.is-invalid {
            border-color: #dc3545 !important; /* Set the border color to the invalid state color */
        }

    </style>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card card-body">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter product name" wire:model="name">
                        @error('name') <small class="text-danger">{{ $message }}</small>@enderror
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
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>General setup</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select select2 @error('category_id') is-invalid @enderror" id="category_id" wire:model="category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($category_list as $category_data)
                                            <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="sub_category" class="form-label">Sub Category</label>
                                <select class="form-select select2 sub_category @error('sub_category') is-invalid @enderror" id="sub_category" wire:model="sub_category_id">
                                    <option>Select Sub Category</option>
                                    @foreach ($sub_category_list as $sub_category_data)
                                        <option value="{{$sub_category_data->id}}">{{$sub_category_data->name}}</option>
                                    @endforeach
                                </select>
                                @error('sub_category') <small class="text-danger">{{ $message }}</small>@enderror
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

                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                        <option>Select Brand</option>
                                        @foreach ($brand_list as $brand_data)
                                            <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label for="unit_id" class="form-label">Unit</label>
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
                                    <label for="packaging_type" class="form-label">Packaging Type</label>
                                    <select class="form-select select2 @error('packaging_type') is-invalid @enderror" id="packaging_type" wire:model="packaging_type" data-placeholder="Select packaging type" multiple>
                                        @foreach ($packaging_type_list as $packaging_type_data)
                                            <option value="{{ $packaging_type_data->id }}">{{ $packaging_type_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('packaging_type') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            @foreach ($packaging_type_name as $packaging_types)
                                <div class="col-md-4 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">{{$packaging_types}}</span>
                                        <input type="number" class="form-control " placeholder="Enter {{$packaging_types}} Price" wire:model="packaging_type_price.{{$loop->iteration}}">
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Pricing & others</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="base_price" class="form-label">Base Price</label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" min="0" step="0.01" placeholder="Enter purchase price" wire:model="base_price">
                                @error('base_price') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="loading_charge" class="form-label">Loading Charge</label>
                                <input type="number" class="form-control @error('loading_charge') is-invalid @enderror" id="loading_charge" min="0" step="0.01" placeholder="Enter loading charge" wire:model="loading_charge">
                                @error('loading_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="insurance_charge" class="form-label">Insurance Charge</label>
                                <input type="number" class="form-control @error('insurance_charge') is-invalid @enderror" id="insurance_charge" min="0" step="0.01" placeholder="Enter insurance charge" wire:model="insurance_charge">
                                @error('insurance_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="quality_charge" class="form-label">Quality Inspection Charge</label>
                                <input type="number" class="form-control @error('quality_charge') is-invalid @enderror" id="quality_charge" min="0" step="0.01" placeholder="Enter quality charge" wire:model="quality_charge">
                                @error('quality_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gst" class="form-label">GST (%)</label>
                                <input type="number" class="form-control @error('gst') is-invalid @enderror" id="gst" min="0" step="0.01" placeholder="Enter gst charge" wire:model="gst">
                                @error('gst') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tcs" class="form-label">TCS (%)</label>
                                <input type="number" class="form-control @error('tcs') is-invalid @enderror" id="tcs" min="0" step="0.01" placeholder="Enter tcs charge" wire:model="tcs">
                                @error('tcs') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-inverse-primary btn-xs" wire:click="addOtherChargesField({{$charge}})">Add Other Charges</button>
                            </div>

                            @foreach($charge_inputs as $charge_key => $charge_input)

                                <div class="col-md-4 mb-3">
                                    <label for="charge_name_{{$charge_input}}" class="form-label">Charge Name</label>
                                    <input type="test" class="form-control @error('charge_name.'.$charge_input) is-invalid @enderror" id="charge_name_{{$charge_input}}" placeholder="Enter charge name" wire:model="charge_name.{{$charge_input}}">
                                    @error('charge_name.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="charge_price_{{$charge_input}}" class="form-label">Charge Price</label>
                                    <input type="number" class="form-control @error('charge_price.'.$charge_input) is-invalid @enderror" id="charge_price_{{$charge_input}}" min="0" step="0.01" placeholder="Enter charge price" wire:model="charge_price.{{$charge_input}}">
                                    @error('charge_price.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="operator_{{$charge_input}}" class="form-label">Operator</label>
                                    <select class="form-select @error('operator.'.$charge_input) is-invalid @enderror" id="operator_{{$charge_input}}" wire:model="operator.{{$charge_input}}">
                                        <option value="">Select</option>
                                        <option value="+">+</option>
                                        <option value="-">-</option>
                                        <option value="*">*</option>
                                        <option value="/">/</option>
                                        <option value="%">%</option>
                                    </select>
                                    @error('operator.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <label for="" class="form-label">&nbsp;</label>

                                    <button type="button" class="btn btn-inverse-danger" wire:click="removeOtherChargesField({{$charge_key}})">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Product variation setup</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Size</span>
                                    <input type="text" class="form-control @error('size.0') is-invalid @enderror" placeholder="Enter Size" wire:model="size.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('size_price.0') is-invalid @enderror" placeholder="Enter Size Price" wire:model="size_price.0">
                                </div>
                                @error('size.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('size_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            {{--<div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Dimension</span>
                                    <input type="text" class="form-control @error('dimension.0') is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.0">
                                    <span class="input-group-text">Price</span>
                                    <input type="number" class="form-control @error('dimension_price.0') is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.0">
                                </div>
                                @error('dimension.0') <small class="text-danger">{{ $message }}</small>@enderror
                                @error('dimension_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div> --}}

                            <div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Specification</span>
                                    <textarea class="form-control @error('specification.0') is-invalid @enderror" wire:model="specification.0" rows="1"></textarea>
                                </div>
                                @error('specification.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-1 mb-3">
                                <button type="button" class="btn btn-inverse-success" wire:click="addVariationField({{$variation}})">Add</button>
                            </div>

                            @foreach ($variation_inputs as $variation_key => $variation_input)

                                <div class="col-md-5 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Size</span>
                                        <input type="text" class="form-control @error('size.'.$variation_input) is-invalid @enderror" placeholder="Enter Size" wire:model="size.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('size_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Size Price" wire:model="size_price.{{$variation_input}}">
                                    </div>
                                    @error('size.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('size_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Dimension</span>
                                        <input type="text" class="form-control @error('dimension.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension" wire:model="dimension.{{$variation_input}}">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('dimension_price.'.$variation_input) is-invalid @enderror" placeholder="Enter Dimension Price" wire:model="dimension_price.{{$variation_input}}">
                                    </div>
                                    @error('dimension.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('dimension_price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Specification</span>
                                        <textarea class="form-control @error('specification.'.$variation_input) is-invalid @enderror" wire:model="specification.{{$variation_input}}" rows="1"></textarea>
                                    </div>
                                    @error('specification.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-inverse-danger" wire:click="removeVariationField({{$variation_key}})">Remove</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header d-flex">
                        <h5>Product quality setup</h5>
                        <div class="form-check form-switch ms-3">
                            <input type="checkbox" class="form-check-input" id="quality_switch" value="{{$is_quality ? 1 : 0}}" wire:model.live="is_quality">
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if ($is_quality)
                                <div class="col-md-11 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Quality</span>
                                        <input type="text" class="form-control @error('quality.0') is-invalid @enderror" placeholder="Enter Quality" wire:model="quality.0">
                                        <span class="input-group-text">Price</span>
                                        <input type="number" class="form-control @error('quality_price.0') is-invalid @enderror" placeholder="Enter Quality Price" wire:model="quality_price.0">
                                    </div>
                                    @error('quality.0') <small class="text-danger">{{ $message }}</small>@enderror
                                    @error('quality_price.0') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-inverse-success" wire:click="addQualityField({{$quality_field}})">Add</button>
                                </div>

                                @foreach ($quality_inputs as $quality_key => $quality_input)

                                    <div class="col-md-11 mb-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Quality</span>
                                            <input type="text" class="form-control @error('quality.'.$quality_input) is-invalid @enderror" placeholder="Enter Quality" wire:model="quality.{{$quality_input}}">
                                            <span class="input-group-text">Price</span>
                                            <input type="number" class="form-control @error('quality_price.'.$quality_input) is-invalid @enderror" placeholder="Enter Quality Price" wire:model="quality_price.{{$quality_input}}">
                                        </div>
                                        @error('quality.'.$quality_input) <small class="text-danger">{{ $message }}</small>@enderror
                                        @error('quality_price.'.$quality_input) <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <button type="button" class="btn btn-inverse-danger" wire:click="removeQualityField({{$quality_key}})">Remove</button>
                                    </div>

                                @endforeach

                            @endif
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card card-body">
                            <label for="thumbnail">
                                Product Thumbnail
                                <br>
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" height="200" width="200">
                                @else
                                    <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                @endif

                            </label>
                            <input type="file" id="thumbnail" wire:model="thumbnail" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-body">
                            <label for="images">
                                Product Images
                                <br>
                                @if ($images)
                                    <img src="{{ $images->temporaryUrl() }}" height="200" width="200">
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
                                <label for="meta_image">
                                    Meta Image
                                    <br>
                                    @if ($meta_image)
                                        <img src="{{ $meta_image->temporaryUrl() }}" height="200" width="200">
                                    @else
                                        <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                    @endif

                                </label>
                                <input type="file" id="meta_image" wire:model="meta_image" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-end">
                        <x-submit-btn text=" Save" />
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
