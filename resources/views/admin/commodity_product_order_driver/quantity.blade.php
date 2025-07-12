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
                                <i class="bi bi-arrow-left btn-icon-prepend"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="fw-bold">Note : Enter Final Quantity After Loading Material In Vehicle</p>
                    <p class="fw-bold mt-2 mb-2">Vehicle Detail</p>
                    <p><span class="fw-bold text-danger">Driver Name </span> : {{ $driver_data->name }} </p>
                    <p><span class="fw-bold text-danger">Vehicle Number </span> : {{ $driver_data->vehicle_number }} </p>
                    <p><span class="fw-bold text-danger">Driver Number </span> : {{ $driver_data->phone }} </p>
                    <p><span class="fw-bold text-danger">Alternate Number </span> : {{ $driver_data->alternate_phone_number }} </p>
                    <p><span class="fw-bold text-danger">Transporter Name </span> : {{ $driver_data->transporter_name }} </p>
                    <p><span class="fw-bold text-danger">Transporter Number </span> : {{ $driver_data->transporter_phone_number }} </p>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="invoice">Invoice</label>
                            <input type='file' id="invoice" class="form-control @error('invoice') is-invalid @enderror" wire:model="invoice">
                            <label for="invoice">
                                @if ($invoice)
                                    @php
                                        $extension = strtolower($invoice->getClientOriginalExtension());
                                    @endphp
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'PNG', 'gif']))
                                        <img src="{{ $invoice->temporaryUrl() }}" class="label-banner">
                                    @else
                                        <span class="text-warning">Preview not available for {{ $extension }} files.</span>
                                    @endif
                                @elseif ($show_invoice)
                                    @php
                                        $extension = strtolower(pathinfo($show_invoice, PATHINFO_EXTENSION));
                                    @endphp
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'PNG', 'gif']))
                                        <img src="{{ $show_invoice }}" class="label-banner">
                                    @else
                                        <a href="{{ $show_invoice }}" target="_blank">View Attachment</a>
                                    @endif
                                @else
                                    <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                @endif
                            </label>
                            @error('invoice')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="invoice">Amount</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" placeholder="Enter Amount" wire:model="amount">
                            @error('amount') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="ebill">E - Waybill</label>
                            <input type='file' id="ebill" class="form-control @error('ebill') is-invalid @enderror" wire:model="ebill">
                            <label for="ebill">
                                @if ($ebill)
                                    @php
                                        $extension = strtolower($ebill->getClientOriginalExtension());
                                    @endphp
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'PNG', 'gif']))
                                        <img src="{{ $ebill->temporaryUrl() }}" class="label-banner">
                                    @else
                                        <span class="text-warning">Preview not available for {{ $extension }} files.</span>
                                    @endif
                                @elseif ($show_ebill)
                                    @php
                                        $extension = strtolower(pathinfo($show_ebill, PATHINFO_EXTENSION));
                                    @endphp
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'PNG', 'gif']))
                                        <img src="{{ $show_ebill }}" class="label-banner">
                                    @else
                                        <a href="{{ $show_ebill }}" target="_blank">View Attachment</a>
                                    @endif
                                @else
                                    <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                @endif
                            </label>
                            @error('ebill')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="ebill_expiry_date">E - Waybill Expiry Date</label>
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
                                    @php
                                        $extension = strtolower($transport_receipt->getClientOriginalExtension());
                                    @endphp
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'PNG', 'gif']))
                                        <img src="{{ $transport_receipt->temporaryUrl() }}" class="label-banner">
                                    @else
                                        <span class="text-warning">Preview not available for {{ $extension }} files.</span>
                                    @endif
                                @elseif ($show_transport_receipt)
                                    @php
                                        $extension = strtolower(pathinfo($show_transport_receipt, PATHINFO_EXTENSION));
                                    @endphp
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'PNG', 'gif']))
                                        <img src="{{ $show_transport_receipt }}" class="label-banner">
                                    @else
                                        <a href="{{ $show_transport_receipt }}" target="_blank">View Attachment</a>
                                    @endif
                                @else
                                    <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                @endif
                            </label>
                            @error('transport_receipt')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="custom-table">
                            <tbody>
                                @foreach ($order_data->value as $key => $variation)

                                    <tr>
                                        @foreach ($variation['value'] as $value)
                                            <td>{{ $value['name'] }} : <span class="fw-bold">{{ $value['value'] }} {{ $value['unit']['short_name'] }}</span> </td>
                                        @endforeach
                                        <td style="width: 30%;">
                                            <div class="input-group">
                                                <input type="number" class="form-control form-control-sm @error('quantity.'.$key) is-invalid @enderror" placeholder="Enter quantity" wire:model="quantity.{{$key}}">
                                                <span class="input-group-text input-group-addon p-1">MT</span>
                                            </div>
                                            @error('quantity.'.$key) <small class="text-danger">{{ $message }}</small>@enderror
                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-danger mt-2" wire:click="update()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
