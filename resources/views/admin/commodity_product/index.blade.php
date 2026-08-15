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
                            <label class="bz-filter-label" for="commodity_product_search">Search products</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="commodity_product_search" class="form-control" placeholder="Search here..." wire:model.live="search">
                            </div>
                        </div>
                        <a type="button" class="btn btn-danger btn-sm" title="add" href="{{route('admin.commodity-product.create')}}" wire:navigate>
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
                                    <th>Id</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Base Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>
                                            <img src="{{imageUrl($data->thumbnail)}}" alt="image" >
                                            {{ $data->name }}
                                        </td>
                                        <td>{{ $data->getCategory->name }}</td>
                                        <td>
                                            @if ($data->base_price)
                                                <b class="bz-num">₹ {{ $data->base_price }}</b>
                                            @else
                                                <a href="{{route('admin.commodity-product.price', $data->id)}}" wire:navigate> Set Price</a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" {{ $data->status == 'active' ? 'checked' : '' }}  wire:change="updateStatus({{ $data->id }})">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="bz-row-action">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.show', $data->id)}}" wire:navigate><i class="bi bi-eye me-2"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.edit', $data->id)}}" wire:navigate><i class="bi bi-pencil-square me-2"></i><span>Edit</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.price', $data->id)}}" wire:navigate><i class="bi bi-currency-rupee me-2"></i><span>Pricing & others</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.variation', $data->id)}}" wire:navigate><i class="bi bi-cart-plus me-2"></i><span>Variation</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.quality', $data->id)}}" wire:navigate><i class="bi bi-tags me-2"></i><span>Quality</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.statePrice', $data->id)}}" wire:navigate><i class="bi bi-geo-alt me-2"></i><span>Add State Price</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.sellerPrice', $data->id)}}" wire:navigate><i class="bi bi-bar-chart me-2"></i><span>Seller Prices</span></a>
                                                {{-- <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-copy me-2"></i><span>Duplicate</span></a>
                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash me-2"></i><span>Delete</span></a>--}}
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
