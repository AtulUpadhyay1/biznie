<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.seller.seller_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Staff List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->phone }}</td>
                                        <td>
                                            {{ $data->role ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if ($data->permission)
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#permissionModal{{ $data->id }}">
                                                    <i class="bi bi-eye"></i>View
                                                </button>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="6" />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permission Modals --}}
    @foreach ($list as $data)
        @if ($data->permission)
            <div class="modal fade" id="permissionModal{{ $data->id }}" tabindex="-1" aria-labelledby="permissionModalLabel{{ $data->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="permissionModalLabel{{ $data->id }}">Permissions for {{ $data->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @php
                                $permissions = is_string($data->permission) ? json_decode($data->permission, true) : $data->permission;
                            @endphp

                            @if (is_array($permissions))
                                <div class="row">
                                    @foreach ($permissions as $permissionGroup)
                                        @foreach ($permissionGroup as $module => $actions)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="mb-0">{{ ucfirst($module) }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach ($actions as $action => $status)
                                                                <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                    <span class="text-capitalize">{{ str_replace('-', ' ', $action) }}</span>
                                                                    @if ($status)
                                                                        <span class="bz-status bz-status--success">
                                                                            <i class="bi bi-check-lg"></i> Enabled
                                                                        </span>
                                                                    @else
                                                                        <span class="bz-status bz-status--muted">
                                                                            <i class="bi bi-x-lg"></i> Disabled
                                                                        </span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No permissions available</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>
