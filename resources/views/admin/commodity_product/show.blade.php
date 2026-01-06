<div>
    <style>
        .table-sm>:not(caption)>*>* {
            padding: 0.25rem .55rem;
        }
    </style>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{ route('admin.commodity-product.index') }}"
                                class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i
                                    class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- {{ $data }} --}}
                    <h4>{{ $data->name }}</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Category</th>
                                                <td>{{ $data->getCategory ? $data->getCategory->name : '--' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Sub Category</th>
                                                <td>{{ $data->getSubCategory ? $data->getSubCategory->name : '--' }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>Sub Sub Category</th>
                                                <td>{{ $data->getSubSubCategory ? $data->getSubSubCategory->name : '--' }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>Brand</th>
                                                <td>
                                                    @if (!empty($data->brand_id))
                                                        @foreach ($data->brand_id as $brand_id)
                                                            {{ getBrand($brand_id)->name }}@if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Unit</th>
                                                <td>{{ $data->getUnit ? $data->getUnit->name : '--' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Description</th>
                                                <td>{{ $data->description ?? '--' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Packaging Type</th>
                                                <td>
                                                    @if (!empty($data->packaging_type))
                                                        @foreach ($data->packaging_type as $index => $packaging_type)
                                                            {{ getPackagingType($packaging_type)->name }} –
                                                            ₹ {{ $data->packaging_type_price[$index + 1] ?? 0 }}
                                                            @if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>HSN Code</th>
                                                <td>{{ $data->hsn_code ?? '--' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Pricing & Others</h4>
                                </div>
                                <div class="card-body">
                                    <p><b>Base Price :</b> ₹ {{ $data->base_price }}</p>
                                    <p><b>Loading charge :</b> ₹ {{ $data->loading_charge }}</p>
                                    <p><b>Insurance Charge :</b> ₹ {{ $data->insurance_charge }}</p>
                                    <p><b>Quality Inspection Charge :</b> ₹ {{ $data->quality_charge }}</p>
                                    <p><b>GST (%) :</b> ₹ {{ $data->gst }}</p>
                                    <p><b>TCS (%) :</b> ₹ {{ $data->tcs }}</p>
                                    <p><b>Other Charges :</b>
                                        @foreach ($data->charge_name as $charge_name)
                                            {{ $charge_name }} - ₹ {{ $data->operator[$loop->iteration] }}{{ $data->charge_price[$loop->iteration] }}@if (!$loop->last), @endif
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    @if ($data->quality)
                        <div class="accordion mt-2" id="product_quality">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="product_quality">
                                    <button class="accordion-button collapsed fw-bold fs-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#product_quality_collapse"
                                        aria-expanded="false" aria-controls="product_quality_collapse">
                                        Product Quality
                                    </button>
                                </h2>
                                <div id="product_quality_collapse" class="accordion-collapse collapse"
                                    aria-labelledby="product_quality" data-bs-parent="#product_quality">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Quality</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data->quality as $key => $quality)
                                                        <tr>
                                                            <td>{{ $quality }}</td>
                                                            <td>{{ $data->quality_price[$key] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($data->getCommodityProductVariation)
                        <div class="accordion mt-2" id="product_variation_accordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="product_variation_heading">
                                    <button class="accordion-button collapsed fw-bold fs-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#product_variation_collapse"
                                        aria-expanded="false" aria-controls="product_variation_collapse">
                                        Product Variation
                                    </button>
                                </h2>
                                <div id="product_variation_collapse" class="accordion-collapse collapse"
                                    aria-labelledby="product_variation_heading"
                                    data-bs-parent="#product_variation_accordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        @foreach ($data->attributes as $attributes_id)
                                                            <th>
                                                                {{ getAttribute($attributes_id)->name }}
                                                                @if ($data->unit && isset($data->unit[getAttribute($attributes_id)->name]))
                                                                    ({{ getProductUnit($data->unit[getAttribute($attributes_id)->name])->short_name }})
                                                                @endif
                                                            </th>
                                                        @endforeach
                                                        {{-- <th>Gauge <br> Difference</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data->getCommodityProductVariation as $key => $product_variation)
                                                        <tr>
                                                            <td><span
                                                                    class="badge bg-danger">{{ $loop->iteration }}</span>
                                                            </td>
                                                            @foreach ($product_variation->value as $variation_value)
                                                                <td>{{ $variation_value['value'] }}</td>
                                                            @endforeach
                                                            {{-- <td>{{$variation}}</td> --}}
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($data->getStateVariation->count() > 0)
                        <hr>
                        <h4>State wise product variation price</h4>
                        <hr>

                        <div class="accordion" id="accordion_state_variation">
                            @foreach ($data->getStateVariation as $state_variation)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading_{{ $state_variation->id }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#state_variation_collapse_{{ $state_variation->id }}"
                                            aria-expanded="false"
                                            aria-controls="state_variation_collapse_{{ $state_variation->id }}">
                                            <b>Brand : </b>{{ $state_variation->getBrand->name }} | <b>State :
                                            </b>{{ $state_variation->state }} | <b>City :
                                            </b>{{ $state_variation->city }}
                                        </button>
                                    </h2>
                                    <div id="state_variation_collapse_{{ $state_variation->id }}"
                                        class="accordion-collapse collapse"
                                        aria-labelledby="heading_{{ $state_variation->id }}"
                                        data-bs-parent="#accordion_state_variation">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <b>Pincode</b> : {{ $state_variation->pincode }} |
                                                    <b>Address Line One</b> : {{ $state_variation->address_line_one }}
                                                    |
                                                    <b>Address Line Two</b> : {{ $state_variation->address_line_two }}
                                                    |
                                                    <b>Load Within</b> : {{ $state_variation->load_within }} Days

                                                </div>
                                                <div class="col-4 text-end">
                                                    <b>Action</b> :
                                                    <button type="reset" class="btn btn-light btn-icon btn-xs p-0"
                                                        data-bs-toggle="modal" title="Chart"
                                                        data-bs-target="#chartModal_{{ $state_variation->id }}"
                                                        form="copyFormModal_{{ $state_variation->id }}">
                                                        <i class="bi bi-clipboard-data"></i>
                                                    </button>
                                                    <button type="reset" class="btn btn-secondary btn-icon btn-xs p-0"
                                                        data-bs-toggle="modal" title="Copy"
                                                        data-bs-target="#copyModal_{{ $state_variation->id }}"
                                                        form="copyFormModal_{{ $state_variation->id }}">
                                                        <i class="bi bi-copy"></i>
                                                    </button>
                                                    <a href="{{ route('admin.commodity-product.statePrice', $data->id) }}?state_price_id={{ $state_variation->id }}"
                                                        wire:navigate class="btn btn-primary btn-icon btn-xs"
                                                        title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-icon btn-xs p-0"
                                                        title="Delete"
                                                        wire:click="deleteStatePrice({{ $state_variation->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive mt-1">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        @foreach ($data->attributes as $attributes_id)
                                                            <th>
                                                                {{ getAttribute($attributes_id)->name }}
                                                                @if ($data->unit && $data->unit[getAttribute($attributes_id)->name])
                                                                    ({{ getProductUnit($data->unit[getAttribute($attributes_id)->name])->short_name }})
                                                                @endif
                                                            </th>
                                                        @endforeach
                                                        <th>Gauge <br> Difference</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($state_variation->getStateVariationPrice->where('is_brand_selling', '1') as $key => $variation)
                                                        <tr>
                                                            <td><span
                                                                    class="badge bg-danger">{{ $loop->iteration }}</span>
                                                            </td>
                                                            @foreach ($variation->value as $variation_value)
                                                                <td>{{ $variation_value['value'] }}</td>
                                                            @endforeach
                                                            <td>{{ $variation->price }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="copyModal_{{ $state_variation->id }}" tabindex="-1"
                                    aria-labelledby="copyModalLabel_{{ $state_variation->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" wire:ignore.self>
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="copyModalLabel_{{ $state_variation->id }}">Copy Product
                                                    Variation Price</h5>
                                                <button type="reset" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="btn-close"
                                                    form="copyFormModal_{{ $state_variation->id }}"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="fs-5">
                                                    <b>Brand : </b>{{ $state_variation->getBrand->name }} | <b>State :
                                                    </b>{{ $state_variation->state }} | <b>City :
                                                    </b>{{ $state_variation->city }}
                                                </div>
                                                <hr>
                                                <form id="copyFormModal_{{ $state_variation->id }}"
                                                    wire:submit="copyStatePrice({{ $state_variation->id }})">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div>
                                                                <label for="brand_id_{{ $state_variation->id }}"
                                                                    class="form-label">Brand <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    class="form-select @error('brand_id') is-invalid @enderror"
                                                                    id="brand_id_{{ $state_variation->id }}"
                                                                    wire:model.live="brand_id">
                                                                    <option value="">Select Brand</option>
                                                                    @foreach ($brand_list as $brand_data)
                                                                        <option value="{{ $brand_data->id }}">
                                                                            {{ $brand_data->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @error('brand_id')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div>
                                                                <label for="state_name_{{ $state_variation->id }}"
                                                                    class="form-label">State <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    class="form-select @error('state_name') is-invalid @enderror"
                                                                    id="state_name_{{ $state_variation->id }}"
                                                                    wire:model.live="state_name">
                                                                    <option value="">Select State</option>
                                                                    @foreach ($state_list as $state_data)
                                                                        <option value="{{ $state_data->state }}">
                                                                            {{ $state_data->state }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @error('state_name')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div>
                                                                <label for="city_name_{{ $state_variation->id }}"
                                                                    class="form-label">City <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    class="form-select @error('city_name') is-invalid @enderror"
                                                                    id="city_name_{{ $state_variation->id }}"
                                                                    wire:model.live="city_name">
                                                                    <option value="">Select City</option>
                                                                    @foreach ($city_list as $city_data)
                                                                        <option value="{{ $city_data->city }}">
                                                                            {{ $city_data->city }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @error('city_name')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <div>
                                                                <label for="pincode_{{ $state_variation->id }}"
                                                                    class="form-label">Pincode <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    class="form-select @error('pincode') is-invalid @enderror"
                                                                    id="pincode_{{ $state_variation->id }}"
                                                                    wire:model.live="pincode">
                                                                    <option value="">Select Pincode</option>
                                                                    @foreach ($pincode_list as $pincode_data)
                                                                        <option value="{{ $pincode_data->pincode }}">
                                                                            {{ $pincode_data->pincode }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @error('pincode')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <label for="address_line_one" class="form-label">Address
                                                                Line One <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('address_line_one') is-invalid @enderror"
                                                                id="address_line_one" wire:model="address_line_one"
                                                                placeholder="Address Line One">
                                                            @error('address_line_one')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <label for="address_line_two" class="form-label">Address
                                                                Line Two <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                id="address_line_two" wire:model="address_line_two"
                                                                placeholder="Address Line Two">
                                                            @error('address_line_two')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <label for="load_within" class="form-label">Load Within
                                                                <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control"
                                                                id="load_within" wire:model="load_within"
                                                                placeholder="Load Within">
                                                            @error('load_within')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="reset" class="btn btn-secondary"
                                                    data-bs-dismiss="modal"
                                                    form="copyFormModal_{{ $state_variation->id }}">Close</button>
                                                <button type="submit" class="btn btn-primary"
                                                    form="copyFormModal_{{ $state_variation->id }}">Copy</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="chartModal_{{ $state_variation->id }}" tabindex="-1"
                                    aria-labelledby="chartModalLabel_{{ $state_variation->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" wire:ignore.self>
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="chartModalLabel_{{ $state_variation->id }}">Chart Product
                                                    Variation Price</h5>
                                                <button type="reset" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="btn-close"
                                                    form="chartFormModal_{{ $state_variation->id }}"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="fs-5">
                                                    <b>Brand : </b>{{ $state_variation->getBrand->name }} | <b>State :
                                                    </b>{{ $state_variation->state }} | <b>City :
                                                    </b>{{ $state_variation->city }}
                                                </div>
                                                <hr>
                                                <form id="chartFormModal_{{ $state_variation->id }}"
                                                    wire:submit="chartStatePrice({{ $state_variation->id }})">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="chart_{{ $state_variation->id }}"
                                                                class="form-label">Upload New Chart</label>
                                                            <input type="file"
                                                                class="form-control @error('chart') is-invalid @enderror"
                                                                id="chart_{{ $state_variation->id }}"
                                                                wire:model.live="chart">
                                                            <label for="chart_{{ $state_variation->id }}">
                                                                @if ($chart)
                                                                    <img src="{{ $chart->temporaryUrl() }}"
                                                                        class="img-thumbnail" alt="Upload File"
                                                                        class="mt-2">
                                                                @else
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="{{ asset('common/images/upload.png') }}"
                                                                            class="img-thumbnail" alt="Upload File"
                                                                            class="mt-2" wire:loading.remove>
                                                                        <span class="text-danger h5"
                                                                            wire:loading>Uploading...</span>
                                                                    </div>
                                                                @endif
                                                            </label>
                                                        </div>
                                                        @error('chart')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror

                                                        <div class="row">
                                                            @if ($state_variation->chart)
                                                                @foreach ($state_variation->chart ?? [] as $chart_image)
                                                                    <div class="col-md-4 mb-3">
                                                                        <span
                                                                            wire:click="removeChart({{ $state_variation->id }}, {{ $chart_image }})"
                                                                            class="text-danger cls-btn"><i
                                                                                class="bi bi-x-circle"></i></span>
                                                                        <img src="{{ imageUrl($chart_image) }}"
                                                                            class="img-thumbnail" alt="Upload File"
                                                                            class="mt-2" height="150"
                                                                            width="150">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="reset" class="btn btn-secondary"
                                                    data-bs-dismiss="modal"
                                                    form="chartFormModal_{{ $state_variation->id }}">Close</button>
                                                <button type="submit" class="btn btn-primary"
                                                    form="chartFormModal_{{ $state_variation->id }}">
                                                    <span wire:loading.remove>
                                                        Upload
                                                    </span>
                                                    <span wire:loading wire.loading.attr="disabled">
                                                        Uploading...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- @foreach ($data->getStateVariation as $state_variation)
                            <div class="row">
                                <div class="fs-5 mb-1 col-md-10">
                                    <b>Brand : </b>{{$state_variation->getBrand->name}} | <b>State : </b>{{ $state_variation->state }} | <b>City : </b>{{ $state_variation->city }}
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="reset" class="btn btn-light btn-icon btn-xs p-0" data-bs-toggle="modal" title="Chart" data-bs-target="#chartModal_{{$state_variation->id}}" form="copyFormModal_{{$state_variation->id}}">
                                        <i class="bi bi-clipboard-data"></i>
                                    </button>
                                    <button type="reset" class="btn btn-secondary btn-icon btn-xs p-0" data-bs-toggle="modal" title="Copy" data-bs-target="#copyModal_{{$state_variation->id}}" form="copyFormModal_{{$state_variation->id}}">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                    <a href="{{route('admin.commodity-product.statePrice', $data->id)}}?state_price_id={{ $state_variation->id }}" wire:navigate class="btn btn-primary btn-icon btn-xs" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-icon btn-xs p-0" title="Delete" wire:click="deleteStatePrice({{ $state_variation->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive mt-1">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            @foreach ($data->attributes as $attributes_id)
                                                <th>
                                                    {{ getAttribute($attributes_id)->name }}
                                                    @if ($data->unit && $data->unit[getAttribute($attributes_id)->name])
                                                        ({{getProductUnit($data->unit[getAttribute($attributes_id)->name])->short_name}})
                                                    @endif
                                                </th>
                                            @endforeach
                                            <th>Gauge <br> Difference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($state_variation->getStateVariationPrice as $key => $variation)
                                            <tr>
                                                <td><span class="badge bg-danger">{{$loop->iteration}}</span></td>
                                                @foreach ($variation->value as $variation_value)
                                                    <td>{{ $variation_value['value'] }}</td>
                                                @endforeach
                                                <td>{{  $variation->price }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                        @endforeach --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').on('change', function(e) {
                    let elementName = $(this).attr('id');
                    var data = $(this).select2("val");
                    @this.set(elementName, data);
                });
            });
        </script>
    @endpush
</div>
