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
                                <h4> {{ $page_title }} - {{ $data->order_id }}</h4>
                            </div>
                            <div class="col-6 text-end">
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
