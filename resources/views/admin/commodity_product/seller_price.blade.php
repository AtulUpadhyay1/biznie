<div>
    @section('title', config('app.name') . ' | ' . $page_title)
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
                            <a href="{{ route('admin.commodity-product.index') }}"
                                class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i
                                    class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="accordion" id="state_price">
                            @foreach ($list as $data)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading_{{ $data->id }}">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse_{{ $data->id }}"
                                            aria-expanded="false" aria-controls="collapse_{{ $data->id }}">
                                            <b>{{ $data->getUser->getBusiness->name }}
                                                <small>({{ $data->getUser->name }} -
                                                    {{ $data->getUser->phone }})</small></b>, &nbsp;<b>Brand</b> :
                                            {{ $data->getBrand->name }}, &nbsp;<b>State</b> :
                                            {{ $data->getStatePrice[0]->state }}, &nbsp;<b>City</b> :
                                            {{ $data->getStatePrice[0]->city }}, &nbsp;<b>Base Price</b> :
                                            {{ $data->base_price ?? 0 }}
                                        </button>
                                    </h2>
                                    <div id="collapse_{{ $data->id }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading_{{ $data->id }}"
                                        data-bs-parent="#accordionExample">
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
                                                                    {{ $attribute['name'] }}
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
                                                                    @if ($state_price->is_selected)
                                                                        <i
                                                                            class="bi bi-check2-circle text-success fs-5"></i>
                                                                    @endif
                                                                </th>
                                                                @foreach ($state_price->value as $price_value)
                                                                    <td> {{ $price_value['value'] }} </td>
                                                                @endforeach
                                                                <td> {{ $state_price->price }} </td>
                                                                <td> {{ $state_price->stock ?? 0 }} </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <th colspan="{{ count($attributes) + 2 }}"
                                                                class="text-start">Loading Charge</th>
                                                            <td>{{ $data->loading_charge ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="{{ count($attributes) + 2 }}"
                                                                class="text-start">
                                                                Insurance Charge</th>
                                                            <td>{{ $data->insurance_charge ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="{{ count($attributes) + 2 }}"
                                                                class="text-start">
                                                                Quality Charge</th>
                                                            <td>{{ $data->quality_charge ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="{{ count($attributes) + 2 }}"
                                                                class="text-start">
                                                                GST</th>
                                                            <td>{{ $data->gst ?? 0 }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="{{ count($attributes) + 2 }}"
                                                                class="text-start">
                                                                TCS</th>
                                                            <td>{{ $data->tcs ?? 0 }}</td>
                                                        </tr>
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
