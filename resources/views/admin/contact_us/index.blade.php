<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>

                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <label class="bz-filter-label" for="contact_us_search">Search messages</label>
                            <div class="input-group">
                                <span class="input-group-text"> <i class="bi bi-search"></i></span>
                                <input type="text" id="contact_us_search" class="form-control"
                                    placeholder="Search here..." wire:model.live.debounce.400ms="search">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr wire:key="query-{{ $data->id }}">
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->phone }}</td>
                                        <td>
                                            {{-- Bootstrap's own delegated handler opens this. The page is
                                                 reached through wire:navigate, which never fires
                                                 DOMContentLoaded, so the hand-rolled listener this
                                                 replaces was never attached and the button did nothing. --}}
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="modal" data-bs-target="#queryModal{{ $data->id }}">
                                                View
                                            </button>
                                        <div class="modal fade" id="queryModal{{ $data->id }}" tabindex="-1"
                                            aria-labelledby="queryModalLabel{{ $data->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="queryModalLabel{{ $data->id }}">
                                                            Message from {{ $data->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <dl class="row mb-3">
                                                            <dt class="col-sm-3">Name</dt>
                                                            <dd class="col-sm-9">{{ $data->name ?: '--' }}</dd>

                                                            <dt class="col-sm-3">Email</dt>
                                                            <dd class="col-sm-9">
                                                                @if ($data->email)
                                                                    <a href="mailto:{{ $data->email }}">{{ $data->email }}</a>
                                                                @else
                                                                    --
                                                                @endif
                                                            </dd>

                                                            <dt class="col-sm-3">Phone</dt>
                                                            <dd class="col-sm-9">
                                                                @if ($data->phone)
                                                                    <a href="tel:{{ $data->phone }}">{{ $data->phone }}</a>
                                                                @else
                                                                    --
                                                                @endif
                                                            </dd>

                                                            <dt class="col-sm-3">Received</dt>
                                                            <dd class="col-sm-9">{{ dateTimeFormat($data->created_at) }}</dd>
                                                        </dl>

                                                        <p class="mb-1 fw-semibold">Message</p>
                                                        <p class="mb-0" style="white-space: pre-line">{{ $data->message }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </td>
                                        <td>{{ dateTimeFormat($data->created_at) }}</td>
                                    </tr>

                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="bz-pagination">
                            {{ $list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
