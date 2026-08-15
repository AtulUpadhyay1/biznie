<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>

                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.address.index') }}" wire:navigate>
                            <i class="bi bi-x-lg"></i>Cancel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pincode">Pincode <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('pincode') is-invalid @enderror"
                                    id="pincode" wire:model="pincode" placeholder="Enter pincode">
                                @error('pincode')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" wire:model="city" placeholder="Enter city">
                                @error('city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="state">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                    id="state" wire:model="state" placeholder="Enter state">
                                @error('state')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="country">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    id="country" wire:model="country" placeholder="Enter country">
                                @error('country')
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
