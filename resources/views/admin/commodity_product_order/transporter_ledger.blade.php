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
                        @include('admin.commodity_product_order.menu', [
                            'is_active' => 'transporterLedger',
                        ])
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <div class="bz-panel">
                                <dl class="bz-kv-list">
                                    <div>
                                        <dt>Transport Amount</dt>
                                        <dd><strong>₹
                                                {{ formatIndianNumber($data->transporter_invoice_amount) }}</strong></dd>
                                    </div>

                                    <div>
                                        <dt>Total Paid</dt>
                                        <dd><strong class="text-success">₹
                                                {{ formatIndianNumber($total_paid) }}</strong></dd>
                                    </div>

                                    <div>
                                        <dt>Remaining Amount</dt>
                                        <dd>
                                            <strong class="text-danger">
                                                ₹ {{ formatIndianNumber($data->transporter_invoice_amount - $total_paid) }}
                                            </strong>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">
                                Add Payment
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction</th>
                                    <th>Amount</th>
                                    <th>Remaining</th>
                                    <th>More Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ledgers as $key => $data)
                                    <tr>
                                        <td>{{ $ledgers->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->transaction_id }}</div>
                                            <span
                                                class="bz-status {{ $data->type == 'credit' ? 'bz-status--success' : 'bz-status--danger' }}">
                                                {{ ucfirst($data->type) }}
                                            </span> <br>
                                            <b>Date & Time : </b>
                                            {{ dateTimeFormat($data->date_time ?? $data->created_at) }}
                                        </td>

                                        <td>₹ {{ formatIndianNumber($data->amount) }}</td>
                                        <td>₹ {{ formatIndianNumber($data->remaining_balance) }}</td>
                                        <td style="width: 300px;">
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Add By</dt>
                                                    <dd>{{ ucfirst($data->added_by) }}</dd>
                                                </div>
                                                @if ($data->payment_mode)
                                                    <div>
                                                        <dt>Payment Mode</dt>
                                                        <dd>{{ $data->payment_mode }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->payment_method)
                                                    <div>
                                                        <dt>Payment Method</dt>
                                                        <dd>{{ $data->payment_method }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->transaction_account_name)
                                                    <div>
                                                        <dt>Account Name</dt>
                                                        <dd>{{ $data->transaction_account_name }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->transaction_account_number)
                                                    <div>
                                                        <dt>Account Number</dt>
                                                        <dd>{{ $data->transaction_account_number }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->transaction_bank_name)
                                                    <div>
                                                        <dt>Bank Name</dt>
                                                        <dd>{{ $data->transaction_bank_name }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->transaction_number)
                                                    <div>
                                                        <dt>Transaction ID</dt>
                                                        <dd>{{ $data->transaction_number }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->description)
                                                    <div>
                                                        <dt>Description</dt>
                                                        <dd>{{ $data->description }}</dd>
                                                    </div>
                                                @endif
                                                @if ($data->file)
                                                    <div>
                                                        <dt>File</dt>
                                                        <dd>
                                                            <a href="{{ asset('storage/' . $data->file) }}"
                                                                target="_blank">View</a>
                                                        </dd>
                                                    </div>
                                                @endif
                                                @if ($data->getDrivers)
                                                    <div>
                                                        <dt>Vehicle No</dt>
                                                        <dd>{{ $data->getDrivers->vehicle_number }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Driver Name</dt>
                                                        <dd>{{ $data->getDrivers->name }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Driver Phone</dt>
                                                        <dd>{{ $data->getDrivers->phone }}</dd>
                                                    </div>
                                                @endif
                                            </dl>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="5" />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="bz-pagination">
                            {{ $ledgers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Payment</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Paymet Added By :</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="added_by" id="seller" value="seller"
                                wire:model="added_by">
                            <label class="form-check-label" for="seller">
                                Seller
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="added_by" id="biznie" value="biznie"
                                wire:model="added_by">
                            <label class="form-check-label" for="biznie">
                                Biznie
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method :</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="payment_method" id="cash"
                                value="cash" wire:model="payment_method">
                            <label class="form-check-label" for="cash">
                                Cash
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="payment_method" id="online"
                                value="online" wire:model="payment_method">
                            <label class="form-check-label" for="online">
                                Online
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="driver_id">Vehicle <span class="text-danger">*</span></label>
                        <select class="form-select @error('driver_id') is-invalid @enderror" id="driver_id"
                            wire:model="driver_id">
                            <option value="">Select Vehicle</option>
                            @foreach ($driver_list ?? [] as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->vehicle_number }}</option>
                            @endforeach
                        </select>
                        @error('driver_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="amount">Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" placeholder="Enter amount" wire:model="amount">
                        @error('amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                            rows="1" placeholder="Enter description"></textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="file">File</label>
                        <input type="file" id="file" class="form-control @error('file') is-invalid @enderror"
                            wire:model="file">
                        @error('file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="save()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
