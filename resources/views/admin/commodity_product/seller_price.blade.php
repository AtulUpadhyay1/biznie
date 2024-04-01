<div>
    @section('title', config('app.name') . ' | '.$page_title)
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
                            <a href="{{route('admin.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="accordion" id="state_price">
                            @foreach ($list as $data)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading_{{ $data->id }}">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $data->id }}" aria-expanded="{{ $loop->index == 0 ? true : false}}" aria-controls="collapse_{{ $data->id }}">
                                        <b>{{ $data->getUser->name}}</b>, &nbsp;<b>Brand</b> : {{ $data->getBrand->name }}, &nbsp;<b>State</b> : {{ $data->getStatePrice[0]->state }}, &nbsp;<b>City</b> : {{ $data->getStatePrice[0]->city }}
                                        </button>
                                    </h2>
                                    <div id="collapse_{{ $data->id }}" class="accordion-collapse collapse {{ $loop->index == 0 ? 'show' : ''}}" aria-labelledby="heading_{{ $data->id }}" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            @php
                                                                $attributes = $data->getStatePrice[0]->value;
                                                            @endphp
                                                            @foreach ($attributes as $attribute)
                                                                <th>
                                                                    {{$attribute['name']}}
                                                                </th>
                                                            @endforeach
                                                            <th>Gauge Difference</th>
                                                            <th>Stock</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->getStatePrice as $state_price)
                                                            <tr>
                                                                <th>
                                                                    {{ $loop->iteration }}
                                                                    @if($state_price->is_selected)
                                                                        <i class="bi bi-check2-circle text-success fs-5"></i>
                                                                    @endif
                                                                </th>
                                                                @foreach ($state_price->value as $price_value)
                                                                    <td> {{ $price_value['value'] }} </td>
                                                                @endforeach
                                                                <td> {{ $state_price->price }} </td>
                                                                <td> {{ $state_price->stock ?? 0 }} </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
