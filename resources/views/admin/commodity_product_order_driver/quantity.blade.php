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
