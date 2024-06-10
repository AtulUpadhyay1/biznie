<div>
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @php
                                        $attributes = $list[0]->value;
                                    @endphp
                                    @foreach ($attributes as $attribute)
                                        <th>
                                            {{$attribute['name']}}
                                        </th>
                                    @endforeach
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $data)
                                    <tr>
                                        <th>
                                            {{ $loop->iteration }}
                                            @if($data->is_selected)
                                                <i class="bi bi-check2-circle text-success fs-5"></i>
                                            @endif
                                        </th>
                                        @foreach ($data->value as $price_value)
                                            <td> {{ $price_value['value'] }} </td>
                                        @endforeach
                                        <td>
                                            <input type="number" class="form-control form-control-sm" value="{{$data->price}}">

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
