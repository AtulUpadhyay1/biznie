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
                                <h4> {{ $page_title }} - {{ $data->getProductEnquiry->unique_id }}</h4>
                                <small> ( {{ $data->order_id }} ) </small>
                                <span class="badge rounded-pill border {{$data->status == 'cancel' ? 'border-danger text-danger' : 'border-primary text-primary' }} rounded-pill ms-1">{{ $data->status }} </span>
                            </div>
                            <div class="col-6 text-end">
                                <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="View" wire:navigate>
                                    <i class="bi bi-eye icon-sm"></i>
                                </a>
                                <a href="{{route('admin.commodity-product-order.status', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="Update Status" wire:navigate>
                                    <i class="bi bi-device-ssd icon-sm"></i>
                                </a>
                                <a href="{{route('admin.commodity-product-order.history', $data->id)}}" class="btn btn-secondary btn-icon btn-sm me-1" title="History" wire:navigate>
                                    <i class="bi bi-clock-history icon-sm"></i>
                                </a>
                                <a href="{{ route('admin.commodity-product-order.index') }}"
                                    class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i
                                        class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
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
