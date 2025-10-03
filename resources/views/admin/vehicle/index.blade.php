<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                @can('vehicle-create')
                                    <a href="{{ route('admin.vehicle.create') }}" class="btn btn-danger btn-sm btn-icon-text mb-2 mb-md-0" wire:navigate>
                                        <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                        Add
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>
                                            <img src="{{ imageUrl($data->photo) }}">
                                        </td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->type }}</td>
                                        <td>{{ $data->capacity }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input status_update" wire:click="updateStatus({{$data->id}})"
                                                    value="20" {{$data->status == 1 ? 'checked' : ''}}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @can('vehicle-edit')
                                                <a type="button" id="actionBtn_{{$data->id}}" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="actionBtn_{{$data->id}}">
                                                    <a class="dropdown-item d-flex align-items-center" href="{{route('admin.vehicle.edit', $data->id)}}" wire:navigate><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                </div>
                                            @endcan
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
