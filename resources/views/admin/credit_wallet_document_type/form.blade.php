<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.credit-wallet-document-type.index') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter credit wallet document type" wire:model="name">
                                @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <h5>Number Of Documents</h5>
                            <div class="col-md-5 mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title.0') is-invalid @enderror" id="title" placeholder="Enter title" wire:model="title.0">
                                @error('title.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea id="description" class="form-control @error('description.0') is-invalid @enderror" rows="1" cols="1" placeholder="Enter description" wire:model="description.0"></textarea>
                                @error('description.0') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-1">
                                <label for="" class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-inverse-primary mt-4" wire:click="addField({{$input_count}})">Add</button>
                            </div>

                            @foreach($inputs as $key => $input)
                                <div class="col-md-5 mb-3">
                                    <label for="title_{{$input}}" class="form-label">Title</label>
                                    <input type="text" class="form-control @error('title.'.$input) is-invalid @enderror" id="title_{{$input}}" placeholder="Enter title" wire:model="title.{{$input}}">
                                    @error('title.'.$input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="description_{{$input}}" class="form-label">Description</label>
                                    <textarea id="description_{{$input}}" class="form-control @error('description.'.$input) is-invalid @enderror" rows="1" cols="1" placeholder="Enter description" wire:model="description.{{$input}}"></textarea>
                                    @error('description.'.$input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <label for="" class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-inverse-danger" wire:click="removeField({{$key}})">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <x-submit-btn text=" Save" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
