<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.seller.seller_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Seller Order List</h4>
                </div>
                <div class="card-body">
                    {{-- <p class="h6">
                        <b>Total RFQ | 0</b> &nbsp; &nbsp;
                        <b>Total Order | 0</b> &nbsp; &nbsp;
                        <b>Total Replied | 0</b> &nbsp; &nbsp;
                        <b>Total Not Replied | 0</b> &nbsp; &nbsp;
                        <b>Total Delivered | 0</b> &nbsp; &nbsp;
                        <b>Total Dispatched | 0</b> &nbsp; &nbsp;
                        <b>Total In-transit | 0</b> &nbsp; &nbsp;
                        <b>Total Deals | 0</b>
                    </p> --}}

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
                                            {{ $data->getProductEnquiry->unique_id }}
                                            <br>
                                            <small>({{ $data->order_id }})</small>
                                        </td>
                                        <td>{{ $data->getCommodityProduct->name }}</td>
                                        <td><b>RS {{ $data->total_amount }}</b></td>
                                        <td>
                                            <span
                                                class="bz-status {{ in_array($data->status, ['delivered', 'completed', 'paid']) ? 'bz-status--success' : (in_array($data->status, ['cancelled', 'rejected', 'failed']) ? 'bz-status--danger' : 'bz-status--info') }}">{{ ucfirst($data->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" class="bz-row-action" id="actionBtn{{ $data->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="actionBtn{{ $data->id }}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product-order.show', $data->id)}}" wire:navigate><i class="bi bi-eye me-2"></i><span>View</span></a>
                                                {{-- <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down me-2"></i><span>Download</span></a> --}}
                                                {{-- <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash me-2"></i><span>Delete</span></a> --}}
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
