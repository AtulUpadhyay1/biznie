<div>
    @section('title', config('app.name') . ' | '.$page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>Products <span class="badge bg-secondary rounded-pill fs-6 ms-1">{{$list->count()}}</span></h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('seller.product.create')}}" class="btn btn-primary btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-plus-lg btn-icon-prepend"></i>Add New Product</a>
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
                                    <th>SL</th>
                                    <th>Product Name</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                    <th>Verify Status</th>
                                    <th>Active Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data )
                                    <tr>
                                        <th>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</th>
                                        <td>
                                            <img src="{{imageUrl($data->thumbnail)}}" alt="image" >
                                            {{ $data->name }}
                                        </td>
                                        <td> {{ $data->purchase_price }} </td>
                                        <td> {{ $data->unit_price }} </td>
                                        <td> {{ $data->request_status }} </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input featured_update"
                                                    wire:click="updateFeatured({{ $data->id }})"
                                                    {{ $data->featured == 1 ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.seller-kyc-detail', $data->id)}}" wire:navigate><i
                                                        class="bi bi-eye icon-sm me-2"></i><span>Kyc Detail</span></a>
                                                <a href="{{route('admin.edit-seller')}}" wire:navigate
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a href="javascript:;"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a>
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
