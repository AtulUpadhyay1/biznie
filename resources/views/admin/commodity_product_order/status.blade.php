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
                            {{-- <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a> --}}
                            <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="View" wire:navigate>
                                <i class="bi bi-eye icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.status', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="Update Status" wire:navigate>
                                <i class="bi bi-device-ssd icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.history', $data->id)}}" class="btn btn-secondary btn-icon btn-sm me-1" title="History" wire:navigate>
                                <i class="bi bi-clock-history icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <p><b>Current Status : </b> {{ ucwords($data->status) }}</p>
                        </div>

                        <div class="col-4 text-end mb-2">
                            <select class="form-select" wire:model="status" wire:change="updateStatus()">
                                <option value="pending" disabled="">Pending</option>
                                <option value="confirm">Confirm</option>
                                <option value="vehicle booked">Vehicle Booked</option>
                                <option value="vehicle waiting to load">Vehicle Waiting To Load</option>
                                <option value="loading">Loading</option>
                                <option value="bills generated">Bills Generated</option>
                                <option value="dispatched">Dispatched</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancel">Cancel</option>
                            </select>
                        </div>

                        <hr>
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
                                <b>Address: </b> {{ $data->billing_address['address_line_one']??'' }} <br>
                                <b>City: </b> {{ $data->billing_address['city'] }} <br>
                                <b>State: </b> {{ $data->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p>
                                <b>Delivery Address</b> <br>
                                <b>Pincode: </b> {{ $data->delivery_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $data->delivery_address['address_line_one']??'' }} <br>
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
                </div>
            </div>
        </div>
    </div>
</div>
