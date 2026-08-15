<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
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
                        <label class="bz-filter-label" for="status">Status</label>
                        <select id="status" class="form-select" wire:model.live="status">
                            <option value="">Status</option>
                            <option value="active">Active</option>
                            <option value="in_active">In Active</option>
                        </select>
                        @can('transporter-create')
                            <a href="{{ route('admin.transporter.create') }}" class="btn btn-danger btn-sm" wire:navigate>
                                <i class="bi bi-plus-lg"></i>
                                Add
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Contact Info</th>
                                    <th>Registration Date</th>
                                    <th>Last Active</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            <div class="bz-cell-sub"><i class="bi bi-telephone"></i>{{ $data->phone }}</div>
                                        </td>
                                        <td>{{ dateFormat($data->created_at) }}</td>
                                        <td>{{ lastActive($data->id) }}</td>
                                        <td>
                                            {!! $data->status == 'active' ? '<span class="bz-status bz-status--success">Active</span>' : '<span class="bz-status bz-status--muted">Inactive</span>' !!}
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="actionBtn_{{$data->id}}" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="actionBtn_{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.transporter.profile', $data->id)}}" wire:navigate><i
                                                    class="bi bi-eye me-2"></i><span>View</span></a>
                                                @can('transporter-edit')
                                                    <a class="dropdown-item d-flex align-items-center" href="{{route('admin.transporter.edit', $data->id)}}" wire:navigate><i
                                                        class="bi bi-pencil-square me-2"></i><span>Edit</span></a>
                                                @endcan
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.transporter.vehicle', $data->id)}}" wire:navigate><i
                                                    class="bi bi-truck me-2"></i><span>Vehicles</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.transporter.product', $data->id)}}" wire:navigate><i
                                                    class="bi bi-cart me-2"></i><span>Products</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.transporter.addressPrice', $data->id)}}" wire:navigate><i
                                                    class="bi bi-diagram-2 me-2"></i><span>Belts</span></a>
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
    <div class="modal fade" id="showDetailsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showDetailsLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showDetailsLabel">View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($show)
                    <p>Name: {{ $show->name }}</p>
                    <p>Phone: {{ $show->phone }}</p>
                    <p>Alternate Phone: {{ $show->getTransporterDetail->alternate_phone }}</p>
                    <p>Company Name: {{ $show->getTransporterDetail->company_name }}</p>
                    <p>GST Number: {{ $show->getTransporterDetail->gst_number }}</p>
                    <p>Aadhar Number: {{ $show->getTransporterDetail->aadhar_number }}</p>
                    <p>Address: {{ $show->getTransporterDetail->address }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>
