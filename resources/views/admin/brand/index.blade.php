<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{$page_title}}</h4>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <form class="custom-search-bar me-3 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <span class="input-group-text"> <i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                                    </div>
                                </form>
                                <a type="button" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="add" href="{{route('admin.create-brand')}}" wire:navigate>
                                    <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                    Add Brand
                                </a>
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
                                        <td><img src="{{asset('storage/'.$data->thumbnail)}}" alt="image"></td>
                                        <td>{{$data->name}}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input status_update" wire:click="updateStatus({{$data->id}})" {{$data->status == 1 ? 'checked' : ''}}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input featured_update" wire:click="updateFeatured({{$data->id}})"
                                                    value="20" {{$data->featured == 1 ? 'checked' : ''}}>
                                            </div>
                                        </td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{$data->id}}" data-bs-toggle="dropdown" role="button"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.brand.show', $data->id)}}" wire:navigate><i
                                                        class="bi bi-card-list icon-sm me-2"></i><span>Product List</span></a>

                                                <a href="{{route('admin.edit-brand', $data->id)}}" class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>

                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center" wire:click="delete({{$data->id}})"><i
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
</div>
