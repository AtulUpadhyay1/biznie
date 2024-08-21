<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <x-loader />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-5 card-title">
                            <h4>{{ $page_title }}</h4>
                            <small> ( {{ $data->order_id }} ) </small>
                            <span class="badge bg-primary rounded-pill ms-1">{{ $data->status }} </span>
                        </div>
                        <div class="col-7 text-end">
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

                    <div class="row mt-3">
                        <div class="col-md-3 mb-2">
                            <div class="card">
                                <div class="card-body p-0">
                                    @if ($data->quality_check_image && count($data->quality_check_image) > 0)
                                        @foreach ($data->quality_check_image as $check_image)
                                            <a href="{{ imageUrl($check_image) }}" target="_blank">
                                                <img src="{{ imageUrl($check_image) }}" class="img-thumbnail mr-1" alt="Quality Check Image" title="Quality Check Image">
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="text-center p-4">
                                            <button class="btn btn-inverse-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#fileUploadModal" wire:click="setUploadType('quality_check_image')">Upload</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    Quality Check
                                    @if ($data->quality_check_image_status && $data->quality_check_image_status != 'pending')
                                        <span class="badge {{ $data->quality_check_image_status == 'approved' ? 'bg-success' : 'bg-danger' }}"> {{ ucfirst($data->quality_check_image_status) }} </span>
                                        By {{ ucfirst($data->quality_check_image_status_updated_by) }}
                                    @endif
                                    @if (!$data->quality_check_image_status || $data->quality_check_image_status == 'pending')
                                        <button class="btn p-0 float-end ms-1" title="Rejected" wire:click="qualityCheckImageStatus('rejected')"><i class="bi bi-x-square-fill text-danger"></i></button>
                                        <button class="btn p-0 float-end" title="Approved" wire:click="qualityCheckImageStatus('approved')"><i class="bi bi-check-square-fill text-success"></i></button>
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
                                            <button class="btn btn-inverse-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#fileUploadModal" wire:click="setUploadType('quality_check_certificate')">Upload</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    Quality Check Certificate
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                    <span class="fs-5">
                                        Customer Quality Check Visibility
                                        <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="It enable product order quality check visible to customer.">
                                            <i class="bi bi-info-circle"></i>
                                        </span>
                                    </span>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:click="customerQualityCheck()" {{$data->customer_quality_check_visibility ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <div class="card-title">
                                        <h5>Driver List</h5>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <a href="{{route('admin.commodity-product-order-driver.create', $data->id)}}" class="btn btn-secondary btn-xs btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-plus btn-icon-prepend"></i>Add</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row profile-body">
                                @foreach ($data->getDrivers as $driver)
                                    <div class="d-none d-md-block col-md-3 left-wrapper mb-3">
                                        <div class="card rounded">
                                            <div class="card-body p-3">
                                                <div class="text-center mb-1">
                                                    <img class="wd-70 rounded-circle profile-img" src="{{ imageUrl($driver->photo) }}" onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/avatar.png') }}'" alt="profile">
                                                </div>
                                                <div class="text-center mb-2">
                                                    <h6 class="card-title mb-0">{{$driver->name}}</h6>
                                                </div>
                                                <p><i class="bi bi-phone"></i> {{$driver->phone}} </p>
                                                <div class="mt-1">
                                                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Vehicle Number:</label>
                                                    <p class="text-muted">{{$driver->vehicle_number}}</p>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Tracking Number:</label>
                                                    <p class="text-muted">{{$driver->tracking_number ?? '--'}}</p>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Transporter Name:</label>
                                                    <p class="text-muted">{{$driver->transporter_name ?? '--'}}</p>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Transporter Phone Number:</label>
                                                    <p class="text-muted">{{$driver->transporter_phone_number ?? '--'}}</p>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Advance Amount:</label>
                                                    <p class="text-muted">{{$driver->advance_amount ?? '--'}}</p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex">
                                                    <a href="{{route('admin.commodity-product-order-driver.show', [$data->id, $driver->id])}}" class="btn btn-icon border btn-xs me-2 btn-light" title="View" wire:navigate>
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{route('admin.commodity-product-order-driver.edit', [$data->id, $driver->id])}}" class="btn btn-icon border btn-xs me-2 btn-info" title="Edit" wire:navigate>
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="javascript:;" class="btn btn-icon border btn-xs me-2 btn-danger" title="Remove" wire:click="driverDelete({{$driver->id}})">
                                                        <i class="bi bi-trash3"></i>
                                                    </a>
                                                    @if ($driver->generate_invoice)
                                                        <button type="button" class="btn btn-icon border btn-xs me-2 btn-success" title="Print Invoice" wire:click="generateInvoice({{$driver->id}})">
                                                            <i class="bi bi-printer"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-icon border btn-xs me-2 btn-primary" title="Generate Invoice" type="button" data-bs-toggle="modal" data-bs-target="#invoiceGenrateModal_{{$driver->id}}">
                                                            <i class="bi bi-gear-wide-connected"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-icon border btn-xs me-2 btn-primary" title="eBill" type="button" data-bs-toggle="modal" data-bs-target="#eBillModal_{{$driver->id}}">
                                                        <i class="bi bi-receipt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="invoiceGenrateModal_{{$driver->id}}" tabindex="-1" aria-labelledby="invoiceGenrateModalLabel_{{$driver->id}}" aria-hidden="true" data-bs-backdrop="static" wire:ignore.self>
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form wire:submit.prevent="generateInvoice({{$driver->id}})">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="invoiceGenrateModalLabel_{{$driver->id}}">Generate Invoice</h5>
                                                        {{-- <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                                                    </div>
                                                    <div class="modal-body">
                                                        <div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="radio" class="form-check-input" name="generate_invoice" id="seller" value="seller" wire:model="generate_invoice">
                                                                <label class="form-check-label" for="seller">
                                                                    By Seller
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="radio" class="form-check-input" name="generate_invoice" id="biznie" value="biznie" wire:model="generate_invoice">
                                                                <label class="form-check-label" for="biznie">
                                                                    By Biznie
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @error('generate_invoice')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="reset" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-xs" data-bs-dismiss="modal">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="eBillModal_{{$driver->id}}" tabindex="-1" aria-labelledby="eBillModalLabel_{{$driver->id}}" aria-hidden="true" data-bs-backdrop="static" wire:ignore.self>
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form wire:submit.prevent="updateeBill({{$driver->id}})">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="eBillModalLabel_{{$driver->id}}">eBill Upload</h5>
                                                        {{-- <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="eBill_file" class="form-control @error('eBill_file') is-invalid @enderror" wire:model="eBill_file">
                                                        <label for="eBill_file">
                                                            @if ($eBill_file)
                                                                <img src="{{ $eBill_file->temporaryUrl() }}" class="label-banner">
                                                            @elseif ($driver->ebill)
                                                                <img src="{{ imageUrl($driver->ebill) }}" class="label-banner">
                                                            @else
                                                                <img class="label-thumbnail" src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                                            @endif
                                                        </label>
                                                        @error('eBill_file')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="reset" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-xs">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true" data-bs-backdrop="static" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="uploadFile()">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileUploadModalLabel">Upload </h5>
                        {{-- <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                    </div>
                    <div class="modal-body">
                        <input type="file" class="form-control @error('uploaded_file') is-invalid @enderror" wire:model="uploaded_file">
                        @error('uploaded_file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-xs">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
