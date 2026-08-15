<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h4>Product Sub Categories</h4>
                <div class="bz-toolbar">
                    <div class="custom-search-bar">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <label class="bz-filter-label" for="search">Search</label>
                            <input id="search" type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                        </div>
                    </div>
                    <a type="button" class="btn btn-danger btn-sm"
                        title="add" href="{{ route('admin.create-product-sub-category') }}" wire:navigate>
                        <i class="bi bi-plus-lg"></i>Add Sub Category
                    </a>
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
                            @forelse ($list as $data)
                                <tr>
                                    <td><img src="{{ imageUrl($data->thumbnail) }}" alt="image"></td>
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
                                        <a type="button" id="ActionBtn_{{ $data->id }}" data-bs-toggle="dropdown"
                                            role="button" aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="ActionBtn_{{ $data->id }}">
                                            <a class="dropdown-item d-flex align-items-center" href=""><i
                                                    class="bi bi-eye me-2"></i><span>View</span></a>
                                            <a href="{{route('admin.edit-product-sub-category', $data->id)}}" class="dropdown-item d-flex align-items-center"
                                                wire:navigate>
                                                <i class="bi bi-pencil-square me-2"></i><span>Edit</span></a>
                                            <a href="javascript:;" class="dropdown-item d-flex align-items-center"
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
