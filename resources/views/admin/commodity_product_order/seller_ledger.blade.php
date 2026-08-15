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
                        @include('admin.commodity_product_order.menu', ['is_active' => 'sellerLedger'])
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction</th>
                                    <th>Amount</th>
                                    {{-- <th>Remaining</th> --}}
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
                                        {{-- <td>₹ {{ formatIndianNumber($data->remaining_balance) }}</td> --}}
                                        <td style="width: 300px;">
                                            <dl class="bz-kv-list">
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
                                            </dl>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="4" />
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
</div>
