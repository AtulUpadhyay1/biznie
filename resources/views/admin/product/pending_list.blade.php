<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>
                        {{ $page_title }}
                        <span class="badge bg-secondary rounded-pill fs-6 ms-1">{{ $list->count() }}</span>
                    </h4>
                    <div class="bz-toolbar">
                        <div class="bz-toolbar-field">
                            <label class="bz-filter-label" for="dashboardDateInput">Filter by date</label>
                            <div class="input-group flatpickr" id="dashboardDate">
                                <span class="input-group-text" data-toggle><i class="bi bi-calendar"></i></span>
                                <input type="text" id="dashboardDateInput" class="form-control"
                                    placeholder="Select date" data-input>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm">
                            <i class="bi bi-cloud-download"></i>
                            Download Report
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-horizontal filter-list-group">
                        <li class="list-group-item border-0">
                            <form class="custom-search-bar">
                                <label class="bz-filter-label" for="product_filter_search">Search products</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="product_filter_search" class="form-control" placeholder="Search here...">
                                </div>
                            </form>
                        </li>
                        <li class="list-group-item border-0">
                            <label class="bz-filter-label" for="product_filter_category">Category</label>
                            <select id="product_filter_category" class="form-select">
                                <option>Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <label class="bz-filter-label" for="product_filter_sub_category">Sub Category</label>
                            <select id="product_filter_sub_category" class="form-select">
                                <option>Sub Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <label class="bz-filter-label" for="product_filter_business_category">Business Category</label>
                            <select id="product_filter_business_category" class="form-select">
                                <option>Business Category</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <label class="bz-filter-label" for="product_filter_seller_type">Seller Type</label>
                            <select id="product_filter_seller_type" class="form-select">
                                <option>Seller Type</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <label class="bz-filter-label" for="product_filter_sort_by">Sort By</label>
                            <select id="product_filter_sort_by" class="form-select">
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
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <img src="{{imageUrl($data->thumbnail)}}" alt="image" >
                                            {{ $data->name }}
                                        </td>
                                        <td><b>{{ $data->getBusiness->name }}</b></td>
                                        <td> 0 </td>
                                        <td> {{ $data->current_stock }} </td>
                                        <td><b class="bz-num">RS {{ $data->unit_price }}</b></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-inverse-success" wire:click="updateRequestStatus({{$data->id}}, 'approved')"><i class="bi bi-check-lg"></i>Approve</button>
                                            <button class="btn btn-sm btn-inverse-danger" wire:click="updateRequestStatus({{$data->id}}, 'rejected')"><i class="bi bi-x-lg"></i>Reject</button>
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
