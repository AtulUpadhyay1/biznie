<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>
                        {{ $page_title }}
                        <span class="badge bg-secondary rounded-pill fs-6 ms-1">{{$total}}</span>
                    </h4>

                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <label class="bz-filter-label" for="commodity_enquiry_search">Search enquiries</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="commodity_enquiry_search" class="form-control" placeholder="Search here..." wire:model.live="search">
                            </div>
                        </div>
                        <a type="button" class="btn btn-danger btn-sm" title="add" href="{{route('admin.commodity-product-enquiry.create')}}" wire:navigate>
                            <i class="bi bi-plus-lg"></i>
                            Add
                        </a>
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
                                        <td>{{ $data->getBrand->name }}</td>
                                        <td>{{ $data->getCommodityProduct->name}}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->getUser?->getUserDetail?->company_name ?? '--' }}</div>
                                            <div class="bz-cell-sub">{{ $data->getUser->name }}</div>
                                        </td>
                                        <td>{{ dateTimeFormat($data->created_at) }}</td>
                                        <td>
                                            <span class="bz-status {{ $data->status == 'ordered' ? 'bz-status--success' : 'bz-status--info' }}">{{ ucfirst($data->status) }}</span>
                                            @if ($data->status == 'ordered' && $data->getCommodityProductOrder)
                                                <br><small><a href="{{route('admin.commodity-product-order.show', $data->getCommodityProductOrder->id)}}" wire:navigate>{{ $data->getCommodityProductOrder->order_id }}</a></small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="bz-row-action">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.commodity-product-enquiry.show', $data->id)}}" wire:navigate><i class="bi bi-eye"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.commodity-product-enquiry.sellerReply', $data->id)}}" wire:navigate><i class="bi bi-reply-all"></i><span>Seller Reply</span></a>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.commodity-product-enquiry.manualPriceEntry', $data->id)}}" wire:navigate><i class="bi bi-pencil-square"></i><span>Live Bidding / Manual Price</span></a>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.commodity-product-enquiry.history', $data->id)}}" wire:navigate><i class="bi bi-clock-history"></i><span>History</span></a>
                                                @if ($data->status == 'Seller Marked')
                                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.commodity-product-enquiry.convertToOrder', $data->id)}}" wire:navigate><i class="bi bi-cart-check"></i><span>Convert To Order</span></a>
                                                    <button class="dropdown-item d-flex align-items-center gap-2" wire:click="processOverPhone({{$data->id}})"><i class="bi bi-cloud-haze2"></i><span>Process Over Phone</span></button>
                                                @endif
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
