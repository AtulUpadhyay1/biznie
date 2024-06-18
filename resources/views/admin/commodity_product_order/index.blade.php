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
                                <span class="badge bg-secondary rounded-pill fs-6 ms-1">{{$total}}</span>
                            </h4>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <form class="custom-search-bar me-3 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <span class="input-group-text"> <i data-feather="search"></i></span>
                                        <input type="text" class="form-control" placeholder="Search here...">
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
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
                                    <th>#</th>
                                    <th>Unique Id</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Seller</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>{{ $data->order_id }}</td>
                                        <td>{{ $data->getBrand->name }}</td>
                                        <td>{{ $data->getCommodityProduct->name }}</td>
                                        <td>{{ $data->getCustomer->name }}</td>
                                        <td>{{ $data->getSeller->name }}</td>
                                        <td>
                                            {{ ucfirst($data->status) }}
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-light btn-xs px-2">
                                                <i class="bi bi-three-dots-vertical icon-lg text-dark"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product-order.show', $data->id)}}" wire:navigate><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product-order.status', $data->id)}}" wire:navigate><i class="bi bi-device-ssd icon-sm me-2"></i><span>Update Status</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product-order.history', $data->id)}}" wire:navigate><i class="bi bi-clock-history icon-sm me-2"></i><span>History</span></a>
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
