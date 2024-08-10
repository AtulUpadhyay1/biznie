<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <x-loader />
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.commodity-product-order.show', $order_id) }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="Enter phone">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="alternate_phone_number">Alternate Phone Number</label>
                                <input type="number" id="alternate_phone_number" class="form-control @error('alternate_phone_number') is-invalid @enderror" wire:model="alternate_phone_number" placeholder="Enter Alternate Phone Number">
                                @error('alternate_phone_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="tracking_number">Tracking Number</label>
                                <input type="text" id="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" wire:model="tracking_number" placeholder="Enter Tracking Number">
                                @error('tracking_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="transporter_name">Transporter Name</label>
                                <input type="text" id="transporter_name" class="form-control @error('transporter_name') is-invalid @enderror" wire:model="transporter_name" placeholder="Enter Transporter Name">
                                @error('transporter_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="vehicle_number">Vehicle Number</label>
                                <input type="text" id="vehicle_number" class="form-control @error('vehicle_number') is-invalid @enderror" wire:model="vehicle_number" placeholder="Enter Vehicle Number">
                                @error('vehicle_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="transporter_phone_number">Transporter Phone Number</label>
                                <input type="number" id="transporter_phone_number" class="form-control @error('transporter_phone_number') is-invalid @enderror" wire:model="transporter_phone_number" placeholder="Enter Transporter Phone Number">
                                @error('transporter_phone_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="advance_amount">Advance Amount</label>
                                <input type="number" id="advance_amount" class="form-control @error('advance_amount') is-invalid @enderror" wire:model="advance_amount" placeholder="Enter Advance Amount">
                                @error('advance_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="driver_photo">Driver Photo</label>
                                <input type='file' id="driver_photo" class="form-control @error('driver_photo') is-invalid @enderror" wire:model="driver_photo">
                                <label for="driver_photo">
                                    @if ($driver_photo)
                                        <img src="{{ $driver_photo->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_driver_photo)
                                        <img src="{{ $show_driver_photo }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('driver_photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="unloaded_vehicle_photo">Unloaded Vehicle Photo</label>
                                <input type='file' id="unloaded_vehicle_photo" class="form-control @error('unloaded_vehicle_photo') is-invalid @enderror" wire:model="unloaded_vehicle_photo">
                                <label for="unloaded_vehicle_photo">
                                    @if ($unloaded_vehicle_photo)
                                        <img src="{{ $unloaded_vehicle_photo->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_unloaded_vehicle_photo)
                                        <img src="{{ $show_unloaded_vehicle_photo }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('unloaded_vehicle_photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="loaded_vehicle_photo">Loaded Vehicle Photo</label>
                                <input type='file' id="loaded_vehicle_photo" class="form-control @error('loaded_vehicle_photo') is-invalid @enderror" wire:model="loaded_vehicle_photo">
                                <label for="loaded_vehicle_photo">
                                    @if ($loaded_vehicle_photo)
                                        <img src="{{ $loaded_vehicle_photo->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_loaded_vehicle_photo)
                                        <img src="{{ $show_loaded_vehicle_photo }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('loaded_vehicle_photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="driver_with_vehicle_photo">Driver With Vehicle Photo</label>
                                <input type='file' id="driver_with_vehicle_photo" class="form-control @error('driver_with_vehicle_photo') is-invalid @enderror" wire:model="driver_with_vehicle_photo">
                                <label for="driver_with_vehicle_photo">
                                    @if ($driver_with_vehicle_photo)
                                        <img src="{{ $driver_with_vehicle_photo->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_driver_with_vehicle_photo)
                                        <img src="{{ $show_driver_with_vehicle_photo }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('driver_with_vehicle_photo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="invoice">Invoice</label>
                                <input type='file' id="invoice" class="form-control @error('invoice') is-invalid @enderror" wire:model="invoice">
                                <label for="invoice">
                                    @if ($invoice)
                                        <img src="{{ $invoice->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_invoice)
                                        <img src="{{ $show_invoice }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('invoice')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="ebill">eBill</label>
                                <input type='file' id="ebill" class="form-control @error('ebill') is-invalid @enderror" wire:model="ebill">
                                <label for="ebill">
                                    @if ($ebill)
                                        <img src="{{ $ebill->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_ebill)
                                        <img src="{{ $show_ebill }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('ebill')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="ebill_expiry_date">eBill Expiry Date</label>
                                <input type='date' id="ebill_expiry_date" class="form-control @error('ebill_expiry_date') is-invalid @enderror" wire:model="ebill_expiry_date">
                                @error('ebill_expiry_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="transport_receipt">Transport Receipt</label>
                                <input type='file' id="transport_receipt" class="form-control @error('transport_receipt') is-invalid @enderror" wire:model="transport_receipt">
                                <label for="transport_receipt">
                                    @if ($transport_receipt)
                                        <img src="{{ $transport_receipt->temporaryUrl() }}" class="label-banner">
                                    @elseif ($show_transport_receipt)
                                        <img src="{{ $show_transport_receipt }}" class="label-banner">
                                    @else
                                        <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                    @endif
                                </label>
                                @error('transport_receipt')
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
