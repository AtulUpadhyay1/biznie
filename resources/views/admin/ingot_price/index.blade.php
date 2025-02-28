<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <a type="button"
                                    class="btn btn-danger btn-sm btn-icon-text float-end align-items-center"
                                    title="add" href="{{ route('admin.ingot_price.create') }}" wire:navigate.hover>
                                    <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                    Add
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
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $data)
                                    <tr>
                                        <td>{{ $data->location }}</td>
                                        <td>{{ $data->price }}</td>
                                        <td>{{ $data->created_at }}</td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{ $data->id }}"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{ $data->id }}">
                                                <a href="{{route('admin.ingot_price.edit', $data->id)}}"
                                                    class="dropdown-item d-flex align-items-center" wire:navigate><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center"
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
</div>
