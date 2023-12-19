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
                            <h4>
                                {{ $page_title }}
                            </h4>
                        </div>

                        <div class="col-6 text-end">
                            <a href="{{route('seller.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3" wire:ignore>
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select select2 @error('category_id') is-invalid @enderror" id="category_id" wire:model="category_id">
                                <option>Select Category</option>
                                @foreach ($category_list as $category_data)
                                    <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3" wire:ignore>
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" wire:model="product_id">
                                <option data-image="{{asset('seller_css/no-photo.png')}}" value="">Select Product</option>
                                @foreach ($product_list as $product_data)
                                    <option value="{{ $product_data->id }}" data-image="{{ imageUrl($product_data->thumbnail) }}">{{ $product_data->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3" wire:ignore>
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                <option>Select Category</option>
                                @foreach ($category_list as $category_data)
                                    <option value="{{ $category_data->id }}">{{ $category_data->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        @if (count($quality_arr) > 0)
                            <div class="col-md-12 mb-3">
                                <label for="" class="form-label">Select Quality</label><br>
                                @foreach ($quality_arr as $quality_key => $quality_data)
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="check_quality_{{$loop->iteration}}" value="{{$quality_key}}" wire:model.live="quality">
                                        <label class="form-check-label" for="check_quality_{{$loop->iteration}}">
                                            {{ $quality_key }} - ₹ {{ $quality_data }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @foreach ($quality as $quality_name)
                                <div class="col-md-4 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">{{$quality_name}}</span>
                                        <input type="number" class="form-control " placeholder="Enter {{$quality_name}} Price" wire:model="quality_price">
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if (count($variant_arr) > 0)
                            <div class="row">
                                <label for="" class="form-label">Variant</label>
                                @foreach ($variant_arr as $variant_key => $variant)
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control @error('size') is-invalid @enderror" wire:model="size.{{$variant_key}}" value="{{$variant['size']}}" hidden>
                                            <span class="input-group-text">{{$variant['size']}}</span>
                                            <input type="number" class="form-control @error('size_price') is-invalid @enderror" placeholder="Enter {{$variant['size']}} Price" wire:model="size_price.{{$variant_key}}" >
                                        </div>
                                        @error('size_price') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control @error('dimension') is-invalid @enderror" wire:model="dimension.{{$variant_key}}" hidden>
                                            <span class="input-group-text">{{$variant['dimension']}}</span>
                                            <input type="number" class="form-control @error('dimension_price') is-invalid @enderror" placeholder="Enter {{$variant['dimension']}} Price" wire:model="dimension_price.{{$variant_key}}">
                                        </div>
                                        @error('dimension_price') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
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
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label for="packaging_type" class="form-label">Packaging Type</label>
                            <input type="text" class="form-control @error('packaging_type') is-invalid @enderror" id="packaging_type" placeholder="Enter packaging type" wire:model="packaging_type">
                            @error('packaging_type') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="uploading_address" class="form-label">Uploading Address</label>
                            <input type="text" class="form-control @error('uploading_address') is-invalid @enderror" id="uploading_address" placeholder="Enter packaging type" wire:model="uploading_address">
                            @error('uploading_address') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" placeholder="Enter state" wire:model="state">
                            @error('state') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" placeholder="Enter city" wire:model="city">
                            @error('city') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="number" class="form-control @error('pincode') is-invalid @enderror" id="pincode" placeholder="Enter pincode" wire:model="pincode">
                            @error('pincode') <small class="text-danger">{{ $message }}</small>@enderror
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

                        <div class="col-md-6 mb-3">
                            <label for="tcs" class="form-label">Select TCS</label>
                            <select class="form-select select2 @error('tcs') is-invalid @enderror" id="tcs" wire:model="tcs">
                                <option value="1%">1%</option>
                            </select>
                            @error('tcs') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gst_type" class="form-label">Select GST type</label>
                            <select class="form-select select2 @error('gst_type') is-invalid @enderror" id="gst_type" wire:model="gst_type">
                                <option value="GST">GST</option>
                                <option value="CGST">CGST</option>
                            </select>
                            @error('gst_type') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </div>
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
            });
        </script>
    @endpush
</div>
