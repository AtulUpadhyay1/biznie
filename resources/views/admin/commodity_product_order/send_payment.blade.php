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
                            {{-- <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a> --}}
                            <div class="btn-group mb-3 mb-md-0" role="group" aria-label="Basic example">
                                <a href="{{route('admin.commodity-product-order.ledger', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-file-earmark-ruled icon-sm"></i>  Buyer Ledger
                                </a>
                                <a href="{{route('admin.commodity-product-order.sellerLedger', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-file-ruled icon-sm"></i> Seller Ledger
                                </a>
                                <a href="{{route('admin.commodity-product-order.receive-payment', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-credit-card-2-front icon-sm"></i> Receive Payment
                                </a>
                                <a href="{{route('admin.commodity-product-order.send-payment', $data->id)}}" class="btn btn-sm btn-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-credit-card icon-sm"></i> Send Payment
                                </a>
                                <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-eye icon-sm"></i> View
                                </a>
                                <a href="{{route('admin.commodity-product-order.status', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-device-ssd icon-sm"></i> Update Status
                                </a>
                                <a href="{{route('admin.commodity-product-order.history', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                    <i class="bi bi-clock-history icon-sm"></i> History
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <p>
                                    <b>Company Name: </b> {{ $data->getCustomer?->getUserDetail?->company_name ?? '--' }} <br>
                                    <b>User: </b> {{ $data->getCustomer?->name }} <br>
                                    <b>GST: </b> {{ $data->getCustomer?->getUserDetail?->gst_number ?? '--' }} <br>
                                    <b>Phone: </b> {{ $data->getCustomer?->phone }} <br>
                                </p>
                            </div>
                            <div class="col-6 mb-3">
                                <p>
                                    <b>Total Amount:</b> ₹ {{ formatIndianNumber($data->total_amount) }} <br>
                                    <b>Paid Amount:</b> ₹ {{ formatIndianNumber($data->paid_amount) }} <br>
                                    <b>Remaining Amount:</b> ₹ {{ formatIndianNumber($data->due_amount) }} <br>
                                </p>
                            </div>
                        </div>
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

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" wire:model="status">
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="success">Success</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
