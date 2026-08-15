<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#viewBeltModal">
                            <i class="bi bi-eye"></i>
                            View Belt
                        </button>
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.transporter.index') }}" wire:navigate>
                            <i class="bi bi-arrow-left"></i>Back
                        </a>
                    </div>
                </div>
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
                            <div class="col-md-12 mb-3">
                                <span class="bz-section-label">Unloading Address</span>
                            </div>
                            <div class="col-md-6 mb-3">
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
                                <div class="col-12">
                                    <h5 class="mb-1">{{ $state }}</h5>
                                </div>
                                @php
                                    $main_loop = $loop->iteration;
                                @endphp
                                @foreach ($city_data as $city)
                                    <div class="col-md-2 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="city_{{ strtolower(str_replace(" ","_",$city)) }}" wire:model.live="selected_city" value="{{$city}}">
                                            <label for="city_{{ strtolower(str_replace(" ","_",$city)) }}" class="form-check-label">
                                                {{ $city }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="loading_min_{{ strtolower(str_replace(" ","_",$city)) }}">Min Price</label>
                                        <input type="number" class="form-control @error('min_price.'.strtolower(str_replace(" ","_",$city))) is-invalid @enderror" id="loading_min_{{ strtolower(str_replace(" ","_",$city)) }}" wire:model="min_price.{{ strtolower(str_replace(" ","_",$city)) }}" placeholder="Enter Min Price" @if(!in_array($city, $selected_city)) disabled @endif>
                                        @error('min_price.'.strtolower(str_replace(" ","_",$city))) <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="loading_max_{{ strtolower(str_replace(" ","_",$city)) }}">Max Price</label>
                                        <input type="number" class="form-control @error('max_price.'.strtolower(str_replace(" ","_",$city))) is-invalid @enderror" id="loading_max_{{ strtolower(str_replace(" ","_",$city)) }}" wire:model="max_price.{{ strtolower(str_replace(" ","_",$city)) }}" placeholder="Enter Max Price" @if(!in_array($city, $selected_city)) disabled @endif>
                                        @error('max_price.'.strtolower(str_replace(" ","_",$city))) <small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewBeltModal" tabindex="-1" aria-labelledby="viewBeltModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBeltModalLabel">View Belt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    @forelse ($transporter_address as $address)
                        <div class="card mb-2">
                            <div class="card-header"> <h5> {{$address['loading_address']}} </h5></div>
                            <div class="card-body p-2">
                                <h5>Unloading Address : </h5>
                                <div class="row">
                                    @foreach ($address['unloading_address'] as $unloading_address)
                                        <div class="col-6 mb-2">
                                            <div class="card card-body">
                                                <p>
                                                    <b>State : </b> {{ $unloading_address['state'] }} <br>
                                                    <b>City : </b> {{ $unloading_address['city'] }} <br>
                                                    <b>Price : </b> {{ formatIndianNumber($unloading_address['min_price']) }} - {{ formatIndianNumber($unloading_address['max_price']) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bz-empty">
                            <span class="bz-empty__icon"><i class="bi bi-diagram-2"></i></span>
                            <span class="bz-empty__title">No belts yet</span>
                            <p class="bz-empty__text">No loading/unloading belts have been saved for this transporter.</p>
                        </div>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
