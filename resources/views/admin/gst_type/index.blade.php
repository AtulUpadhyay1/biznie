<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Gst Type</h4>

                    <div class="bz-toolbar">
                        <form class="custom-search-bar">
                            <label class="bz-filter-label" for="gst_type_search">Search GST types</label>
                            <div class="input-group">
                                <span class="input-group-text"> <i class="bi bi-search"></i></span>
                                <input type="text" id="gst_type_search" class="form-control" placeholder="Search here...">
                            </div>
                        </form>
                        <a type="button" class="btn btn-danger btn-sm"
                            title="add" href="{{ route('admin.create-gst-type') }}" wire:navigate>
                            <i class="bi bi-plus-lg"></i>
                            Add Gst Type
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->value }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input status_update"
                                                    wire:click="updateStatus({{ $data->id }})" value="20"
                                                    {{ $data->status == 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{ $data->id }}"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{ $data->id }}">
                                                <a class="dropdown-item" href=""><i
                                                        class="bi bi-eye"></i><span>View</span></a>
                                                <a href="{{route('admin.edit-gst-type', $data->id)}}"
                                                    class="dropdown-item" wire:navigate><i
                                                        class="bi bi-pencil-square"></i><span>Edit</span></a>
                                                <a href="javascript:;" class="dropdown-item"
                                                    wire:click="delete({{ $data->id }})"><i
                                                        class="bi bi-trash"></i><span>Delete</span></a>
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
