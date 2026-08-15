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
                        @include('admin.commodity_product_order.menu', ['is_active' => 'send-payment'])
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="bz-panel">
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company Name</dt>
                                            <dd class="fw-semibold">
                                                {{ $data->getSeller?->getBusiness?->name ?? '--' }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>User</dt>
                                            <dd class="fw-semibold">
                                                {{ $data->getSeller?->name ?? '--' }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd class="fw-semibold">
                                                {{ $data->getSeller?->getUserDetail?->gst_number ?? '--' }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd class="fw-semibold">
                                                {{ $data->getSeller?->phone ?? '--' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                            </div>
                            <div class="col-4 mb-3">
                                <div class="bz-panel">
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Total Amount</dt>
                                            <dd><strong>₹ {{ formatIndianNumber($total_amount) }}</strong></dd>
                                        </div>

                                        <div>
                                            <dt>Paid Amount</dt>
                                            <dd><strong class="text-success">₹
                                                    {{ formatIndianNumber($paid_amount) }}</strong></dd>
                                        </div>

                                        <div>
                                            <dt>Remaining Amount</dt>
                                            <dd><strong class="text-danger">₹
                                                    {{ formatIndianNumber($remaining_balance) }}</strong></dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div class="col-2 mb-3 text-end">
                                <a href="{{ route('admin.seller.payments', $data->getSeller?->id) }}?mode=cashwallet"
                                    class="btn btn-secondary btn-sm" wire:navigate>Cash Wallet</a>
                                <a href="{{ route('admin.seller.payments', $data->getSeller?->id) }}?mode=creditwallet"
                                    class="btn btn-secondary btn-sm" wire:navigate>Credit Wallet</a>
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
                                <select class="form-select @error('payment_method') is-invalid @enderror"
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
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
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
                    <div class="card-footer d-flex justify-content-end">
                        <x-submit-btn text="Save" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
