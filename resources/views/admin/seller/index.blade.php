<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>All Sellers</h4>
                        </div>
                        <div class="col-6 text-end">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                {{-- <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                                    <span class="input-group-text input-group-addon bg-transparent border-danger"
                                        data-toggle><i data-feather="calendar" class="text-danger"></i></span>
                                    <input type="text" class="form-control bg-transparent border-danger"
                                        placeholder="Select date" data-input>
                                </div>
                                <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud btn-icon-prepend"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
                                    Download Report
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-horizontal filter-list-group">
                        <li class="list-group-item border-0">
                            <form class="custom-search-bar">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                                </div>
                            </form>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option>Type</option>
                                <option>Dairy</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>City</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>State</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>Pincode</option>
                            </select>
                        </li>
                        <li class="list-group-item border-0">
                            <select class="form-select">
                                <option selected disabled>Status</option>
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
                                    <th style="width: 25%">Seller Details</th>
                                    <th style="width: 30%;">Business Details</th>
                                    <th>Activity Details</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data )
                                    <tr>
                                        <th>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</th>
                                        <td>
                                            <b>User Name:</b>
                                            <span>{{ $data->name }}</span>
                                            <br>
                                            <i class="bi bi-telephone"></i><span class="ms-2">{{ $data->phone }}</span>
                                            <br>
                                            <i class="bi bi-envelope-at"></i><span class="ms-2">{{ $data->email }}</span>
                                        </td>
                                        <td>
                                            <b>Business Name:</b>
                                            <span class="text-primary"> {{ $data->getBusiness->name }} </span>
                                            @if ($data->getSellerKycDetail)
                                                @if ($data->getSellerKycDetail->status == 'uploaded' || $data->getSellerKycDetail->status == 'pending')
                                                    <i class="bi bi-stopwatch-fill text-warning"></i>
                                                @elseif ($data->getSellerKycDetail->status == 'approved')
                                                    <i class="bi bi-patch-check-fill text-success"></i>
                                                @elseif (($data->getSellerKycDetail->status == 'rejected'))
                                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                                @endif
                                            @endif
                                            <br>
                                            <b>Business Category:</b>
                                            <span class="text-success">Textile</span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Cash Balance"><i class="bi bi-cash-stack"></i> : <b>
                                                    <i class="bi bi-currency-rupee"></i>
                                                    {{ formatIndianNumber($data->cash_balance) }}</b>
                                            </span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Credit Balance"><i class="bi bi-credit-card-2-front"></i> : <b>
                                                    <i class="bi bi-currency-rupee"></i>
                                                    {{ formatIndianNumber($data->credit_balance) }}</b>
                                                </span>
                                            <br>
                                            <span class="pe-2 border-end" data-bs-toggle="tooltip"
                                                title="Product Enquiry"><i class="bi bi-cart"></i> :<b> {{ $data->getSellerProductEnquiries->count() }}</b></span>
                                            <span class="pe-2" data-bs-toggle="tooltip" title="Total Orders"><i
                                                    class="bi bi-cart-check"></i>: <b> {{ $data->getSellerOrders->count() }} </b></span>
                                        </td>
                                        <td>
                                            <b>Registration Date:</b>
                                            {{ dateFormat($data->getBusiness->created_at) }} <br>
                                            <b>Updation Date:</b>
                                            {{ dateFormat($data->getBusiness->updated_at) }} <br>
                                            <b>Last Active:</b>
                                            {{ lastActive($data->id) }}
                                        </td>
                                        <td>
                                            <b>Pincode:</b>
                                            <span>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->postal_code : '' }}</span>
                                            <br>
                                            <b>Area:</b>
                                            <span>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->address : '' }}</span>
                                            <br>
                                            <b>City:</b>
                                            <span>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->city : '' }}</span>
                                            <br>
                                            <b>State:</b>
                                            <span>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->state : '' }}</span>
                                            <br>
                                            <b>Country</b>
                                            <span>{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->country : '' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.seller-kyc-detail', $data->id)}}" wire:navigate><i
                                                        class="bi bi-eye icon-sm me-2"></i><span>Kyc Detail</span></a>
                                                {{-- <a href="{{route('admin.edit-seller')}}" wire:navigate
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a> --}}
                                                <a href="{{route('admin.tag-priority', $data->id)}}" wire:navigate
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-bookmarks icon-sm me-2"></i><span>Tag & Priority</span></a>
                                                {{-- <a href="javascript:;"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a> --}}
                                                <a href="{{route('admin.seller-product.index', $data->id)}}" class="dropdown-item d-flex align-items-center"><i class="bi bi-box icon-sm me-2"></i><span>Product Section</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{$list->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
