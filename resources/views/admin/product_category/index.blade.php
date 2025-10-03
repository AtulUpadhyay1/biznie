<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Product Categories</h4>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <div class="custom-search-bar me-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                                    </div>
                                </div>
                                @can('commodity_product-create')
                                    <a type="button" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="add" href="{{route('admin.create-product-category')}}" wire:navigate>
                                        <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                        Add Product Category
                                    </a>
                                @endcan
                            </div>
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
                                                    wire:click="updateFeatured({{ $data->id }})"
                                                    {{ $data->featured == 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            @can('commodity_product-edit')
                                                <a type="button" id="ActionBtn_{{$data->id}}" data-bs-toggle="dropdown" role="button"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="ActionBtn_{{$data->id}}">
                                                    <a href="{{route('admin.edit-product-category', $data->id)}}"
                                                        class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                            class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                            {{-- <a href="javascript:;"
                                                            class="dropdown-item d-flex align-items-center"
                                                            wire:click="delete({{ $data->id }})"><i
                                                        class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a> --}}
                                                </div>
                                            @endcan
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
