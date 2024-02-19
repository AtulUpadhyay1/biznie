<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>
                                {{ $page_title }}
                                <span class="badge bg-secondary rounded-pill fs-6 ms-1">{{ $list->count() }}</span>
                            </h4>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                                    <span class="input-group-text input-group-addon bg-transparent border-danger"
                                        data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                                    <input type="text" class="form-control bg-transparent border-danger"
                                        placeholder="Select date" data-input>
                                </div>
                                <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                    <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                                    Download Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-horizontal filter-list-group">
                        <li class="list-group-item border-0">
                            <form class="custom-search-bar">
                                <div class="input-group">
                                    <span class="input-group-text"> <i data-feather="search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search here...">
                                </div>
                            </form>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Sub Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Business Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Seller Type</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Sort By</option>
                            </select>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Product Name</th>
                                    <th>Business Name</th>
                                    <th>Number Of Sale</th>
                                    <th>Total Stock</th>
                                    <th>Base Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>
                                            <img src="{{imageUrl($data->thumbnail)}}" alt="image" >
                                            {{ $data->name }}
                                        </td>
                                        <td><b class="text-primary"> {{ $data->getBusiness->name }} </b></td>
                                        <td> 0 </td>
                                        <td> {{ $data->current_stock }} </td>
                                        <td><b class="text-sucess">RS {{ $data->unit_price }}</b></td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" wire:click="updateRequestStatus({{$data->id}}, 'approved')">Approve</button>
                                            <button class="btn btn-danger btn-sm" wire:click="updateRequestStatus({{$data->id}}, 'rejected')">Reject</button>
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
