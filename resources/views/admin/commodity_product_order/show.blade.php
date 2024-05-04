<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a>
                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <p>
                                <b>Product: </b> {{ $data->getCommodityProduct->name }} <br>
                                <b>Brand: </b> {{ $data->getBrand->name }} <br>
                                <b>Purpose: </b> {{ $data->purpose }} <br>
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <p>
                                <b>Customer: </b> {{ $data->getCustomer->name }} <br>
                                <b>Phone: </b> {{ $data->getCustomer->phone }} <br>
                            </p>
                        </div>
                        <div class="col-4 text-end">
                            <p>
                                <b>Seller: </b> {{ $data->getSeller->name }} <br>
                                <b>Phone: </b> {{ $data->getSeller->phone }} <br>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <p>
                                <b>Billing Address</b> <br>
                                <b>Pincode: </b> {{ $data->billing_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $data->billing_address['address'] }} <br>
                                <b>City: </b> {{ $data->billing_address['city'] }} <br>
                                <b>State: </b> {{ $data->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p>
                                <b>Delivery Address</b> <br>
                                <b>Pincode: </b> {{ $data->delivery_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $data->delivery_address['address'] }} <br>
                                <b>City: </b> {{ $data->delivery_address['city'] }} <br>
                                <b>State: </b> {{ $data->delivery_address['state'] }} <br>
                            </p>
                        </div>
                    </div>

                    <h5 class="my-3">Product Variation</h5>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @foreach ($data->value[0]['value'] as $variation_heading)
                                        <th>{{ $variation_heading['name'] }}</th>
                                    @endforeach
                                    <th>Quantity</th>
                                    <th>Gauge Diff.</th>
                                    <th>Final Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->value as $variation)

                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        @foreach ($variation['value'] as $value)
                                            <td>{{ $value['value'] }}</td>
                                        @endforeach
                                        <td>{{ $variation['quantity'] }}</td>
                                        <td>{{ $variation['price'] }}</td>
                                        <td>{{ $variation['price'] + $data->base_price }}</td>
                                    </tr>

                                @endforeach

                                <tr>
                                    <td colspan="{{ count($data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="base_price" class="form-label">Base Price</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $data->base_price }}
                                        </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ count($data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="transport_price" class="form-label">Transport Price</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $data->transport_price }}
                                        </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ count($data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="commission" class="form-label">Commission</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $data->commission }}
                                        </td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 mb-2">
                            <div class="card">
                                <div class="card-body p-0 d-flex">
                                    @if ($data->quality_check_image && count($data->quality_check_image) > 0)
                                        @foreach ($data->quality_check_image as $check_image)
                                            <a href="{{ imageUrl($check_image) }}" target="_blank">
                                                <img src="{{ imageUrl($check_image) }}" class="img-thumbnail mr-1" alt="Quality Check Image" title="Quality Check Image">
                                            </a>
                                        @endforeach
                                    @else
                                        <button class="btn btn-inverse-primary btn-xs">Upload</button>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    Quality Check
                                    @if ($data->quality_check_image_status)
                                        <span class="badge {{ $data->quality_check_image_status == 'approved' ? 'bg-success' : 'bg-danger' }}"> {{ ucfirst($data->quality_check_image_status) }} </span>
                                        By {{ ucfirst($data->quality_check_image_status_updated_by) }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="card">
                                <div class="card-body p-0">
                                    @if ($data->quality_check_certificate)
                                        <a href="{{ imageUrl($data->quality_check_certificate) }}" target="_blank">
                                            <img src="{{ imageUrl($data->quality_check_certificate) }}" class="img-thumbnail" alt="Quality Check Certificate" title="Quality Check Certificate">
                                        </a>
                                    @else
                                        <div class="text-center p-4">
                                            <button class="btn btn-inverse-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">Upload</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    Quality Check Certificate
                                </div>
                            </div>
                        </div>

                    </div>

                    @if ($data->getDrivers->count() > 0)

                        <h5 class="my-3">Driver List</h5>

                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Photo</th>
                                        <th>Unloaded Vehicle Photo</th>
                                        <th>Loaded Vehicle Photo</th>
                                        <th>Driver With Vehicle Photo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->getDrivers as $driver)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $driver->name }}</td>
                                            <td>{{ $driver->phone }}</td>
                                            <td>
                                                <a href="{{ imageUrl($driver->photo) }}" target="_blank">
                                                    <img src="{{ imageUrl($driver->photo) }}" class="img-thumbnail" alt="Driver Photo" title="Driver Photo">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ imageUrl($driver->unloaded_vehicle_photo) }}" target="_blank">
                                                    <img src="{{ imageUrl($driver->unloaded_vehicle_photo) }}" class="img-thumbnail" alt="Unloaded Vehicle Photo" title="Unloaded Vehicle Photo">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ imageUrl($driver->loaded_vehicle_photo) }}" target="_blank">
                                                    <img src="{{ imageUrl($driver->loaded_vehicle_photo) }}" class="img-thumbnail" alt="Loaded Vehicle Photo" title="Loaded Vehicle Photo">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ imageUrl($driver->driver_with_vehicle_photo) }}" target="_blank">
                                                    <img src="{{ imageUrl($driver->driver_with_vehicle_photo) }}" class="img-thumbnail" alt="Driver With Vehicle Photo" title="Driver With Vehicle Photo">
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>

                <div class="card-footer">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <input type="file" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-xs">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
