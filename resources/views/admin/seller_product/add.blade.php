<div>
    @section('title', config('app.name') . ' | '.$page_title)
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
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('product_id') is-invalid @enderror" id="product_id" wire:model="product_id">
                                        <option value="">Select Product</option>
                                        @foreach ($product_list as $product_data)
                                            <option value="{{ $product_data->id }}">{{ $product_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('product_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id">
                                        <option value="">Select Brand</option>
                                        @foreach ($brand_list as $brand_data)
                                            <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('brand_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('state') is-invalid @enderror" id="state" wire:model="state">
                                        <option value="">Select State</option>
                                        @foreach ($state_list as $state_data)
                                            <option value="{{ $state_data }}">{{ $state_data }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('state') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('city') is-invalid @enderror" id="city" wire:model="city">
                                        <option value="">Select City</option>
                                        @foreach ($city_list as $city_data)
                                            <option value="{{ $city_data }}">{{ $city_data }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('city') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <h5>My Package Type</h5>
                            <hr>
                            @foreach ($packaging_type_name as $packaging_types)
                                <div class="col-md-4 mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">{{$packaging_types}}</span>
                                        <input type="number" class="form-control " placeholder="Enter {{$packaging_types}} Price" wire:model="packaging_type_price.{{$loop->iteration}}">
                                    </div>
                                </div>
                            @endforeach

                            <p class="h5">Add Loading Address <button class="btn btn-primary btn-xs float-end mb-1" type="button">Add</button></p>
                            <hr>
                            <div class="col-md-4 mb-3">
                                <label for="pin_code" class="form-label">Pincode</label>
                                <input type="number" class="form-control @error('pin_code') is-invalid @enderror" id="pin_code" placeholder="Enter product pin code" wire:model="loading_address.pin_code">
                                @error('pin_code') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address_line_one" class="form-label">Address Line 1 (Plot No/House No/Street)</label>
                                <input type="text" class="form-control @error('address_line_one') is-invalid @enderror" id="address_line_one" placeholder="Enter product pin code" wire:model="loading_address.address_line_one">
                                @error('address_line_one') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address_line_two" class="form-label">Address Line 2 (Area)</label>
                                <input type="text" class="form-control @error('address_line_two') is-invalid @enderror" id="address_line_two" placeholder="Enter product pin code" wire:model="loading_address.address_line_two">
                                @error('address_line_two') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="loading_position" class="form-label">Loading Position (In Days)</label>
                                <input type="number" class="form-control @error('loading_position') is-invalid @enderror" id="loading_position" placeholder="Enter product pin code" wire:model="loading_address.loading_position">
                                @error('loading_position') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <x-submit-btn text="Save" />
                                </div>
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

                $('#category_id').on('change', function (e) {
                    @this.setProductList();
                });

                $('#product_id').on('change', function (e) {
                    @this.setBrandList();
                });

                $('#brand_id').on('change', function (e) {
                    @this.setStateList();
                });

                $('#state').on('change', function (e) {
                    @this.setCityList();
                });
            });
        </script>
    @endpush
</div>
