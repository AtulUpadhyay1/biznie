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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Add Fund</h5>
                                </div>
                                <form wire:submit.prevent="save()">
                                    <div class="card-body">
                                        @foreach ($data->form_data as $key => $form_value)
                                            <div class="mb-3">
                                                <label class="form-label">{{ $form_value['label'] }} @if($form_value['required'])<span class="text-danger">*</span>@endif</label>
                                                @if(is_array($form_value) && isset($form_value['type']) && $form_value['type'] == 'file' && isset($form_value['value']))
                                                    <div>
                                                        <a href="{{imageUrl($form_value['value'])}}" target="_blank">View Document</a>
                                                    </div>
                                                @else
                                                    <input type="text" class="form-control" value="{{ is_array($form_value) && isset($form_value['value']) ? $form_value['value'] : $form_value }}" readonly>
                                                @endif
                                            </div>
                                        @endforeach

                                        <div class="mb-3">
                                            <label class="form-label" for="reference_number">Reference Number</label>
                                            <input type="text" class="form-control  @error('reference_number') is-invalid @enderror" id="reference_number" wire:model="reference_number" placeholder="Enter Reference Number">
                                            @error('reference_number')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
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

                                        <div class="mb-3">
                                            <label class="form-label" for="amount">Add Credit Balance</label>
                                            <input type="number" id="amount" class="form-control @error('amount') is-invalid @enderror" wire:model="amount" placeholder="Enter Credit Balance" {{ $status != 'Approved' ? 'disabled' : '' }}>
                                            @error('amount')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="credit_days">Credit Days</label>
                                            <input type="number" id="credit_days" class="form-control @error('credit_days') is-invalid @enderror" wire:model="credit_days" placeholder="Enter Credit Balance" {{ $status != 'Approved' ? 'disabled' : '' }}>
                                            @error('credit_days')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="notes">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" cols="30" rows="1" placeholder="Enter notes" wire:model="notes"></textarea>
                                            @error('notes') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" cols="30" rows="1" placeholder="Enter description" wire:model="description"></textarea>
                                            @error('description') <small class="text-danger">{{ $message }}</small>@enderror
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                <x-submit-btn text="Save" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>User Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><b>Company Name : </b> {{ $data->getUser?->getUserDetail?->company_name }}</p>
                                    <p><b>Name : </b> {{ $data->getUser->name }} ({{ $data->getUser->type }})</p>
                                    <p><b>Phone : </b> {{ $data->getUser->phone }}</p>
                                    <div class="d-flex mt-2">
                                        <div class="me-1">
                                            <span class="badge border border-primary text-primary p-3">
                                                <h6>Credit Balance</h6>
                                                <h3> ₹ {{ formatIndianNumber($data->getUser->credit_balance) }} </h3>
                                            </span>
                                        </div>

                                        <div class="ms-1">
                                            <span class="badge border border-success text-success p-3">
                                                <h6>Cash Balance</h6>
                                                <h3> ₹ {{ formatIndianNumber($data->getUser->cash_balance) }} </h3>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
