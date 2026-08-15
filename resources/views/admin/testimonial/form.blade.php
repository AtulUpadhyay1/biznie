<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>

                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.testimonial.index') }}" wire:navigate>
                            <i class="bi bi-x-lg"></i>Cancel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">User Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" wire:model="name" placeholder="Enter name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="designation">User Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                    id="designation" wire:model="designation" placeholder="Enter Designation">
                                @error('designation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="message">Message <span class="text-danger">*</span></label>
                                <textarea id="message" class="form-control @error('message') is-invalid @enderror" wire:model.live="message" rows="5" placeholder="Enter message"></textarea>
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="badge bg-success">{{ 300-$characterCount }}</small>
                                </div>
                                @error('message')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="image">
                                    User Photo <span class="text-danger">*</span>
                                    <br>
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" height="150" width="150">
                                    @elseif ($showImage)
                                        <img src="{{ $showImage }}" height="150" width="150">
                                    @else
                                        <img src="{{asset('common/images/upload.png')}}" height="150" width="150">
                                    @endif

                                </label>
                                <input type="file" id="image" wire:model="image" hidden accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                @error('image')
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
