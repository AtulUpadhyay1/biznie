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
                            <a @if($state_price_id) href="{{route('admin.commodity-product.show', $hidden_id)}}" @else href="{{route('admin.commodity-product.index')}}" @endif class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select class="form-select select2 @error('brand_id') is-invalid @enderror" id="brand_id" wire:model="brand_id" {{ $state_price_id ? 'disabled' : '' }}>
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
                                    <label for="state_name" class="form-label">State</label>
                                    <select class="form-select select2 @error('state_name') is-invalid @enderror" id="state_name" wire:model="state_name" {{ $state_price_id ? 'disabled' : '' }}>
                                        <option>Select State</option>
                                        @foreach ($state_list as $state_data)
                                            <option value="{{ $state_data->state }}">{{ $state_data->state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('state_name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="city_name" class="form-label">City</label>
                                    <select class="form-select select2 @error('city_name') is-invalid @enderror" id="city_name" wire:model="city_name" {{ $state_price_id ? 'disabled' : '' }}>
                                        <option>Select City</option>
                                        @foreach ($city_list as $city_data)
                                            <option value="{{ $city_data->city }}">{{ $city_data->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('city_name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($selected_attributes as $attribute)
                                            <th class="text-center">
                                                {{getAttribute($attribute)->name}}
                                                @if($data->unit && $data->unit[getAttribute($attribute)->name])
                                                    ({{getProductUnit($data->unit[getAttribute($attribute)->name])->short_name}})
                                                @endif
                                                {{-- <br> <hr style="margin: 3px; border: 0; border-top: 1px solid; opacity: 1.1;"> --}}
                                                <select class="form-control form-control-sm text-center d-none" wire:model="unit.{{getAttribute($attribute)->name}}">
                                                    <option value="">Select Unit</option>
                                                    @foreach ($unit_list as $unit_data)
                                                        <option value="{{$unit_data->id}}">{{$unit_data->name}} ({{$unit_data->short_name}})</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                        @endforeach
                                        <th class="text-center">Price</th>
                                        {{-- <th class="text-center">Action</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th><span class="badge bg-danger">1</span></th>
                                        @foreach ($selected_attributes as $attribute)
                                            <td>
                                                <input type="text" class="form-control form-control-sm @error('variation.'.getAttribute($attribute)->name.'.0') is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="variation.{{getAttribute($attribute)->name}}.0" readonly style="cursor: no-drop;">
                                                @error('variation.'.getAttribute($attribute)->name.'.0') <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="number" class="form-control form-control-sm @error('variation.Price.0') is-invalid @enderror" placeholder="Enter Price" wire:model="variation.Price.0">
                                            @error('variation.Price.0') <small class="text-danger">{{ $message }}</small>@enderror
                                        </td>
                                        {{-- <td></td> --}}
                                    </tr>
                                    @foreach ($variation_inputs as $variation_key => $variation_input)
                                        <tr>
                                            <th><span class="badge bg-danger">{{ $loop->iteration+1 }}</span></th>
                                            @foreach ($selected_attributes as $attribute)
                                                <td>
                                                    <input type="text" class="form-control form-control-sm @error('variation.'.getAttribute($attribute)->name.'.'.$variation_input) is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="variation.{{getAttribute($attribute)->name}}.{{$variation_input}}" readonly style="cursor: no-drop;">
                                                    @error('variation.'.getAttribute($attribute)->name.'.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                                </td>
                                            @endforeach
                                            <td>
                                                <input type="number" class="form-control form-control-sm @error('variation.Price.'.$variation_input) is-invalid @enderror" placeholder="Enter Price" wire:model="variation.Price.{{$variation_input}}">
                                                @error('variation.Price.'.$variation_input) <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                            {{-- <td>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-inverse-danger btn-sm btn-icon" wire:click="removeVariationField({{$variation_key}})" title="Remove Field" disabled><i class="bi bi-x-circle"></i></button>
                                                </div>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- <div>
                                <button type="button" class="btn btn-inverse-success btn-sm py-1" wire:click="addVariationField({{$variation_count}}, false)" title="Add Field">Add More</button>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </div>
                </form>
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
