<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4>{{ $page_title }}</h4>
                        <small> ( {{ $data->order_id }} ) </small>
                        <span
                            class="bz-status {{ $data->status == 'cancel' ? 'bz-status--danger' : ($data->status == 'pending' ? 'bz-status--warning' : 'bz-status--info') }} ms-1">{{ $data->status }}
                        </span>
                    </div>
                    <div class="bz-toolbar">
                        <a href="{{ route('admin.commodity-product-order.index') }}" class="btn btn-secondary btn-sm"
                            wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                    <div class="w-100 text-center">
                        @include('admin.commodity_product_order.menu', ['is_active' => 'transporter'])
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h5 class="my-3">Available Transporter</h5>
                        </div>

                        <div class="col-4 d-flex align-items-center justify-content-end">
                            @if (count($this->transporter_user_id))
                                <button class="btn btn-danger btn-sm" title="Send enquiry to seller"
                                    wire:click="sendTransporterEnquiry()"><i class="bi bi-send"></i>Send Enquiry</button>
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
                                            <input type="checkbox" id="transporter_{{ $transporter_data->user_id }}"
                                                class="form-check-input" value="{{ $transporter_data->user_id }}"
                                                wire:model.live="transporter_user_id">
                                            <label class="visually-hidden"
                                                for="transporter_{{ $transporter_data->user_id }}">Select
                                                transporter</label>
                                        </td>
                                        <td>{{ $transporter_data->getUser->name }}</td>
                                        <td>{{ $transporter_data->getUser->phone }}</td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>State</dt>
                                                    <dd>{{ $transporter_data->state }}</dd>
                                                </div>
                                                <div>
                                                    <dt>City</dt>
                                                    <dd>{{ $transporter_data->city }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <!-- Price Range -->
                                                <div>
                                                    <dt>Price Range</dt>
                                                    <dd>
                                                        ₹ {{ number_format($transporter_data->min_price) }} -
                                                        ₹ {{ number_format($transporter_data->max_price) }}
                                                    </dd>
                                                </div>

                                                <!-- Updated Price & Status (Conditional) -->
                                                @if ($transporter_data->enquiry_data && $transporter_data->enquiry_data->price)
                                                    <div>
                                                        <dt>Updated Price</dt>
                                                        <dd>
                                                            ₹
                                                            {{ number_format($transporter_data->enquiry_data->price) }}
                                                            <button class="btn btn-secondary btn-sm btn-icon"
                                                                title="Update Price" data-bs-toggle="modal"
                                                                data-bs-target="#updateTransporterPrice_{{ $transporter_data->enquiry_data->id }}"
                                                                wire:click="setTransporterPrice({{ $transporter_data->enquiry_data->id }})">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <br>
                                                            <span class="bz-status bz-status--info">
                                                                {{ ucfirst($transporter_data->enquiry_data->status) }}
                                                            </span>
                                                        </dd>
                                                    </div>
                                                @elseif ($transporter_data->enquiry_data)
                                                    <div>
                                                        <dt>Update Price</dt>
                                                        <dd>
                                                            <button class="btn btn-secondary btn-sm"
                                                                title="Update Price" data-bs-toggle="modal"
                                                                data-bs-target="#updateTransporterPrice_{{ $transporter_data->enquiry_data->id }}"
                                                                wire:click="setTransporterPrice({{ $transporter_data->enquiry_data->id }})">
                                                                <i class="bi bi-pencil-square"></i> Update Price
                                                            </button>
                                                        </dd>
                                                    </div>
                                                @endif
                                            </dl>
                                        </td>
                                        <td>
                                            @if (
                                                $transporter_data->enquiry_data &&
                                                    $transporter_data->enquiry_data->status == 'replied' &&
                                                    !$transporter_data->enquiry_data->is_mark)
                                                <button class="btn btn-sm btn-inverse-primary my-2"
                                                    title="Mark order to transporter"
                                                    wire:click="markTransporter({{ $transporter_data->enquiry_data->id }})">Mark
                                                    Transporter</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($transporter_data->enquiry_data)
                                        <div class="modal fade"
                                            id="updateTransporterPrice_{{ $transporter_data->enquiry_data->id }}"
                                            tabindex="-1"
                                            aria-labelledby="updateTransporterPriceLable_{{ $transporter_data->enquiry_data->id }}"
                                            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false"
                                            wire:ignore.self>
                                            <div class="modal-dialog modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="updateTransporterPriceLable_{{ $transporter_data->enquiry_data->id }}">
                                                            Update Transporter Price</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="btn-close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label
                                                            for="transporter_price_{{ $transporter_data->enquiry_data->id }}"
                                                            class="form-label">Transporter Price</label>
                                                        <input type="number" class="form-control"
                                                            id="transporter_price_{{ $transporter_data->enquiry_data->id }}"
                                                            placeholder="Enter Transporter Price"
                                                            wire:model="transporter_price">
                                                        @error('transporter_price')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            wire:click="updateTransporterPrice()">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <x-table-no-data colspan="6" icon="bi-truck" title="No transporters available"
                                        text="No transporter is available for this order yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
