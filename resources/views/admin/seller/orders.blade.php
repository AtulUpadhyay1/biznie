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
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 card-title">
                            <h5 class="mt-2">Seller Order List</h5>
                        </div>
                        <div class="col-8 text-end">

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="h6">
                        <b>Total RFQ | 0</b> &nbsp; &nbsp;
                        <b>Total Order | 0</b> &nbsp; &nbsp;
                        <b>Total Replied | 0</b> &nbsp; &nbsp;
                        <b>Total Not Replied | 0</b> &nbsp; &nbsp;
                        <b>Total Delivered | 0</b> &nbsp; &nbsp;
                        <b>Total Dispatched | 0</b> &nbsp; &nbsp;
                        <b>Total In-transit | 0</b> &nbsp; &nbsp;
                        <b>Total Deals | 0</b>
                    </p>

                    <div class="table-responsive mt-2">
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
                                @foreach ($list as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>
                                            {{ $data->getProductEnquiry->unique_id }}
                                            <br>
                                            <small>({{ $data->order_id }})</small>
                                        </td>
                                        <td>{{ $data->getCommodityProduct->name }}</td>
                                        <td><b>RS {{ $data->total_amount }}</b></td>
                                        <td class="fw-bolder">{{ ucfirst($data->status) }}</td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn" style="">
                                                <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down icon-sm me-2"></i><span>Download</span></a>
                                                {{-- <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
