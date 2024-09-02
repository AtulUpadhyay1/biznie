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
                </div>
            </div>
        </div>
    </div>
</div>
