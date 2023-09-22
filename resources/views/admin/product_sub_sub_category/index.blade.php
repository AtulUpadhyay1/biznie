<div>
    @if ($formMode)
        @include('admin.product_sub_sub_category.form')
    @else
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Product Sub Sub Categories</h4>
                        </div>
                        <div class="col-6">
                            <a class="btn btn-danger btn-icon-text float-end align-items-center"
                                wire:click="create()"><i class="bi bi-plus-lg me-1"></i>Add Sub Sub Category
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $data)
                                    <tr>
                                        <td><img src="{{ asset('storage/' . $data->thumbnail) }}" alt="image"></td>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input status_update"
                                                    wire:click="updateStatus({{ $data->id }})" value="20"
                                                    {{ $data->status == 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input featured_update"
                                                    wire:click="updateFeatured({{ $data->id }})" value="20"
                                                    {{ $data->featured == 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{$data->id}}" data-bs-toggle="dropdown" role="button"
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
