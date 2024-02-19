<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <style>
        .select2-container--default .color-preview {
            height: 12px;
            width: 12px;
            display: inline-block;
            margin-right: 5px;
            margin-left: 3px;
            margin-top: 2px;
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
                                <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" wire:model="unit_id">
                                    <option>Select Unit</option>
                                    @foreach ($unit_list as $unit_data)
                                        <option value="{{ $unit_data->id }}">{{ $unit_data->short_name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="search_tags" class="form-label">Search Tags</label>
                                <input type="text" class="form-control tags @error('search_tags') is-invalid @enderror" id="search_tags" placeholder="Enter search tags" wire:model="search_tags">
                                @error('search_tags') <small class="text-danger">{{ $message }}</small>@enderror
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
                                <label for="purchase_price" class="form-label">Purchase Price</label>
                                <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" min="0" step="0.01" placeholder="Enter purchase price" wire:model="purchase_price">
                                @error('purchase_price') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="unit_price" class="form-label">Unit Price</label>
                                <input type="number" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" min="0" step="0.01" placeholder="Enter unit price" wire:model="unit_price">
                                @error('unit_price') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="minimum_order_qty" class="form-label">Minimum Order Qty</label>
                                <input type="number" class="form-control @error('minimum_order_qty') is-invalid @enderror" id="minimum_order_qty" min="1" step="1" placeholder="Enter minimum order qty" wire:model="minimum_order_qty">
                                @error('minimum_order_qty') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="current_stock" class="form-label">Current Stock Qty</label>
                                <input type="number" class="form-control @error('current_stock') is-invalid @enderror" id="current_stock" min="0" step="1" placeholder="Enter current stock qty" wire:model="current_stock">
                                @error('current_stock') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="discount_type" class="form-label">Discount Type</label>
                                <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" wire:model="discount_type">
                                    <option value="flat">Flat</option>
                                    <option value="percent">Percent</option>
                                </select>
                                @error('discount_type') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="discount" class="form-label">Discount Amount</label>
                                <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount" min="0" step="0.01" placeholder="Enter current stock qty" wire:model="discount">
                                @error('discount') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="tax" class="form-label">Tax Amount(%)</label>
                                <input type="number" class="form-control @error('tax') is-invalid @enderror" id="tax" min="0" step="0.01" placeholder="Enter current stock qty" wire:model="tax">
                                @error('tax') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="tax_model" class="form-label">Tax Calculation</label>
                                <select class="form-select @error('tax_model') is-invalid @enderror" id="tax_model" wire:model="tax_model">
                                    <option value="include">Include with product</option>
                                    <option value="exclude">Exclude with product</option>
                                </select>
                                @error('tax_model') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="shipping_cost" class="form-label">Shipping Cost</label>
                                <input type="number" class="form-control @error('shipping_cost') is-invalid @enderror" id="shipping_cost" min="0" step="1" placeholder="Enter shipping cost" wire:model="shipping_cost">
                                @error('shipping_cost') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Product variation setup</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3" wire:ignore>
                                <label for="colors" class="form-label d-flex">
                                    Select Colors
                                    <div class="form-check form-switch ms-3">
                                        <input type="checkbox" class="form-check-input" id="color_switcher">
                                    </div>
                                </label>
                                <select class="form-control select2 color-var-select @error('colors') is-invalid @enderror" id="colors" disabled wire:model="colors">
                                    <option value="" selected disabled></option>
                                    @foreach ($colors_list as $color_data)
                                        <option value="{{$color_data->code}}">{{ $color_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('colors') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3" wire:ignore>
                                <label for="attribute" class="form-label">Select Attributes</label>
                                <select class="form-control select2 @error('attributes') is-invalid @enderror" id="attribute" multiple wire:model="attribute">
                                    @foreach ($attributes_list as $attributes_data)
                                        <option value="{{ $attributes_data->id }}">{{ $attributes_data->name }}</option>
                                    @endforeach
                                </select>
                                @error('attributes') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            @foreach ($attribute as $attribute_id)
                                <div class="col-md-6 mb-3">
                                    <label for="choice_options" class="form-label">{{ getAttribute($attribute_id)->name }}</label>
                                    <input type="text" class="form-control @error('choice_options.{{$attribute_id}}') is-invalid @enderror" id="choice_options" placeholder="Enter {{ getAttribute($attribute_id)->name }} value" wire:model="choice_options.{{$attribute_id}}">
                                    @error('choice_options.{{$attribute_id}}') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            @endforeach
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

                $('#category_id').on('change', function (e) {
                    @this.setSubCategoryList();
                });

                $('#sub_category').on('change', function (e) {
                    @this.setSubSubCategoryList();
                });

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
