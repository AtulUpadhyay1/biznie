<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4> {{ $page_title }} - {{ $data->unique_id }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{ route('admin.commodity-product-enquiry.index') }}"
                            class="btn btn-secondary btn-sm" wire:navigate><i
                                class="bi bi-arrow-left"></i>Back</a>
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
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="bz-status bz-status--info">{{ ucfirst($history['status']) }}</span></td>
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
