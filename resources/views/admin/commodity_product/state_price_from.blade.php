<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a @if($state_price_id) href="{{route('admin.commodity-product.show', $hidden_id)}}" @else href="{{route('admin.commodity-product.index')}}" @endif class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div wire:ignore>
                                    <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
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
                                    <label for="state_name" class="form-label">State <span class="text-danger">*</span></label>
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
                                    <label for="city_name" class="form-label">City <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('city_name') is-invalid @enderror" id="city_name" wire:model="city_name" {{ $state_price_id ? 'disabled' : '' }}>
                                        <option>Select City</option>
                                        @foreach ($city_list as $city_data)
                                            <option value="{{ $city_data->city }}">{{ $city_data->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('city_name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div>
                                    <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('pincode') is-invalid @enderror" id="pincode" wire:model="pincode">
                                        <option>Select Pincode</option>
                                        @foreach ($pincode_list as $pincode_data)
                                            <option value="{{ $pincode_data->pincode }}">{{ $pincode_data->pincode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('pincode') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="address_line_one" class="form-label">Address Line One <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address_line_one') is-invalid @enderror" id="address_line_one" wire:model="address_line_one" placeholder="Address Line One">
                                @error('address_line_one') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="address_line_two" class="form-label">Address Line Two <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_line_two" wire:model="address_line_two" placeholder="Address Line Two">
                                @error('address_line_two') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="load_within" class="form-label">Load Within <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="load_within" wire:model="load_within" placeholder="Load Within">
                                @error('load_within') <small class="text-danger">{{ $message }}</small>@enderror
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
                                                <select class="form-select form-select-sm text-center d-none" wire:model="unit.{{getAttribute($attribute)->name}}">
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
                                    @forelse ($data->getCommodityProductVariation as $variation_key => $variation)
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <label class="form-check-label" for="selection_check_{{ $loop->iteration }}">
                                                        {{ $loop->iteration }}
                                                    </label>
                                                    <input type="checkbox" class="form-check-input" id="selection_check_{{ $loop->iteration }}" value="1" wire:model="uploaded_variation.{{$variation->id}}.is_brand_selling" @if($uploaded_variation[$variation->id]['is_brand_selling']) checked @endif>
                                                </div>
                                            </th>
                                            @foreach ($selected_attributes as $attribute)
                                                <td>
                                                    <input type="text" class="form-control form-control-sm @error('uploaded_variation.'.getAttribute($attribute)->name) is-invalid @enderror" placeholder="Enter {{getAttribute($attribute)->name}}" wire:model="uploaded_variation.{{$variation->id}}.{{getAttribute($attribute)->name}}" readonly style="cursor: no-drop;">
                                                    @error('uploaded_variation.'.getAttribute($attribute)->name) <small class="text-danger">{{ $message }}</small>@enderror
                                                </td>
                                            @endforeach

                                            <td>
                                                <input type="number" class="form-control form-control-sm @error('variation.Price.0') is-invalid @enderror" placeholder="Enter Price" wire:model="uploaded_variation.{{$variation->id}}.Price">
                                                @error('variation.Price.0') <small class="text-danger">{{ $message }}</small>@enderror
                                            </td>
                                        </tr>
                                    @empty
                                        <x-table-no-data />
                                    @endforelse
                                </tbody>
                            </table>
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
