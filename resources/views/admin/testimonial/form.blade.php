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
                                href="{{ route('admin.testimonial.index') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">User Name</label>
                                <input type="text" class="form-control  @error('name') is-invalid @enderror"
                                    id="name" wire:model="name" placeholder="Enter name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="designation">User Designation</label>
                                <input type="text" class="form-control  @error('designation') is-invalid @enderror"
                                    id="designation" wire:model="designation" placeholder="Enter Designation">
                                @error('designation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="message">Message</label>
                                <textarea id="message" class="form-control @error('message') is-invalid @enderror" wire:model.live="message" rows="5" placeholder="Enter message"></textarea>
                                <small class="badge bg-success float-end">{{ 300-$characterCount }}</small>
                                @error('message')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image">
                                    User Photo
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
