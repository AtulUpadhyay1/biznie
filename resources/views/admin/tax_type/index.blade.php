<div>
    @section('title', config('app.name') . ' | '.$page_title)
    @if($formMode)
        @include('admin.tax_type.form')
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 card-title">
                                <h4>Tax Type</h4>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                    <form class="custom-search-bar me-3 mb-2 mb-md-0">
                                        <div class="input-group">
                                            <span class="input-group-text"> <i data-feather="search"></i></span>
                                            <input type="text" class="form-control" placeholder="Search here...">
                                        </div>
                                    </form>
                                    <x-add-btn text="Add Tax Type" function="create()" />
                                </div>
                            </div>
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
                                            <td>{{$data->name}}</td>
                                            <td>{{$data->value}}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input status_update"
                                                        wire:click="updateStatus({{ $data->id }})" value="20"
                                                        {{ $data->status == 1 ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <a type="button" id="ActionBtn_{{$data->id}}" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="ActionBtn_{{$data->id}}">
                                                    <a class="dropdown-item d-flex align-items-center" href=""><i
                                                            class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                    <a href="javascript:;"
                                                        class="dropdown-item d-flex align-items-center"
                                                        wire:click="edit({{ $data->id }})"><i
                                                            class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                    <a href="javascript:;"
                                                        class="dropdown-item d-flex align-items-center"
                                                        wire:click="delete({{ $data->id }})"><i
                                                            class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
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
    @endif
</div>
