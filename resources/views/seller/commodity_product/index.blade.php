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
                            <a href="{{route('seller.commodity-product.create')}}" class="btn btn-primary btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-plus-lg btn-icon-prepend"></i>Add New Product</a>
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
                                    <th>Status</th>
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
                                        <td> ₹ {{ $data->base_price }} </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input featured_update"
                                                    {{-- wire:click="updateFeatured({{ $data->id }})" --}}
                                                    {{ $data->status == 'active' ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-light btn-xs px-2">
                                                <i class="bi bi-three-dots-vertical icon-lg text-dark"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('seller.commodity-product.show', $data->id)}}" wire:navigate><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('seller.commodity-product.edit', $data->id)}}" wire:navigate><i class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('seller.commodity-product.price', $data->id)}}" wire:navigate><i class="bi bi-currency-rupee icon-sm me-2"></i><span>Pricing & others</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('seller.commodity-product.variation', $data->id)}}" wire:navigate><i class="bi bi-cart-plus icon-sm me-2"></i><span>Variation</span></a>
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

