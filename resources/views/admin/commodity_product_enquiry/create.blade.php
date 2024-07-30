<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4> {{ $page_title }} </h4>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <form class="custom-search-bar me-3 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <span class="input-group-text"> <i data-feather="search"></i></span>
                                        <input type="text" class="form-control" placeholder="Search here...">
                                    </div>
                                </form>
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
                                    <th>Brand</th>
                                    <th>Location</th>
                                    <th>Base Price</th>
                                    <th>Updated On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 + ($list->currentPage() - 1) * $list->perPage() }}</td>
                                        <td>
                                            @if ($data->thumbnail)
                                                <img src="{{imageUrl($data->thumbnail)}}" alt="image" >
                                            @else
                                                <img src="{{asset('common/images/no-photo.png')}}" alt="image" >
                                            @endif
                                            {{ $data->getCommodityProduct->name }}
                                        </td>
                                        <td>{{ $data->getBrand->name }}</td>
                                        <td>{{ $data->city }}</td>
                                        <td><b class="text-sucess">₹ {{ $data->base_price }}</b> </td>
                                        <td>{{ dateTimeFormat($data->updated_at) }}</td>
                                        <td>
                                            <a class="btn btn-light btn-sm" href="{{route('admin.commodity-product-enquiry.variation', $data->id)}}" wire:navigate>Select</a>
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
