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
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>

                        <div class="col-6 text-end">
                            <a href="{{route('seller.commodity-product.create')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div wire:ignore>
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select select2 @error('category_id') is-invalid @enderror" id="category_id" wire:model="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($category_list as $category_data)
                                        <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <div>
                                <label for="sub_category" class="form-label">Sub Category</label>
                                <select class="form-select select2 sub_category @error('sub_category_id') is-invalid @enderror" id="sub_category" wire:model="sub_category_id">
                                    <option>Select Sub Category</option>
                                    @foreach ($sub_category_list as $sub_category_data)
                                        <option value="{{$sub_category_data->id}}">{{$sub_category_data->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="sub_sub_category" class="form-label">Sub Sub Category</label>
                            <select class="form-select select2 @error('sub_sub_category_id') is-invalid @enderror" id="sub_sub_category" wire:model="sub_sub_category_id">
                                <option>Select Sub Sub Category</option>
                                @foreach ($sub_sub_category_list as $sub_sub_category_data)
                                    <option value="{{$sub_sub_category_data->id}}">{{$sub_sub_category_data->name}}</option>
                                @endforeach
                            </select>
                            @error('sub_sub_category_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-3 mb-3" wire:ignore>
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                <option>Select Brand</option>
                                @foreach ($brand_list as $brand_data)
                                    <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-12 mb-3" wire:ignore>
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" wire:model="product_id">
                                <option data-image="{{asset('seller_css/no-photo.png')}}" value="">Select Product</option>
                                @foreach ($product_list as $product_data)
                                    <option value="{{ $product_data->id }}" data-image="{{ imageUrl($product_data->thumbnail) }}">{{ $product_data->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="base_price" class="form-label">Base Price</label>
                            <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" placeholder="Enter base price" min="0" step="0.01" wire:model="base_price">
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
                            <label for="quality_inspection_charge" class="form-label">Quality Inspection Charge</label>
                            <input type="number" class="form-control @error('quality_inspection_charge') is-invalid @enderror" id="quality_inspection_charge" min="0" step="0.01" placeholder="Enter quality inspection charge" wire:model="quality_inspection_charge">
                            @error('quality_inspection_charge') <small class="text-danger">{{ $message }}</small>@enderror
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

                        @if (count($quality_arr) > 0)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Select Quality</label><br>
                                @foreach ($quality_arr as $quality_key => $quality_data)
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="check_quality_{{$loop->iteration}}" value="{{$quality_key}}" wire:model.live="quality">
                                        <label class="form-check-label" for="check_quality_{{$loop->iteration}}">
                                            {{ $quality_key }} - ₹ {{ $quality_data }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                @foreach ($quality as $quality_name)
                                    <div class="col-md-4 mb-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">{{$quality_name}}</span>
                                            <input type="number" class="form-control " placeholder="Enter {{$quality_name}} Price" wire:model="quality_price.{{$loop->iteration}}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (count($variant_arr) > 0)
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Variant</label>
                                </div>
                                <div class="col-md-6">
                                    {{$specification_notes}}
                                </div>
                                @foreach ($variant_arr as $variant_key => $variant)
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control @error('size') is-invalid @enderror" wire:model="size.{{$variant_key}}" value="{{$variant['size']}}" hidden>
                                            <span class="input-group-text">{{$variant['size']}}</span>
                                            <input type="number" class="form-control @error('size_price') is-invalid @enderror" placeholder="Enter {{$variant['size']}} Price" wire:model="size_price.{{$variant_key}}" >
                                        </div>
                                        @error('size_price') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>

                                    {{-- <div class="col-md-6 mb-3">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control @error('dimension') is-invalid @enderror" wire:model="dimension.{{$variant_key}}" hidden>
                                            <span class="input-group-text">{{$variant['dimension']}}</span>
                                            <input type="number" class="form-control @error('dimension_price') is-invalid @enderror" placeholder="Enter {{$variant['dimension']}} Price" wire:model="dimension_price.{{$variant_key}}">
                                        </div>
                                        @error('dimension_price') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div> --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Specification</span>
                                            <textarea class="form-control @error('specification.{{$variant_key}}') is-invalid @enderror" wire:model="specification.{{$variant_key}}" rows="1"></textarea>
                                        </div>
                                        @error('specification.'.$variant_key) <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>

                                    {{-- <div class="col-md-6 mb-3">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control @error('dimension') is-invalid @enderror" wire:model="dimension.{{$variant_key}}" hidden>
                                            <span class="input-group-text">Specification</span>
                                            <input type="number" class="form-control @error('dimension_price') is-invalid @enderror" placeholder="Enter {{$variant['dimension']}} Price" wire:model="dimension_price.{{$variant_key}}">
                                        </div>
                                        @error('dimension_price') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div> --}}
                                @endforeach
                            </div>
                        @endif

                        @if (count($charge_arr) > 0)
                            <div class="row">
                                <label for="" class="form-label">Charge</label>
                                @foreach ($charge_arr as $charge_key => $charge)
                                    <div class="col-md-4 mb-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">{{$charge['charge_name']}}</span>
                                            <input type="number" class="form-control " placeholder="Enter {{$charge['charge_name']}} Price" wire:model="charge_price.{{$charge_key+1}}">
                                            <span class="input-group-text fw-bold">{{$charge['operator']}}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (count($packaging_type_arr) > 0)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Select Packaging Type</label><br>
                                @foreach ($packaging_type_arr as $packaging_type_key => $packaging_type_data)
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="check_packaging_type_{{$loop->iteration}}" value="{{$packaging_type_data['id']}}" wire:model.live="packaging_type_id">
                                        <label class="form-check-label" for="check_packaging_type_{{$loop->iteration}}">
                                            {{ $packaging_type_data['name'] }} - ₹ {{ $packaging_type_data['price'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                @foreach ($packaging_type_id as $selected_packaging)
                                    <div class="col-md-4 mb-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">{{getPackagingType($selected_packaging)->name}}</span>
                                            <input type="number" class="form-control " placeholder="Enter {{getPackagingType($selected_packaging)->name}} Price" wire:model="packaging_type_price.{{$selected_packaging}}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Loading Address</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Pincode</span>
                                    <input type="text" class="form-control @error('pincode.0') is-invalid @enderror" placeholder="Enter Pincode" wire:model="pincode.0">
                                </div>
                                @error('pincode.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Address</span>
                                    <textarea class="form-control @error('address.0') is-invalid @enderror" wire:model="address.0" placeholder="Enter Loading Address" rows="1"></textarea>
                                </div>
                                @error('address.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-1 mb-3">
                                <button type="button" class="btn btn-inverse-success" wire:click="addAddressField({{$loading_address_count}})">Add</button>
                            </div>

                            @foreach ($loading_address_inputs as $loading_address_input_key => $loading_address_input)
                                <div class="col-md-5 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Pincode</span>
                                        <input type="number" class="form-control @error('pincode.'.$loading_address_input) is-invalid @enderror" placeholder="Enter Pincode" wire:model="pincode.{{$loading_address_input}}">
                                    </div>
                                    @error('pincode.'.$loading_address_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Address</span>
                                        <textarea class="form-control @error('address.'.$loading_address_input) is-invalid @enderror" wire:model="address.{{$loading_address_input}}" placeholder="Enter Loading Address" rows="1"></textarea>
                                    </div>
                                    @error('address.'.$loading_address_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <button type="button" class="btn btn-inverse-danger" wire:click="removeAddressField({{$loading_address_input_key}})">Remove</button>
                                </div>
                            @endforeach
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
            $(document).ready(function() {
                $('#product_id').select2({
                    templateResult: formatProduct,
                    templateSelection: formatProductSelection
                });

                // Custom formatting for displaying product images in the dropdown
                function formatProduct(product) {
                    if (!product.id) {
                        return product.text;
                    }

                    var $product = $(
                        '<span><img style="width:30px;height:30px;border-radius:50%;margin-right:10px;" src="' + $(product.element).data('image') + '" class="img-flag" /> ' + product.text + '</span>'
                    );
                    return $product;
                }

                function formatProductSelection(product) {
                    return product.text;
                }

                $('#product_id').on('change', function() {
                    @this.set('product_id', $(this).val());
                    @this.call('setProductData');
                });

                $('#category_id').on('change', function (e) {
                    @this.set('category_id', $(this).val());
                    @this.setSubCategoryList();
                });

                $('#sub_category').on('change', function (e) {
                    @this.setSubSubCategoryList();
                });
            });
        </script>
    @endpush
</div>
