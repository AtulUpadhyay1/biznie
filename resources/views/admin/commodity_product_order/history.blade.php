<div>
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
                                    <a href="{{route('admin.commodity-product-order.send-payment', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                        <i class="bi bi-credit-card icon-sm"></i> Send Payment
                                    </a>
                                    <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                        <i class="bi bi-eye icon-sm"></i> View
                                    </a>
                                    <a href="{{route('admin.commodity-product-order.status', $data->id)}}" class="btn btn-sm btn-outline-primary btn-icon-text" wire:navigate>
                                        <i class="bi bi-device-ssd icon-sm"></i> Update Status
                                    </a>
                                    <a href="{{route('admin.commodity-product-order.history', $data->id)}}" class="btn btn-sm btn-primary btn-icon-text" wire:navigate>
                                        <i class="bi bi-clock-history icon-sm"></i> History
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->history ?? [] as $history)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ ucwords($history['status']) }}</td>
                                            <td>{{ dateTimeFormat($history['created_at']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
