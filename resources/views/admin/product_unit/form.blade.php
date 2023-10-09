<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Add Unit</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.product-unit') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row mb-3">
                            <h5 class="card-heading-h5">Unit Details:</h5>
                            <div class="col-md-6">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" id="name" class="form-control mb-4 mb-md-0 @error('name') is-invalid @enderror" wire:model.defer="name" placeholder="Enter name">
                                @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="unit">Unit</label>
                                <input type="text" id="unit" class="form-control mb-4 mb-md-0 @error('unit') is-invalid @enderror" wire:model.defer="unit" placeholder="Enter unit">
                                @error('unit') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{$hidden_id?'Update':'Save'}}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
