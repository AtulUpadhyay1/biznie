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
                        <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter product name" wire:model="name">
                                @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label class="form-label" for="packaging_type">Packaging Type</label>
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
                                    <label class="form-label" for="packaging_type_price_{{$loop->iteration}}">{{$packaging_types}}</label>
                                    <input type="number" class="form-control" id="packaging_type_price_{{$loop->iteration}}" placeholder="Enter {{$packaging_types}} Price" wire:model="packaging_type_price.{{$loop->iteration}}">
                                </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="commission_type" class="form-label">Commission Type <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="commission_type" id="commission_type" wire:model="commission_type" value="exclude">
                                            <label class="form-check-label" for="commission_type">
                                                Exclude
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="commission_type" id="commission_type1" wire:model="commission_type" value="include">
                                            <label class="form-check-label" for="commission_type1">
                                                Include
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="commission_amount" class="form-label">Commission Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('commission_amount') is-invalid @enderror" id="commission_amount" placeholder="Enter product pin code" wire:model="commission_amount">
                                    @error('commission_amount') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>

{{-- Which of the four prices the storefront shows for this listing.
                                 Basic is the raw product price before charges. --}}
                            <div class="col-12 mb-3">
                                <h5 class="bz-section-label mb-2">Price Display</h5>
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_basic_price" wire:model="show_basic_price">
                                        <label class="form-check-label" for="show_basic_price">Basic Price</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_ex_price" wire:model="show_ex_price">
                                        <label class="form-check-label" for="show_ex_price">Ex-Works Price</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_for_price" wire:model="show_for_price">
                                        <label class="form-check-label" for="show_for_price">F.O.R Price</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_fob_price" wire:model="show_fob_price">
                                        <label class="form-check-label" for="show_fob_price">F.O.B Price</label>
                                    </div>
                                </div>
                                <small class="text-muted">Shown in the product page price breakup. Leave all off to show none.</small>
                            </div>

                            <div class="col-12 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="bz-section-label mb-0">Update Loading Address</h5>
                                <button class="btn btn-secondary btn-sm" type="button"><i class="bi bi-plus-lg"></i>Add</button>
                            </div>

                            @foreach ($loading_address as $address)
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="pin_code_{{$loop->index }}" class="form-label">Pincode</label>
                                        <input type="number" class="form-control @error('pin_code') is-invalid @enderror" id="pin_code_{{$loop->index }}" placeholder="Enter product pin code" wire:model="loading_address.{{$loop->index }}.pin_code">
                                        @error('pin_code') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="address_line_one_{{$loop->index }}" class="form-label">Address Line 1 (Plot No/House No/Street)</label>
                                        <input type="text" class="form-control @error('address_line_one') is-invalid @enderror" id="address_line_one_{{$loop->index }}" placeholder="Enter product pin code" wire:model="loading_address.{{$loop->index }}.address_line_one">
                                        @error('address_line_one') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="address_line_two_{{$loop->index }}" class="form-label">Address Line 2 (Area)</label>
                                        <input type="text" class="form-control @error('address_line_two') is-invalid @enderror" id="address_line_two_{{$loop->index }}" placeholder="Enter product pin code" wire:model="loading_address.{{$loop->index }}.address_line_two">
                                        @error('address_line_two') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="loading_position_{{$loop->index }}" class="form-label">Loading Position (In Days)</label>
                                        <input type="number" class="form-control @error('loading_position') is-invalid @enderror" id="loading_position_{{$loop->index }}" placeholder="Enter product pin code" wire:model="loading_address.{{$loop->index }}.loading_position">
                                        @error('loading_position') <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <hr>
                            @endforeach
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

                {{-- <div class="card mt-3">
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
                                            <option value="{{ $category_data->id }}"> {{ $category_data->name }} @if($category_data->attributes) (@foreach ($category_data->attributes as $attributes) {{getAttribute($attributes)->name}}@if(!$loop->last),@endif @endforeach)@endif </option>
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
                                    <label class="form-label" for="attribute">Attribute</label>
                                    <select class="form-select select2 @error('attribute') is-invalid @enderror" id="attribute" wire:model="attribute" data-placeholder="Select Attribute" multiple @if($variation_count > 0) disabled @endif>
                                        @foreach($attribute_list as $attribute_data)
                                            <option value="{{$attribute_data->id}}">{{$attribute_data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('attribute') <small class="text-danger">{{ $message }}</small>@enderror
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
                </div> --}}
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
