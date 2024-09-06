<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.vehicle.index') }}" wire:navigate>
                                <i class="bi bi-arrow-left btn-icon-prepend"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ isset($hidden_id) ? 'update()' : 'save()' }}">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Enter Name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" wire:model="type" placeholder="Enter Type">
                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="capacity">Capacity <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('capacity') is-invalid @enderror" id="capacity" wire:model="capacity" placeholder="Enter Capacity">
                                @error('capacity')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="photo">Photo <span class="text-danger">*</span> <br>
                                    @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" height="200" width="200">
                                    @elseif (isset($show_photo))
                                        <img src="{{imageUrl($show_photo)}}" height="200" width="200">
                                    @else
                                        <img src="{{asset('common/images/upload.png')}}" height="200" width="200">
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" wire:model="photo" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                @error('photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{ isset($hidden_id) ? 'Update' : 'Save' }}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
