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
                            <div class="col-md-12 mb-3 d-flex gap-2">
                                <h5>Documents</h5>
                                <button type="button" wire:click="addField" wire:loading.attr="disabled"
                                    wire:target="addField" class="btn btn-sm btn-inverse-primary">
                                    <span wire:loading.remove wire:target="addField">Add Field</span>
                                    <span wire:loading wire:target="addField">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"
                                            aria-hidden="true"></span>
                                        Adding...
                                    </span>
                                </button>
                            </div>

                            @foreach ($fields as $index => $field)
                                <div class="d-flex align-items-center mb-2 border-bottom p-2">
                                    <div class="me-2 flex-grow-1">
                                        <input type="text" wire:model="fields.{{ $index }}.label"
                                            placeholder="Label" class="form-control form-control @error('fields.' . $index . '.label') is-invalid @enderror"/>
                                        @error('fields.' . $index . '.label')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="me-2 flex-grow-1">
                                        <input type="text" wire:model="fields.{{ $index }}.description"
                                            placeholder="Description" class="form-control form-control @error('fields.' . $index . '.description') is-invalid @enderror"/>
                                        @error('fields.' . $index . '.description')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="me-2 flex-grow-1">
                                        <select wire:model="fields.{{ $index }}.type"
                                            class="form-select form-select @error('fields.' . $index . '.type') is-invalid @enderror">
                                            <option value="text">Text</option>
                                            <option value="number">Number</option>
                                            <option value="file">File</option>
                                        </select>
                                        @error('fields.' . $index . '.type')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="me-2">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                wire:model="fields.{{ $index }}.required"
                                                class="form-check-input @error('fields.' . $index . '.required') is-invalid @enderror" id="required{{ $index }}"/>
                                            <label class="form-check-label"
                                                for="required{{ $index }}">Required</label>
                                        </div>
                                        @error('fields.' . $index . '.required')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div>
                                        @if($index != 0)
                                            <button type="button" wire:click="removeField({{ $index }})"
                                                wire:loading.attr="disabled"
                                                wire:target="removeField({{ $index }})"
                                                class="btn btn-sm btn-danger" title="Remove">
                                                <span wire:loading.remove
                                                    wire:target="removeField({{ $index }})">X</span>
                                                <span wire:loading wire:target="removeField({{ $index }})">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                </span>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-danger" disabled title="Remove">X</button>
                                        @endif
                                    </div>
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
