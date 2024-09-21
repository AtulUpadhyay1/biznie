<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.transporter.index') }}" wire:navigate>
                                <i class="bi bi-arrow-left btn-icon-prepend"></i>Back
                            </a>
                            <a class="btn btn-secondary btn-sm btn-icon-text float-end align-items-center me-1" title="Cancel"
                                href="#" wire:navigate>
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form wire:submit.prevent="save()">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="loading_address">Loading Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('loading_address') is-invalid @enderror" id="loading_address" wire:model="loading_address" placeholder="Enter Loading Address">
                                            @error('loading_address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-2">
                                            <label class="form-label" for="unloading_address">Unloading Address <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div wire:ignore>
                                                <label for="state_name" class="form-label">State <span class="text-danger">*</span></label>
                                                <select class="form-select select2 @error('state_name') is-invalid @enderror" id="state_name" wire:model="state_name" multiple>
                                                    @foreach ($state_list as $state_data)
                                                        <option value="{{ $state_data->state }}">{{ $state_data->state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('state_name') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach ($city_list as $state => $city_data)
                                            <h4 class="mb-1"> {{ $state }} </h4>
                                            @php
                                                $main_loop = $loop->iteration;
                                            @endphp
                                            @foreach ($city_data as $city)
                                                <div class="col-md-2 mb-2">
                                                    <p>
                                                        <input type="checkbox" class="form-check-input" id="city_{{ strtolower(str_replace(" ","_",$city)) }}" wire:model.live="selected_city" value="{{$city}}">
                                                        <label for="city_{{ strtolower(str_replace(" ","_",$city)) }}" class="form-label">
                                                            {{ $city }}
                                                        </label>
                                                    </p>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="number" class="form-control @error('min_price.'.strtolower(str_replace(" ","_",$city))) is-invalid @enderror" id="loading_min_{{ strtolower(str_replace(" ","_",$city)) }}" wire:model="min_price.{{ strtolower(str_replace(" ","_",$city)) }}" placeholder="Enter Min Price" @if(!in_array($city, $selected_city)) disabled @endif>
                                                    @error('min_price.'.strtolower(str_replace(" ","_",$city))) <small class="text-danger">{{ $message }}</small>@enderror
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="number" class="form-control @error('max_price.'.strtolower(str_replace(" ","_",$city))) is-invalid @enderror" id="loading_max_{{ strtolower(str_replace(" ","_",$city)) }}" wire:model="max_price.{{ strtolower(str_replace(" ","_",$city)) }}" placeholder="Enter Max Price" @if(!in_array($city, $selected_city)) disabled @endif>
                                                    @error('max_price.'.strtolower(str_replace(" ","_",$city))) <small class="text-danger">{{ $message }}</small>@enderror
                                                </div>
                                            @endforeach
                                            <hr>
                                        @endforeach
                                    </div>
                                    <div class="row mt-3 text-end">
                                        <div class="col-md-12">
                                            <x-submit-btn text="Save" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
