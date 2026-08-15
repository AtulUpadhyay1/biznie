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
                        {{-- <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a> --}}
                        @include('admin.commodity_product_order.menu', ['is_active' => 'history'])
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
                                @forelse ($data->history ?? [] as $history)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span
                                                class="bz-status {{ $history['status'] == 'cancel' ? 'bz-status--danger' : ($history['status'] == 'delivered' ? 'bz-status--success' : ($history['status'] == 'pending' ? 'bz-status--warning' : 'bz-status--info')) }}">{{ ucwords($history['status']) }}</span>
                                        </td>
                                        <td>{{ dateTimeFormat($history['created_at']) }}</td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="3" title="No history yet"
                                        text="No status changes have been recorded for this order." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
