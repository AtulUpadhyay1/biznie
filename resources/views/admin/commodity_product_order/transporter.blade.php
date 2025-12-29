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
                            <small> ( {{ $data->order_id }} ) </small>
                            <span class="badge rounded-pill border {{$data->status == 'cancel' ? 'border-danger text-danger' : 'border-primary text-primary' }} rounded-pill ms-1">{{ $data->status }} </span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center ms-2" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                        <div class="col-12 text-center">
                            @include('admin.commodity_product_order.menu', ['is_active' => 'transporter'])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h5 class="my-3">Available Transporter</h5>
                        </div>

                        <div class="col-4 text-end">
                            @if (count($this->transporter_user_id))
                                <button class="btn btn-primary btn-xs my-2" title="Send enquiry to seller" wire:click="sendTransporterEnquiry()">Send Enquiry</button>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transporter Name</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th colspan="2">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transporter_list as $transporter_data)
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="transporter_{{ $transporter_data->user_id }}" class="form-check-input" value="{{ $transporter_data->user_id }}" wire:model.live="transporter_user_id">
                                        </td>
                                        <td>{{ $transporter_data->getUser->name }}</td>
                                        <td>{{ $transporter_data->getUser->phone }}</td>
                                        <td>
                                            <b>State</b> : {{ $transporter_data->state }} <br>
                                            <b>City</b> : {{ $transporter_data->city }} <br>
                                        </td>
                                        <td>
                                            ₹ {{ number_format($transporter_data->min_price) }} - ₹ {{ number_format($transporter_data->max_price) }} <br>
                                            @if ($transporter_data->enquiry_data && $transporter_data->enquiry_data->price)
                                                Updated Price: ₹ {{ number_format($transporter_data->enquiry_data->price) }} <button class="btn btn-info btn-sm p-0" title="Update Price" data-bs-toggle="modal" data-bs-target="#updateTransporterPrice_{{ $transporter_data->enquiry_data->id }}" wire:click="setTransporterPrice({{ $transporter_data->enquiry_data->id }})"><i class="bi bi-pencil-square"></i></button>
                                                <br><span class="badge bg-info">{{ ucfirst($transporter_data->enquiry_data->status) }}</span>
                                            @elseif ($transporter_data->enquiry_data)
                                                <button class="btn btn-info btn-sm p-0" title="Update Price" data-bs-toggle="modal" data-bs-target="#updateTransporterPrice_{{ $transporter_data->enquiry_data->id }}" wire:click="setTransporterPrice({{ $transporter_data->enquiry_data->id }})"><i class="bi bi-pencil-square"></i> Update Price</button>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transporter_data->enquiry_data && $transporter_data->enquiry_data->status == 'replied' && !$transporter_data->enquiry_data->is_mark)
                                                <button class="btn btn-primary btn-xs my-2" title="Mark order to transporter" wire:click="markTransporter({{ $transporter_data->enquiry_data->id }})">Mark Transporter</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($transporter_data->enquiry_data)
                                        <div class="modal fade" id="updateTransporterPrice_{{ $transporter_data->enquiry_data->id }}" tabindex="-1" aria-labelledby="updateTransporterPriceLable_{{ $transporter_data->enquiry_data->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                                            <div class="modal-dialog modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="updateTransporterPriceLable_{{ $transporter_data->enquiry_data->id }}">Update Transporter Price</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label for="transporter_price_{{ $transporter_data->enquiry_data->id }}" class="form-label">Transporter Price</label>
                                                        <input type="number" class="form-control" id="transporter_price_{{ $transporter_data->enquiry_data->id }}" placeholder="Enter Transporter Price" wire:model="transporter_price">
                                                        @error('transporter_price') <small class="text-danger">{{ $message }}</small>@enderror
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary" wire:click="updateTransporterPrice()">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No Transporter Available for this Order</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
