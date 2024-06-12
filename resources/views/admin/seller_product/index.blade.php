<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Base Price</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->getBrand ? $data->getBrand->name : '--' }} </td>
                                        <td>{{ isset($data->getStatePrice[0]) ? $data->getStatePrice[0]->state : '--' }}</td>
                                        <td>{{ isset($data->getStatePrice[0]) ? $data->getStatePrice[0]->city : '--' }}</td>
                                        <td>{{ $data->base_price }}</td>
                                        <td>{{ $data->updated_at }}</td>
                                        <td>
                                            <a type="button" id="ActionBtn_{{$data->id}}" data-bs-toggle="dropdown" role="button"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical icon-lg text-muted pb-3px"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="ActionBtn_{{$data->id}}">
                                                <a href="{{route('admin.seller-product.edit', [$data->user_id, $data->id])}}" class="dropdown-item d-flex align-items-center" wire:navigate><i class="bi bi-pencil-square icon-sm me-2"></i><span>Edit</span></a>
                                                <a href="{{route('admin.seller-product.variation', [$data->user_id, $data->id])}}" class="dropdown-item d-flex align-items-center" wire:navigate><i class="bi bi-card-checklist icon-sm me-2"></i><span>Variant</span></a>
                                                <a href="{{route('admin.seller-product.price', [$data->user_id, $data->id])}}" class="dropdown-item d-flex align-items-center" wire:navigate><i class="bi bi-currency-rupee icon-sm me-2"></i><span>Update Price</span></a>
                                                <a href="{{route('admin.seller-product.stock', [$data->user_id, $data->id])}}" class="dropdown-item d-flex align-items-center" wire:navigate><i class="bi bi-database icon-sm me-2"></i><span>Update Stock</span></a>

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
