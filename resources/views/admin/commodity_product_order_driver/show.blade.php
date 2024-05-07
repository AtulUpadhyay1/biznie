<div>
    @section('title', config('app.name') . ' | ' . $page_title)
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
                    <p>
                        <a href="{{ imageUrl($data->photo) }}" target="_blank"><img src="{{ imageUrl($data->photo) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/avatar.png') }}'" alt="" height="100" width="100"></a><br>
                        <b>Name: </b>{{ $data->name }} <br>
                        <b>Phone: </b>{{ $data->phone }} <br>
                        <b>Alternate Phone: </b>{{ $data->alternate_phone_number }} <br>
                        <b>Unloaded Vehicle Photo: </b> <a href="{{ imageUrl($data->unloaded_vehicle_photo) }}" target="_blank"><img src="{{ imageUrl($data->unloaded_vehicle_photo) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'" alt="" height="100" width="150"></a><br>
                        <b>Loaded Vehicle Photo: </b> <a href="{{ imageUrl($data->unloaded_vehicle_photo) }}" target="_blank"><img src="{{ imageUrl($data->loaded_vehicle_photo) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'" alt="" height="100" width="150"></a><br>
                        <b>Driver With Vehicle Photo: </b> <a href="{{ imageUrl($data->driver_with_vehicle_photo) }}" target="_blank"><img src="{{ imageUrl($data->driver_with_vehicle_photo) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'" alt="" height="100" width="150"></a><br>
                        <b>Vehicle Number: </b>{{ $data->vehicle_number }} <br>
                        <b>Tracking Number: </b>{{ $data->tracking_number }} <br>
                        <b>Invoice: </b> <a href="{{ imageUrl($data->invoice) }}" target="_blank"><img src="{{ imageUrl($data->invoice) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'" alt="" height="100" width="150"></a><br>
                        <b>eBill: </b> <a href="{{ imageUrl($data->ebill) }}" target="_blank"><img src="{{ imageUrl($data->ebill) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'" alt="" height="100" width="150"></a><br>
                        <b>Transport Receipt: </b> <a href="{{ imageUrl($data->transport_receipt) }}" target="_blank"><img src="{{ imageUrl($data->transport_receipt) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'" alt="" height="100" width="150"></a><br>
                        <b>Transporter Name: </b> {{ $data->transporter_name }} <br>
                        <b>Transporter Phone Number: </b> {{ $data->transporter_phone_number }} <br>
                        <b>Advance Amount: </b> {{ $data->advance_amount }} <br>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
