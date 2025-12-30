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
                            <a href="{{route('admin.credit-wallet-request.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="{{ isset($hidden_id) ? 'update()' : 'save()' }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div wire:ignore>
                                    <label for="user_id" class="form-label">Company <span class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id" wire:model="user_id">
                                        <option value="">Select Company</option>
                                        @foreach ($user_list as $user_data)
                                            <option value="{{ $user_data->id }}">
                                                @if($user_data->type == 'seller')
                                                    {{ $user_data->getBusiness?->name }} - {{ $user_data->name }} ( {{ $user_data->phone }} )
                                                @elseif ($user_data->type == 'customer')
                                                    {{ $user_data->getUserDetail?->company_name }} - {{ $user_data->name }} ( {{ $user_data->phone }} )
                                                @else
                                                    {{ $user_data->name }} - {{ $user_data->phone }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('user_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div wire:ignore>
                                    <label for="document_type_id" class="form-label">Credit Wallet Document Type</label>
                                    <select class="form-select select2 @error('document_type_id') is-invalid @enderror" id="document_type_id" wire:model="document_type_id">
                                        <option value="">Select Credit Wallet Document Type</option>
                                        @foreach ($type_list as $type_data)
                                            <option value="{{ $type_data->id }}">{{ $type_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('document_type_id') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="row">
                                @if ($document_type)
                                    @foreach ($forms ?? [] as $index => $form)
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">{{ $form['label'] }} @if($form['required'])<span class="text-danger">*</span>@endif</label>
                                            @if($form['type'] == 'file')
                                                <input type="file" class="form-control" wire:model="form_values.{{ $index }}" @if($form['required']) required @endif>
                                            @else
                                                <input type="{{ $form['type'] }}" class="form-control" placeholder="Enter {{ $form['label'] }}" wire:model="form_values.{{ $index }}" @if($form['required']) required @endif>
                                            @endif
                                            <small class="form-text text-muted">{{ $form['description'] }}</small>
                                            @error('form_values.'.$index) <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="reference_number">Reference Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control  @error('reference_number') is-invalid @enderror" id="reference_number" wire:model="reference_number" placeholder="Enter Reference Number">
                                @error('reference_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-select" id="status" wire:model.live="status">
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="amount">Add Credit Balance</label>
                                <input type="number" id="amount" class="form-control @error('amount') is-invalid @enderror" wire:model="amount" placeholder="Enter Credit Balance" {{ $status != 'Approved' ? 'disabled' : '' }}>
                                @error('amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label" for="credit_days">Credit Days</label>
                                <input type="number" id="credit_days" class="form-control @error('credit_days') is-invalid @enderror" wire:model="credit_days" placeholder="Enter Credit Balance" {{ $status != 'Approved' ? 'disabled' : '' }}>
                                @error('credit_days')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" cols="30" rows="1" placeholder="Enter notes" wire:model="notes"></textarea>
                                @error('notes') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" cols="30" rows="1" placeholder="Enter description" wire:model="description"></textarea>
                                @error('description') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
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
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').on('change', function (e) {
                    let elementName = $(this).attr('id');
                    var data = $(this).select2("val");
                    @this.set(elementName, data);

                    if(elementName == 'document_type_id'){
                        @this.getDocumentType();
                    }
                });
                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })
            });
        </script>
    @endpush
</div>
