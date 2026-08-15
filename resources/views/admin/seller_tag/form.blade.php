<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Seller Tag</h4>
                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.seller-tag.index') }}" wire:navigate>
                            <i class="bi bi-x-lg"></i>Cancel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{$hidden_id ? 'update()' : 'save()'}}">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter tag name">
                                @error('name') <small class="text-danger">{{ $message }}</small>@enderror
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
