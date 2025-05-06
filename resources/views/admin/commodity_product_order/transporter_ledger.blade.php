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
                            @include('admin.commodity_product_order.menu', ['is_active' => 'transporterLedger'])
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Due Amount : ₹ {{ formatIndianNumber($data->total_freight_amount) }} | Paid Amount : ₹ {{ $total_paid }}</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-info btn-xs mb-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
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
                                        <td>{{ $key + 1 + ($ledgers->currentPage() - 1) * $ledgers->perPage() }}</td>
                                        <td>
                                            {{ $data->transaction_id }} <br>
                                            <span class="badge {{$data->type == 'credit' ? 'bg-success' : 'bg-danger'}}">
                                                {{ ucfirst($data->type) }}
                                            </span> <br>
                                            <b>Date & Time : </b>
                                            {{ dateTimeFormat($data->date_time ?? $data->created_at) }}
                                        </td>

                                        <td>₹ {{ formatIndianNumber($data->amount) }}</td>
                                        <td>₹ {{ formatIndianNumber($data->remaining_balance) }}</td>
                                        <td style="width: 300px;">
                                            <b> Add By : </b> {{ ucfirst($data->added_by) }} <br>
                                            @if ($data->payment_mode)
                                                <b>Payment Mode : </b> {{ $data->payment_mode }} <br>
                                            @endif
                                            @if ($data->payment_method)
                                                <b>Payment Method : </b> {{ $data->payment_method }} <br>
                                            @endif
                                            @if ($data->transaction_account_name)
                                                <b>Account Name : </b> {{ $data->transaction_account_name }} <br>
                                            @endif
                                            @if ($data->transaction_account_number)
                                                <b>Account Number : </b> {{ $data->transaction_account_number }} <br>
                                            @endif
                                            @if ($data->transaction_bank_name)
                                                <b>Bank Name : </b> {{ $data->transaction_bank_name }} <br>
                                            @endif
                                            @if ($data->transaction_number)
                                                <b>Transaction ID : </b> {{ $data->transaction_number }} <br>
                                            @endif
                                            @if ($data->description)
                                                <b>Description : </b> {{ $data->description }} <br>
                                            @endif
                                            @if ($data->file)
                                                <b>File: </b>
                                                <a href="{{ asset('storage/'.$data->file) }}" target="_blank">View</a> <br>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $ledgers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Payment</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Paymet Added By :</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="added_by" id="seller" value="seller" wire:model="added_by">
                            <label class="form-check-label" for="seller">
                                Seller
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="added_by" id="biznie" value="biznie" wire:model="added_by">
                            <label class="form-check-label" for="biznie">
                                Biznie
                            </label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Payment Method :</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="payment_method" id="cash" value="cash" wire:model="payment_method">
                            <label class="form-check-label" for="cash">
                                Cash
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="payment_method" id="online" value="online" wire:model="payment_method">
                            <label class="form-check-label" for="online">
                                Online
                            </label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="amount">Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" placeholder="Enter amount" wire:model="amount">
                        @error('amount') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="1" placeholder="Enter description"></textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="file">File</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file">
                        @error('file') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-xs" wire:click="save()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
