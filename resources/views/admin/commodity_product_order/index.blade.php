<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>
                        {{ $page_title }}
                        <span class="badge bg-secondary rounded-pill ms-1">{{$total}}</span>
                    </h4>
                    <div class="bz-toolbar">
                        <div class="custom-search-bar">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <label class="bz-filter-label" for="search">Search</label>
                                <input type="text" id="search" class="form-control" placeholder="Search here..." wire:model.live="search">
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
                                    <th>Order Id</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Seller</th>
                                    <th>Transporter</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Amount Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->getProductEnquiry->unique_id }}</div>
                                            <div class="bz-cell-sub">({{ $data->order_id }})</div>
                                        </td>
                                        <td>{{ $data->getBrand->name }}</td>
                                        <td>{{ $data->getCommodityProduct->name }}</td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->getCustomer?->getUserDetail?->company_name ?? '--' }}</div>
                                            <div class="bz-cell-sub">{{ $data->getCustomer->name }}</div>
                                        </td>
                                        <td>
                                            <div class="bz-cell-title">{{ $data->getSeller?->getBusiness?->name ?? '--' }}</div>
                                            <div class="bz-cell-sub">{{ $data->getSeller->name }}</div>
                                        </td>
                                        <td>{{ $data->getTransporter ? $data->getTransporter->name : 'NA' }}</td>
                                        <td>
                                            <span class="bz-status {{ $data->status == 'cancel' ? 'bz-status--danger' : ($data->status == 'delivered' ? 'bz-status--success' : ($data->status == 'pending' ? 'bz-status--warning' : 'bz-status--info')) }}">{{ ucfirst($data->status) }}</span>
                                        </td>
                                        <td>{{ dateTimeFormat($data->created_at) }}</td>
                                        <td>
                                            <dl class="bz-kv-list">
                                                <div>
                                                    <dt>Total Amount</dt>
                                                    <dd>₹ {{ formatIndianNumber($data->buyer_invoice_amount ?? $data->total_amount) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Paid Amount</dt>
                                                    <dd>₹ {{ formatIndianNumber($data->paid_amount) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Remaining Amount</dt>
                                                    <dd>₹ {{ formatIndianNumber($data->buyer_invoice_amount) ? formatIndianNumber($data->buyer_invoice_amount - $data->paid_amount) : formatIndianNumber($data->due_amount) }}</dd>
                                                </div>
                                            </dl>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="bz-row-action">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item" href="{{route('admin.commodity-product-order.show', $data->id)}}" wire:navigate><i class="bi bi-eye"></i><span>View</span></a>
                                                <a class="dropdown-item" href="{{route('admin.commodity-product-order.status', $data->id)}}" wire:navigate><i class="bi bi-device-ssd"></i><span>Update Status</span></a>
                                                <a class="dropdown-item" href="{{route('admin.commodity-product-order.history', $data->id)}}" wire:navigate><i class="bi bi-clock-history"></i><span>History</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="11" />
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
