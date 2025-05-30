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
                            @include('admin.commodity_product_order.menu', ['is_active' => 'ledger'])
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Total Used Credit Balance : ₹ {{ $total_credit_wallet }} | Total Paid Credit Balance: ₹ {{ $total_pay_credit_wallet }}</h6>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-xs btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#addBalance">
                                Add Credit Balance
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
    <div class="modal fade" id="addBalance" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBalanceLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBalanceLabel">Add Balance In Credit Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" placeholder="Enter amount" wire:model="amount" required>
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="mode" class="form-label">Mode</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_cash" value="cash" wire:model="payment_method" required>
                                <label class="form-check-label" for="mode_cash">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_online" value="online" wire:model="payment_method">
                                <label class="form-check-label" for="mode_online">Online</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_cheque" value="cheque" wire:model="payment_method">
                                <label class="form-check-label" for="mode_cheque">Cheque</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode" id="mode_other" value="other" wire:model="payment_method">
                                <label class="form-check-label" for="mode_other">Other</label>
                            </div>
                        </div>
                        @error('mode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    {{-- <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="1" placeholder="Enter description" wire:model="description"></textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div> --}}
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" rows="1" placeholder="Enter notes" wire:model="notes"></textarea>
                        @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-xs btn-primary" wire:click="addCreditWalletBalanace()">Add Balance</button>
                </div>
            </div>
        </div>
    </div>
</div>
