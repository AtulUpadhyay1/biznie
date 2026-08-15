<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4> {{ $page_title }} </h4>

                    <div class="bz-toolbar">
                        <form class="custom-search-bar">
                            <label class="bz-filter-label" for="enquiry_product_search">Search products</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="enquiry_product_search" class="form-control" placeholder="Search here...">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
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
                                        <td><b class="bz-num">₹ {{ $data->base_price }}</b> </td>
                                        <td>{{ dateTimeFormat($data->updated_at) }}</td>
                                        <td>
                                            <a class="btn btn-secondary btn-sm" href="{{route('admin.commodity-product-enquiry.variation', $data->id)}}" wire:navigate>Select</a>
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
