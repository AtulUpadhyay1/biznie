<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.credit-wallet-request.create')}}" class="btn btn-danger btn-sm" wire:navigate><i class="bi bi-plus-lg"></i>Add Request</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Info</th>
                                    <th>Reference Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <b>Company Name: </b>{{ $data->getUser?->getUserDetail?->company_name }} <br>
                                            <b>Name: </b>{{ $data->getUser->name }} <br>
                                            <b>Phone: </b>{{ $data->getUser->phone }} <br>
                                        </td>
                                        <td>{{ $data->reference_number ?? 'NA' }}</td>
                                        <td>
                                            @if ($data->status == 'Approved')
                                                <span class="bz-status bz-status--success">{{ $data->status }}</span>
                                            @elseif ($data->status == 'Rejected')
                                                <span class="bz-status bz-status--danger">{{ $data->status }}</span>
                                            @else
                                                <span class="bz-status bz-status--warning">{{ $data->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.credit-wallet-request.show', $data->id)}}" wire:navigate><i class="bi bi-eye me-2"></i><span>View</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
