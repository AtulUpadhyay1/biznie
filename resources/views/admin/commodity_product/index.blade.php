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
                                <span class="badge bg-secondary rounded-pill fs-6 ms-1">{{$total}}</span>
                            </h4>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <form class="custom-search-bar me-3 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <span class="input-group-text"> <i data-feather="search"></i></span>
                                        <input type="text" class="form-control" placeholder="Search here...">
                                    </div>
                                </form>
                                <a type="button" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="add" href="{{route('admin.commodity-product.create')}}" wire:navigate>
                                    <i class="bi bi-plus-lg btn-icon-prepend"></i>
                                    Add
                                </a>
                            </div>
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
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>
                                            <img src="{{imageUrl($data->thumbnail)}}" alt="image" >
                                            {{ $data->name }}
                                        </td>
                                        <td>{{ $data->getCategory->name }}</td>
                                        <td>
                                            @if ($data->base_price)
                                                <b class="text-sucess">₹ {{ $data->base_price }}</b>
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
                                            <a type="button" id="ActionBtn{{$data->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-light btn-xs px-2">
                                                <i class="bi bi-three-dots-vertical icon-lg text-dark"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn{{$data->id}}">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.show', $data->id)}}" wire:navigate><i class="bi bi-eye icon-sm me-2"></i><span>View</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.edit', $data->id)}}" wire:navigate><i class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.price', $data->id)}}" wire:navigate><i class="bi bi-currency-rupee icon-sm me-2"></i><span>Pricing & others</span></a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.variation', $data->id)}}" wire:navigate><i class="bi bi-cart-plus icon-sm me-2"></i><span>Variation</span></a>
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="{{route('admin.commodity-product.quality', $data->id)}}" wire:navigate><i class="bi bi-tags icon-sm me-2"></i><span>Quality</span></a> --}}
                                                {{-- <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-copy icon-sm me-2"></i><span>Duplicate</span></a>
                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-trash icon-sm me-2"></i><span>Delete</span></a> --}}
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
