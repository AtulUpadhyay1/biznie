<div>
    @section('title', config('app.name') . ' | '.$page_title)
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
                <div class="card-body">
                    {{-- {{ $data }} --}}
                    <h4>{{ $data->name }}</h4>
                    <hr>
                    <p><b>Category :</b> {{ $data->getCategory ? $data->getCategory->name : '--' }}</p>

                    <p><b>Sub Category :</b> {{ $data->getSubCategory ? $data->getSubCategory->name : '--' }}</p>

                    <p><b>Sub sub Category :</b> {{ $data->getSubSubCategory ? $data->getSubSubCategory->name : '--' }}</p>

                    <p>
                        <b>Brand :</b>
                        @foreach ($data->brand_id as $brand_id)
                            {{ getBrand($brand_id)->name }}@if(!$loop->last), @endif
                        @endforeach
                    </p>

                    <p><b>Unit :</b> {{ $data->getUnit ? $data->getUnit->name : '--' }}</p>

                    <p><b>Description :</b> {{ $data->description }}</p>

                    <p><b>PackagingType :</b>
                        @foreach ($data->packaging_type as $packaging_type)
                            {{ getPackagingType($packaging_type)->name}} - ₹ {{ $data->packaging_type_price ? $data->packaging_type_price[$loop->iteration] : 0 }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                    <hr>
                    <h4>Pricing & Others</h4>
                    <hr>
                    <p><b>Base Price :</b> ₹ {{ $data->base_price }}</p>
                    <p><b>Loading charge :</b> ₹ {{ $data->loading_charge }}</p>
                    <p><b>Insurance Charge :</b> ₹ {{ $data->insurance_charge }}</p>
                    <p><b>Quality Inspection Charge :</b> ₹ {{ $data->quality_charge }}</p>
                    <p><b>GST (%) :</b> ₹ {{ $data->gst }}</p>
                    <p><b>TCS (%) :</b> ₹ {{ $data->tcs }}</p>
                    <p><b>Other Charges :</b>
                        @foreach ($data->charge_name as $charge_name)
                            {{ $charge_name }} - ₹ {{ $data->operator[$loop->iteration] }}{{ $data->charge_price[$loop->iteration] }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                    @if ($data->quality)
                        <hr>
                        <h4>Product Quality</h4>
                        <hr>
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
                    @endif

                    @if ($data->variation)
                        <hr>
                        <h4>Product Variation</h4>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($data->attributes as $attributes_id)
                                            <th>
                                                {{ getAttribute($attributes_id)->name }}
                                                @if($data->unit && $data->unit[getAttribute($attributes_id)->name])
                                                    ({{getProductUnit($data->unit[getAttribute($attributes_id)->name])->short_name}})
                                                @endif
                                            </th>
                                        @endforeach
                                        {{-- <th>Gauge <br> Difference</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->variation['Price'] as $key => $variation)
                                        <tr>
                                            <td><span class="badge bg-danger">{{$loop->iteration}}</span></td>
                                            @foreach ($data->attributes as $attributes_id)
                                                <td>{{ $data->variation[getAttribute($attributes_id)->name][$key] }}</td>
                                            @endforeach
                                            {{-- <td>{{$variation}}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($data->getStatePrice->count() > 0)
                        <hr>
                        <h4>State wise product variation price</h4>
                        <hr>
                        @foreach ($data->getStatePrice as $state_price)

                            <div class="row">
                                <div class="fs-5 mb-1 col-md-10">
                                    <b>Brand : </b>{{$state_price->getBrand->name}} | <b>State : </b>{{ $state_price->state }} | <b>City : </b>{{ $state_price->city }}
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="reset" class="btn btn-secondary btn-icon btn-xs p-0" data-bs-toggle="modal" data-bs-toggle="tooltip" title="Copy" data-bs-target="#copyModal_{{$state_price->id}}" form="copyFormModal_{{$state_price->id}}">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                    <a href="{{route('admin.commodity-product.statePrice', $data->id)}}?state_price_id={{ $state_price->id }}" wire:navigate class="btn btn-primary btn-icon btn-xs" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-icon btn-xs p-0" title="Delete" wire:click="deleteStatePrice({{ $state_price->id }})">
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
                                                    @if($data->unit && $data->unit[getAttribute($attributes_id)->name])
                                                        ({{getProductUnit($data->unit[getAttribute($attributes_id)->name])->short_name}})
                                                    @endif
                                                </th>
                                            @endforeach
                                            <th>Gauge <br> Difference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->variation['Price'] as $key => $variation)
                                            <tr>
                                                <td><span class="badge bg-danger">{{$loop->iteration}}</span></td>
                                                @foreach ($data->attributes as $attributes_id)
                                                    <td>{{ $data->variation[getAttribute($attributes_id)->name][$key] }}</td>
                                                @endforeach
                                                <td>{{ $state_price->price[$loop->index] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <hr>

                            <div class="modal fade" id="copyModal_{{$state_price->id}}" tabindex="-1" aria-labelledby="copyModalLabel_{{$state_price->id}}" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="copyModalLabel_{{$state_price->id}}">Copy Product Variation Price</h5>
                                            <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close" form="copyFormModal_{{$state_price->id}}"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="fs-5">
                                                <b>Brand : </b>{{$state_price->getBrand->name}} | <b>State : </b>{{ $state_price->state }} | <b>City : </b>{{ $state_price->city }}
                                            </div>
                                            <hr>
                                            <form id="copyFormModal_{{$state_price->id}}" wire:submit="copyStatePrice({{$state_price->id}})">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <div>
                                                            <label for="brand_id_{{$state_price->id}}" class="form-label">Brand</label>
                                                            <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id_{{$state_price->id}}" wire:model.live="brand_id">
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
                                                            <label for="state_name_{{$state_price->id}}" class="form-label">State</label>
                                                            <select class="form-select @error('state_name') is-invalid @enderror" id="state_name_{{$state_price->id}}" wire:model.live="state_name">
                                                                <option value="">Select State</option>
                                                                @foreach ($state_list as $state_data)
                                                                    <option value="{{ $state_data->state }}">{{ $state_data->state }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @error('state_name') <small class="text-danger">{{ $message }}</small>@enderror
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div>
                                                            <label for="city_name_{{$state_price->id}}" class="form-label">City</label>
                                                            <select class="form-select @error('city_name') is-invalid @enderror" id="city_name_{{$state_price->id}}" wire:model.live="city_name">
                                                                <option value="">Select City</option>
                                                                @foreach ($city_list as $city_data)
                                                                    <option value="{{ $city_data->city }}">{{ $city_data->city }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @error('city_name') <small class="text-danger">{{ $message }}</small>@enderror
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal" form="copyFormModal_{{$state_price->id}}">Close</button>
                                            <button type="submit" class="btn btn-primary" form="copyFormModal_{{$state_price->id}}">Copy</button>
                                        </div>
                                    </div>
                                </div>
                              </div>
                        @endforeach
                    @endif
                </div>
            </div>
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
            });
        </script>
    @endpush
</div>
