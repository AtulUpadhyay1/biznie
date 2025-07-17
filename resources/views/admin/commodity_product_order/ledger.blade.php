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
                            <button class="btn btn-xs btn-outline-secondary mb-2" data-bs-toggle="modal" data-bs-target="#addRefund">
                                Add Refund
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

    <div class="modal fade" id="addRefund" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addRefundLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRefundLabel">Add Refund Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="radioInline" id="manual" value="manual" wire:model.live="mode">
                                <label class="form-check-label" for="manual">
                                    Manual
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="radioInline" id="cash_wallet" value="cash_wallet" wire:model.live="mode">
                                <label class="form-check-label" for="cash_wallet">
                                    Cash Wallet
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="radioInline" id="credit_wallet" value="credit_wallet" wire:model.live="mode">
                                <label class="form-check-label" for="credit_wallet">
                                    Credit Wallet
                                </label>
                            </div>
                        </div>
                    </div>
                    @if ($mode == 'cash_wallet')
                        <div class="row">
                            <h5>Available Cash Wallet Balance: ₹ {{ formatIndianNumber($data->getCustomer->cash_balance ?? '0.00') }}</h5>
                            <div class="col-md-6 mb-3">
                                <label for="transaction_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('transaction_amount') is-invalid @enderror" id="transaction_amount" placeholder="Enter transaction amount" wire:model="transaction_amount">
                                @error('transaction_amount') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    @elseif ($mode == 'credit_wallet')
                        <div class="row">
                            <h5>Credit Limit: ₹ {{ formatIndianNumber($data->getCustomer->assign_credit_balance ?? '0.00') }}</h5>
                            <h5>Used Limit: ₹ {{ formatIndianNumber($data->getCustomer?->assign_credit_balance - $data->getCustomer?->credit_balance) }}</h5>
                            <h5>Available Credit Wallet Balance: ₹ {{ formatIndianNumber($data->getCustomer->credit_balance ?? '0.00') }}</h5>
                            <div class="col-md-6 mb-3">
                                <label for="transaction_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('transaction_amount') is-invalid @enderror" id="transaction_amount" placeholder="Enter transaction amount" wire:model="transaction_amount">
                                @error('transaction_amount') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="transaction_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('transaction_amount') is-invalid @enderror" id="transaction_amount" placeholder="Enter transaction amount" wire:model="transaction_amount">
                                @error('transaction_amount') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_account_name" class="form-label">Account Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('transaction_account_name') is-invalid @enderror" id="transaction_account_name" placeholder="Enter transaction account name" wire:model="transaction_account_name">
                                @error('transaction_account_name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('transaction_account_number') is-invalid @enderror" id="transaction_account_number" placeholder="Enter transaction account number" wire:model="transaction_account_number">
                                @error('transaction_account_number') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('transaction_bank_name') is-invalid @enderror" id="transaction_bank_name" placeholder="Enter transaction bank name" wire:model="transaction_bank_name">
                                @error('transaction_bank_name') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <!-- Transaction Number -->
                            <div class="col-md-6 mb-3">
                                <label for="transaction_number" class="form-label">Transaction Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('transaction_number') is-invalid @enderror" id="transaction_number" placeholder="Enter transaction number" wire:model="transaction_number">
                                @error('transaction_number') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <!-- Mode -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" wire:model="payment_method">
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Upi">UPI</option>
                                    <option value="Rtgs">RTGS</option>
                                    <option value="Neft">NEFT</option>
                                    <option value="Check">Check</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('mode') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <!-- Date and Time -->
                            <div class="col-md-6 mb-3">
                                <label for="date_time" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('date_time') is-invalid @enderror" id="date_time" wire:model="date_time">
                                @error('date_time') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <!-- Description -->
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" placeholder="Enter description" wire:model="description" rows="1"></textarea>
                                @error('description') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <!-- File -->
                            <div class="col-md-6 mb-3">
                                <label for="file" class="form-label">File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" wire:model="file">
                                @error('file') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-xs btn-primary" wire:click="addRefundBalance()">Add Refund</button>
                </div>
            </div>
        </div>
    </div>
</div>
