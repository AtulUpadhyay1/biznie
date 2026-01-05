<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                            <small> ( {{ $data->order_id }} ) </small>
                            <span
                                class="badge rounded-pill border {{ $data->status == 'cancel' ? 'border-danger text-danger' : ($data->status == 'pending' ? 'border-warning text-warning' : 'border-primary text-primary') }} rounded-pill ms-1">{{ $data->status }}
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{ route('admin.commodity-product-order.index') }}"
                                class="btn btn-danger btn-sm btn-icon-text float-end align-items-center ms-2"
                                wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                        <div class="col-12 text-center">
                            @include('admin.commodity_product_order.menu', ['is_active' => 'send-payment'])
                        </div>

                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-2">
                                        <div class="col-5 text-muted">Company Name</div>
                                        <div class="col-7 fw-semibold">
                                            {{ $data->getSeller?->getBusiness?->name ?? '--' }}
                                        </div>

                                        <div class="col-5 text-muted">User</div>
                                        <div class="col-7 fw-semibold">
                                            {{ $data->getSeller?->name ?? '--' }}
                                        </div>

                                        <div class="col-5 text-muted">GST</div>
                                        <div class="col-7 fw-semibold">
                                            {{ $data->getSeller?->getUserDetail?->gst_number ?? '--' }}
                                        </div>

                                        <div class="col-5 text-muted">Phone</div>
                                        <div class="col-7 fw-semibold">
                                            {{ $data->getSeller?->phone ?? '--' }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-4 mb-3">
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Total Amount</span>
                                        <strong>₹ {{ formatIndianNumber($total_amount) }}</strong>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Paid Amount</span>
                                        <strong class="text-success">₹ {{ formatIndianNumber($paid_amount) }}</strong>
                                    </div>

                                    <hr class="my-2">

                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Remaining Amount</span>
                                        <strong class="text-danger">₹
                                            {{ formatIndianNumber($remaining_balance) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 mb-3 text-end">
                                <a href="{{ route('admin.seller.payments', $data->getSeller?->id) }}?mode=cashwallet"
                                    class="btn btn-outline-success btn-sm" wire:navigate>Cash Wallet</a>
                                <a href="{{ route('admin.seller.payments', $data->getSeller?->id) }}?mode=creditwallet"
                                    class="btn btn-outline-primary btn-sm" wire:navigate>Credit Wallet</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="transaction_amount" class="form-label">Amount <span
                                        class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control @error('transaction_amount') is-invalid @enderror"
                                    id="transaction_amount" placeholder="Enter transaction amount"
                                    wire:model="transaction_amount">
                                @error('transaction_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_account_name" class="form-label">Account Name <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('transaction_account_name') is-invalid @enderror"
                                    id="transaction_account_name" placeholder="Enter transaction account name"
                                    wire:model="transaction_account_name">
                                @error('transaction_account_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_account_number" class="form-label">Account Number <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('transaction_account_number') is-invalid @enderror"
                                    id="transaction_account_number" placeholder="Enter transaction account number"
                                    wire:model="transaction_account_number">
                                @error('transaction_account_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_bank_name" class="form-label">Bank Name <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('transaction_bank_name') is-invalid @enderror"
                                    id="transaction_bank_name" placeholder="Enter transaction bank name"
                                    wire:model="transaction_bank_name">
                                @error('transaction_bank_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Transaction Number -->
                            <div class="col-md-6 mb-3">
                                <label for="transaction_number" class="form-label">Transaction Number <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('transaction_number') is-invalid @enderror"
                                    id="transaction_number" placeholder="Enter transaction number"
                                    wire:model="transaction_number">
                                @error('transaction_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Mode -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('payment_method') is-invalid @enderror"
                                    id="payment_method" wire:model="payment_method">
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Upi">UPI</option>
                                    <option value="Rtgs">RTGS</option>
                                    <option value="Neft">NEFT</option>
                                    <option value="Check">Check</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('mode')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Date and Time -->
                            <div class="col-md-6 mb-3">
                                <label for="date_time" class="form-label">Date and Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    class="form-control @error('date_time') is-invalid @enderror" id="date_time"
                                    wire:model="date_time">
                                @error('date_time')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    placeholder="Enter description" wire:model="description" rows="1"></textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- File -->
                            <div class="col-md-6 mb-3">
                                <label for="file" class="form-label">File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    id="file" wire:model="file">
                                @error('file')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status"
                                    wire:model="status">
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="success">Success</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
