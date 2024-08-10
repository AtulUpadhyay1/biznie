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
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab"
                                        aria-controls="pills-home" aria-selected="true">Driver</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-view-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-view" type="button" role="tab"
                                        aria-controls="pills-view" aria-selected="false">Transporter</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile" aria-selected="false">Vehicle</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-contact" type="button" role="tab"
                                        aria-controls="pills-contact" aria-selected="false">Bill</button>
                                </li>
                            </ul>
                            <hr>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="card text-center p-2">
                                                <a href="{{ imageUrl($data->photo) }}" target="_blank">
                                                    <img class="rounded-circle" src="{{ imageUrl($data->photo) }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/avatar.png') }}'"
                                                        alt="" height="100" width="100">
                                                </a>
                                                <p><b>Name : </b>{{ $data->name }}</p>
                                                <p><b>Phone : </b>{{ $data->phone }}</p>
                                                <p><b>Alternate Phone : </b>{{ $data->alternate_phone_number }}</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>Vehicle Number: </b>{{ $data->vehicle_number }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>Unloaded Vehicle Photo: </b>
                                                <div class="mt-2">
                                                    <a href="{{ imageUrl($data->unloaded_vehicle_photo) }}"
                                                        target="_blank">
                                                        <img src="{{ imageUrl($data->unloaded_vehicle_photo) }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'"
                                                            alt="" height="100" width="150">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>Loaded Vehicle Photo: </b>
                                                <div class="mt-2">
                                                    <a href="{{ imageUrl($data->unloaded_vehicle_photo) }}"
                                                        target="_blank">
                                                        <img src="{{ imageUrl($data->loaded_vehicle_photo) }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'"
                                                            alt="" height="100" width="150">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>Driver With Vehicle Photo: </b>
                                                <div class="mt-2">
                                                    <a href="{{ imageUrl($data->driver_with_vehicle_photo) }}"
                                                        target="_blank"><img
                                                            src="{{ imageUrl($data->driver_with_vehicle_photo) }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'"
                                                            alt="" height="100" width="150">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                    aria-labelledby="pills-contact-tab">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="card text-center p-2">
                                                <b>Tracking Number: </b>{{ $data->tracking_number }}
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>Invoice: </b> <a href="{{ imageUrl($data->invoice) }}"
                                                    target="_blank">
                                                    <img src="{{ imageUrl($data->invoice) }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'"
                                                        alt="" height="100" width="150"></a>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>eBill: </b> <a href="{{ imageUrl($data->ebill) }}"
                                                    target="_blank"><img src="{{ imageUrl($data->ebill) }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'"
                                                        alt="" height="100" width="150"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-view" role="tabpanel"
                                    aria-labelledby="pills-view-tab">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <b>Transport Receipt: </b> <a
                                                    href="{{ imageUrl($data->transport_receipt) }}"
                                                    target="_blank"><img
                                                        src="{{ imageUrl($data->transport_receipt) }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/others/placeholder.jpg') }}'"
                                                        alt="" height="100" width="150"></a>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <p><b>Transporter Name: </b> {{ $data->transporter_name }}</p>
                                                <p><b>Transporter Phone Number: </b>
                                                    {{ $data->transporter_phone_number }}</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="card text-center p-4">
                                                <p><b>Advance Amount: </b> {{ $data->advance_amount }}/-</p>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <p>
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
                    </p> --}}
                </div>
            </div>
        </div>
    </div>
</div>
