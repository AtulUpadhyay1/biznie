<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.customer_list.customer_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>All Orders</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Order Id</th>
                                    <th>Products</th>
                                    <th style="width: 20%">Amount</th>
                                    <th>Status</th>
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
                                        <td>{{ $data->getCommodityProduct?->name }}</td>
                                        <td><b class="bz-num">RS {{ $data->total_amount }}</b></td>
                                        <td>
                                            <span class="bz-status {{ in_array($data->status, ['delivered', 'completed', 'paid']) ? 'bz-status--success' : (in_array($data->status, ['cancelled', 'rejected', 'failed']) ? 'bz-status--danger' : 'bz-status--info') }}">{{ ucfirst($data->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" class="bz-row-action" id="actionBtn{{ $data->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="actionBtn{{ $data->id }}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product-order.show', $data->id)}}" wire:navigate><i class="bi bi-eye me-2"></i><span>View</span></a>
                                            </div>
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
</div>
