<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.commodity-product.index')}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="base_price" class="form-label">Base Price</label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" min="0" step="0.01" placeholder="Enter purchase price" wire:model="base_price">
                                @error('base_price') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="loading_charge" class="form-label">Loading Charge</label>
                                <input type="number" class="form-control @error('loading_charge') is-invalid @enderror" id="loading_charge" min="0" step="0.01" placeholder="Enter loading charge" wire:model="loading_charge">
                                @error('loading_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="insurance_charge" class="form-label">Insurance Charge</label>
                                <input type="number" class="form-control @error('insurance_charge') is-invalid @enderror" id="insurance_charge" min="0" step="0.01" placeholder="Enter insurance charge" wire:model="insurance_charge">
                                @error('insurance_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="quality_charge" class="form-label">Quality Inspection Charge</label>
                                <input type="number" class="form-control @error('quality_charge') is-invalid @enderror" id="quality_charge" min="0" step="0.01" placeholder="Enter quality charge" wire:model="quality_charge">
                                @error('quality_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gst" class="form-label">GST (%)</label>
                                <input type="number" class="form-control @error('gst') is-invalid @enderror" id="gst" min="0" step="0.01" placeholder="Enter gst charge" wire:model="gst">
                                @error('gst') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tcs" class="form-label">TCS (%)</label>
                                <input type="number" class="form-control @error('tcs') is-invalid @enderror" id="tcs" min="0" step="0.01" placeholder="Enter tcs charge" wire:model="tcs">
                                @error('tcs') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-sm btn-inverse-primary" wire:click="addOtherChargesField({{$charge}})"><i class="bi bi-plus-lg"></i>Add Other Charges</button>
                            </div>
                            @foreach($charge_inputs as $charge_key => $charge_input)

                                <div class="col-md-4 mb-3">
                                    <label for="charge_name_{{$charge_input}}" class="form-label">Charge Name</label>
                                    <input type="text" class="form-control @error('charge_name.'.$charge_input) is-invalid @enderror" id="charge_name_{{$charge_input}}" placeholder="Enter charge name" wire:model="charge_name.{{$charge_input}}">
                                    @error('charge_name.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="charge_price_{{$charge_input}}" class="form-label">Charge Price</label>
                                    <input type="number" class="form-control @error('charge_price.'.$charge_input) is-invalid @enderror" id="charge_price_{{$charge_input}}" min="0" step="0.01" placeholder="Enter charge price" wire:model="charge_price.{{$charge_input}}">
                                    @error('charge_price.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="operator_{{$charge_input}}" class="form-label">Operator</label>
                                    <select class="form-select @error('operator.'.$charge_input) is-invalid @enderror" id="operator_{{$charge_input}}" wire:model="operator.{{$charge_input}}">
                                        <option value="+">+</option>
                                        <option value="-">-</option>
                                        <option value="*">*</option>
                                        <option value="/">/</option>
                                        <option value="%">%</option>
                                    </select>
                                    @error('operator.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-1 mb-3">
                                    <label class="form-label">&nbsp;</label>

                                    <button type="button" class="btn btn-sm btn-inverse-danger" wire:click="removeOtherChargesField({{$charge_key}})"><i class="bi bi-trash"></i>
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
