<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-5 card-title">
                            <h4>{{ $page_title }}</h4>
                            <small> ( {{ $data->order_id }} ) </small>
                            <span class="badge rounded-pill border {{$data->status == 'cancel' ? 'border-danger text-danger' : 'border-primary text-primary' }} rounded-pill ms-1">{{ $data->status }} </span>
                        </div>
                        <div class="col-7 text-end">
                            {{-- <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a> --}}
                            <a href="{{route('admin.commodity-product-order.ledger', $data->id)}}" class="btn btn-warning btn-sm" title="Receive Paymet" wire:navigate>
                                Ledger
                            </a>

                            <a href="{{route('admin.commodity-product-order.receive-payment', $data->id)}}" class="btn btn-outline-primary btn-sm" title="Receive Paymet" wire:navigate>
                                Receive Paymet
                            </a>

                            <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-outline-info btn-sm" title="Send Paymet" wire:navigate>
                                Send Paymet
                            </a>

                            <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="View" wire:navigate>
                                <i class="bi bi-eye icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.status', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="Update Status" wire:navigate>
                                <i class="bi bi-device-ssd icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.history', $data->id)}}" class="btn btn-secondary btn-icon btn-sm me-1" title="History" wire:navigate>
                                <i class="bi bi-clock-history icon-sm"></i>
                            </a>

                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
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
</div>
