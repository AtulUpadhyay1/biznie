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
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save()">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="base_price" class="form-label">Base Price</label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" placeholder="Enter Base Price" wire:model="base_price">
                                @error('base_price') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="loading_charge" class="form-label">Loading Charge</label>
                                <input type="number" class="form-control @error('loading_charge') is-invalid @enderror" id="loading_charge" placeholder="Enter Loading Charge" wire:model="loading_charge">
                                @error('loading_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="insurance_charge" class="form-label">Insurance Charge</label>
                                <input type="number" class="form-control @error('insurance_charge') is-invalid @enderror" id="insurance_charge" placeholder="Enter Insurance Charge" wire:model="insurance_charge">
                                @error('insurance_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="quality_charge" class="form-label">Quality Charge</label>
                                <input type="number" class="form-control @error('quality_charge') is-invalid @enderror" id="quality_charge" placeholder="Enter Quality Charge" wire:model="quality_charge">
                                @error('quality_charge') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gst" class="form-label">GST Charge</label>
                                <input type="number" class="form-control @error('gst') is-invalid @enderror" id="gst" placeholder="Enter GST Charge" wire:model="gst">
                                @error('gst') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tcs" class="form-label">TCS Charge</label>
                                <input type="number" class="form-control @error('tcs') is-invalid @enderror" id="tcs" placeholder="Enter TCS Charge" wire:model="tcs">
                                @error('tcs') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <h5>Other Charges</h5>
                            <hr>
                            @foreach($charge_inputs as $charge_key => $charge_input)

                                <div class="col-md-4 mb-3">
                                    <label for="charge_name_{{$charge_input}}" class="form-label">Charge Name</label>
                                    <input type="text" class="form-control @error('charge_name.'.$charge_input) is-invalid @enderror" id="charge_name_{{$charge_input}}" placeholder="Enter charge name" wire:model="charge_name.{{$charge_input}}" disabled>
                                    @error('charge_name.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="charge_price_{{$charge_input}}" class="form-label">Charge Price</label>
                                    <input type="number" class="form-control @error('charge_price.'.$charge_input) is-invalid @enderror" id="charge_price_{{$charge_input}}" min="0" step="0.01" placeholder="Enter charge price" wire:model="charge_price.{{$charge_input}}">
                                    @error('charge_price.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="operator_{{$charge_input}}" class="form-label">Operator</label>
                                    <select class="form-select @error('operator.'.$charge_input) is-invalid @enderror" id="operator_{{$charge_input}}" wire:model="operator.{{$charge_input}}" disabled>
                                        <option value="+">+</option>
                                        <option value="-">-</option>
                                        <option value="*">*</option>
                                        <option value="/">/</option>
                                        <option value="%">%</option>
                                    </select>
                                    @error('operator.'.$charge_input) <small class="text-danger">{{ $message }}</small>@enderror
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
                </div>
            </form>
        </div>
    </div>
</div>
