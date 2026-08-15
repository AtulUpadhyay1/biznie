<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>

                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.ingot_price.index') }}" wire:navigate>
                            <i class="bi bi-x-lg"></i>Cancel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                <select id="location" class="form-select @error('location') is-invalid @enderror" wire:model="location">
                                    <option value="">Select Location</option>
                                    @foreach ($location_list as $location_data)
                                        <option value="{{ $location_data->location }}">{{ $location_data->location }}</option>
                                    @endforeach
                                </select>
                                @error('location')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" id="price"
                                    class="form-control @error('price') is-invalid @enderror"
                                    wire:model="price" placeholder="Enter ingot price">
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_time" class="form-label">Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="date_time" class="form-control @error('date_time') is-invalid @enderror" wire:model="date_time">
                                @error('date_time')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{ $hidden_id ? 'Update' : 'Save' }}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
