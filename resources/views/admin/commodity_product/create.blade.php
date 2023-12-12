<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <style>
        .select2-container--default .select2-selection--single{
            height: 42px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add new Product</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('seller.product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
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
                        <textarea class="form-control @error('details') is-invalid @enderror" id="description" cols="30" rows="5" placeholder="Enter product description" wire:model="details"></textarea>
                        @error('details') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>General setup</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4 mb-3" wire:ignore>
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select select2 @error('category_id') is-invalid @enderror" id="category_id" wire:model="category_id">
                                    <option>Select Category</option>
                                    @foreach ($category_list as $category_data)
                                        <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3" wire:ignore>
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                    <option>Select Brand</option>
                                    @foreach ($brand_list as $brand_data)
                                        <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3" wire:ignore>
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select select2 @error('unit_id') is-invalid @enderror" id="unit_id" wire:model="unit_id">
                                    <option>Select Unit</option>
                                    @foreach ($unit_list as $unit_data)
                                        <option value="{{ $unit_data->id }}">{{ $unit_data->short_name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Pricing & others</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="base_price" class="form-label">Base Price</label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" min="0" step="0.01" placeholder="Enter purchase price" wire:model="base_price">
                                @error('base_price') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="loading_charge" class="form-label">Loading Charge</label>
                                <input type="number" class="form-control @error('loading_charge') is-invalid @enderror" id="loading_charge" min="0" step="0.01" placeholder="Enter loading charge" wire:model="loading_charge">
                                @error('loading_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="insurance_charge" class="form-label">Insurance Charge</label>
                                <input type="number" class="form-control @error('insurance_charge') is-invalid @enderror" id="insurance_charge" min="0" step="0.01" placeholder="Enter insurance charge" wire:model="insurance_charge">
                                @error('insurance_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="quantity_charge" class="form-label">Quantity Charge</label>
                                <input type="number" class="form-control @error('quantity_charge') is-invalid @enderror" id="quantity_charge" min="0" step="0.01" placeholder="Enter quantity charge" wire:model="quantity_charge">
                                @error('quantity_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-inverse-primary btn-xs" wire:click="addOtherChargesField({{$i}})">Add Other Charges</button>
                            </div>

                            @foreach($inputs as $key => $value)

                                <div class="col-md-4 mb-3">
                                    <label for="charge_name_{{$value}}" class="form-label">Charge Name</label>
                                    <input type="test" class="form-control @error('charge_name'.$value) is-invalid @enderror" id="charge_name_{{$value}}" placeholder="Enter charge name" wire:model="charge_name.{{$value}}">
                                    @error('charge_name'.$value) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="charge_price_{{$value}}" class="form-label">Charge Price</label>
                                    <input type="number" class="form-control @error('charge_price'.$value) is-invalid @enderror" id="charge_price_{{$value}}" min="0" step="0.01" placeholder="Enter charge price" wire:model="charge_price.{{$value}}">
                                    @error('charge_price'.$value) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="operator_{{$value}}" class="form-label">Operator</label>
                                    <select class="form-select @error('operator'.$value) is-invalid @enderror" id="operator_{{$value}}" wire:model="operator.{{$value}}">
                                        <option value="+">+</option>
                                        <option value="-">-</option>
                                        <option value="*">*</option>
                                        <option value="/">/</option>
                                        <option value="%">%</option>
                                    </select>
                                    @error('operator'.$value) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-inverse-danger btn-xs mt-4 btn-icon" wire:click="removeOtherChargesField({{$key}})">
                                        <i class="bi bi-x-circle"></i>
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
                            <div class="col-md-12 mb-3" wire:ignore>
                                <label for="attribute" class="form-label">Select Attributes</label>
                                <select class="form-control select2 @error('attributes') is-invalid @enderror" id="attribute" multiple wire:model="attribute">
                                    @foreach ($attributes_list as $attributes_data)
                                        <option value="{{ $attributes_data->id }}">{{ $attributes_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('attributes') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
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

                $('.color-var-select').select2({
                    templateResult: colorCodeSelect,
                    templateSelection: colorCodeSelect,
                    escapeMarkup: function(m) {
                        return m;
                    }
                });

                function colorCodeSelect(state) {
                    var colorCode = $(state.element).val();
                    if (!colorCode) return state.text;
                    return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state
                        .text;
                }

                // $('#category_id').on('change', function (e) {
                //     @this.setSubCategoryList();
                // });

                // $('#sub_category').on('change', function (e) {
                //     @this.setSubSubCategoryList();
                // });

                $('#color_switcher').on('change', function() {
                    if (!$('#color_switcher').is(':checked')) {
                        $('#colors').prop('disabled', true);
                    } else {
                        $('#colors').prop('disabled', false);
                    }
                });

                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })
            });
        </script>
    @endpush
</div>
