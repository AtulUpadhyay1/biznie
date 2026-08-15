<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <label class="bz-filter-label" for="search">Search</label>
                                <input id="search" type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                            </div>
                        </div>
                        <a type="button" class="btn btn-danger btn-sm" title="add" href="{{route('admin.packaging-type.create')}}" wire:navigate>
                            <i class="bi bi-plus-lg"></i>Add Packaging Type
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input status_update"
                                                    wire:click="updateStatus({{ $data->id }})" {{ $data->status == 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{$data->id}}" data-bs-toggle="dropdown" role="button"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{$data->id}}">
                                                <a href="{{route('admin.packaging-type.edit', $data->id)}}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-pencil-square me-2"></i><span>Edit</span></a>
                                                <a href="javascript:;"
                                                    class="dropdown-item d-flex align-items-center"
                                                    wire:click="delete({{ $data->id }})"><i
                                                        class="bi bi-trash me-2"></i><span>Delete</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
