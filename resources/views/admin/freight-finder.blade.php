<div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Search Transporter by Location</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="loading_address" class="form-label">Loading Address <span
                            class="text-danger">*</span></label>
                    <select wire:model.live="loading_address" id="loading_address" class="form-select">
                        <option value="">Select Loading Address</option>
                        @foreach ($loading_addresses as $addressOption)
                            <option value="{{ $addressOption }}">{{ $addressOption }}</option>
                        @endforeach
                    </select>
                    @error('loading_address')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                    <select wire:model.live="state" id="state" class="form-select"
                        {{ empty($states) ? 'disabled' : '' }}>
                        <option value="">Select State</option>
                        @foreach ($states as $stateOption)
                            <option value="{{ $stateOption }}">{{ $stateOption }}</option>
                        @endforeach
                    </select>
                    @error('state')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                    <select wire:model="city" id="city" class="form-select" {{ empty($cities) ? 'disabled' : '' }}>
                        <option value="">Select City</option>
                        @foreach ($cities as $cityOption)
                            <option value="{{ $cityOption }}">{{ $cityOption }}</option>
                        @endforeach
                    </select>
                    @error('city')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 d-flex align-items-end">
                    <button type="button" wire:click="search" class="btn btn-primary me-2 btn-sm">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <button type="button" wire:click="clearFilters" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($showResults)
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    Transporter Results
                    @if ($transporters->count() > 0)
                        <span class="badge bg-primary">{{ $transporters->count() }} Found</span>
                    @endif
                </h5>

                @if ($transporters->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transporter Name</th>
                                    <th>Contact Number</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Min Price</th>
                                    <th>Max Price</th>
                                    <th>Loading Address</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transporters as $index => $transporter)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($transporter->getUser)
                                                {{ $transporter->getUser->name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transporter->getUser && $transporter->getUser->phone)
                                                {{ $transporter->getUser->phone }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $transporter->state }}</td>
                                        <td>{{ $transporter->city }}</td>
                                        <td>
                                            <span class="badge bg-success">
                                                ₹ {{ number_format($transporter->min_price) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                ₹ {{ number_format($transporter->max_price) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($transporter->loading_address)
                                                {{ Str::limit($transporter->loading_address, 50) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ dateTimeFormat($transporter->updated_at) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        No transporters found for <strong>{{ $loading_address }} - {{ $city }},
                            {{ $state }}</strong>. Please try a different selection.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
