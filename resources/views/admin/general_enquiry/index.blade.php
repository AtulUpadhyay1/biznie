<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>

                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <label class="bz-filter-label" for="general_enquiry_search">Search enquiries</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="general_enquiry_search" class="form-control" placeholder="Search here..." wire:model.live="search">
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
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>{{ $data->unique_id }}</td>
                                        <td>{{ $data->getBrand?->name }}</td>
                                        <td>{{ $data->getSellerCommodityProduct?->name}}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->company_name }}</div>
                                            <div class="bz-cell-sub">{{ $data->name }}</div>
                                        </td>
                                        <td>{{ dateTimeFormat($data->created_at) }}</td>
                                        <td>
                                            <span class="bz-status {{ $data->status == 'cancel' ? 'bz-status--danger' : ($data->status == 'pending' ? 'bz-status--warning' : 'bz-status--success') }}">{{ ucfirst($data->status) }}</span>
                                            @if ($data->status == 'ordered' && $data->getCommodityProductOrder)
                                                <br><small><a href="{{route('admin.commodity-product-order.show', $data->getCommodityProductOrder->id)}}" wire:navigate>{{ $data->getCommodityProductOrder->order_id }}</a></small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a class="bz-row-action" type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item" href="{{route('admin.general-enquiry.show', $data->id)}}" wire:navigate><i class="bi bi-eye"></i><span>View</span></a>

                                            </div>
                                        </td>
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
