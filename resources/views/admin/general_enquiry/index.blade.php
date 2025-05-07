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
                            </h4>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <div class="custom-search-bar">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Search here..." wire:model.live="search">
                                    </div>
                                </div>
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
                                    <th>Unique Id</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>{{ $data->unique_id }}</td>
                                        <td>{{ $data->getBrand?->name }}</td>
                                        <td>{{ $data->getSellerCommodityProduct?->name}}</td>
                                        <td>
                                            {{ $data->company_name }}<br>
                                            <small>{{ $data->name }}</small>
                                        </td>
                                        <td>{{ dateTimeFormat($data->created_at) }}</td>
                                        <td>
                                            {{ ucfirst($data->status) }}
                                            @if ($data->status == 'ordered' && $data->getCommodityProductOrder)
                                                <br><small><a href="{{route('admin.commodity-product-order.show', $data->getCommodityProductOrder->id)}}" wire:navigate>{{ $data->getCommodityProductOrder->order_id }}</a></small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-light btn-xs px-2">
                                                <i class="bi bi-three-dots-vertical icon-lg text-dark"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.general-enquiry.show', $data->id)}}" wire:navigate><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="float-end">
                            {{ $list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
