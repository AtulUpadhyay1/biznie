<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{$page_title}}</h4>
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="product_id" class="form-label">Commodity Product</label>
                            <select class="form-select" id="product_id" wire:model="product_id" wire:change="search()">
                                <option value="">Select Product</option>
                                @foreach ($product_list as $product_data)
                                    <option value="{{ $product_data->id }}">{{ $product_data->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-select" id="brand_id" wire:model="brand_id" wire:change="search()">
                                <option value="">Select Brand</option>
                                @foreach ($brand_list ?? [] as $brand_data)
                                    <option value="{{ $brand_data->id }}">{{ $brand_data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                  <a class="nav-link active" id="seller-tab" data-bs-toggle="tab" href="#seller" role="tab" aria-controls="seller" aria-selected="true">Seller</a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" id="buyer-tab" data-bs-toggle="tab" href="#buyer" role="tab" aria-controls="buyer" aria-selected="false">Buyer</a>
                                </li>
                            </ul>
                            <div class="tab-content border border-top-0 p-3" id="myTabContent">
                                <div class="tab-pane fade show active" id="seller" role="tabpanel" aria-labelledby="seller-tab">
                                    <div class="accordion" id="state_price_accordion">
                                        @forelse ($seller_list ?? [] as $seller_data)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="seller_heading_{{ $seller_data->id }}">

                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#seller_collapse_{{ $seller_data->id }}" aria-expanded="false" aria-controls="seller_collapse_{{ $seller_data->id }}">
                                                    <b>{{ $seller_data->getUser->getBusiness->name}} <small>({{$seller_data->getUser->name}} - {{$seller_data->getUser->phone}}) ({{$seller_data->getUser?->getUserDetail?->priority ?? 0}}⭐)</small></b>,
                                                    &nbsp;<b>Brand</b> : {{ $seller_data->getBrand->name }},
                                                    &nbsp;<b>State</b> : {{ $seller_data->getStatePrice[0]->state }},
                                                    &nbsp;<b>City</b> : {{ $seller_data->getStatePrice[0]->city }},
                                                    &nbsp;<b>Base Price</b> : {{ $seller_data->base_price ?? 0 }},
                                                    @if($seller_data->price_validity) &nbsp;<b>Price Validity</b> : {{ dateTimeFormat($seller_data->price_validity) }} @endif
                                                    @if($seller_data->quantity) &nbsp;<b>Quantity</b> : {{ $seller_data->quantity ?? 0 }} @endif
                                                    </button>
                                                </h2>
                                                <div id="seller_collapse_{{ $seller_data->id }}" class="accordion-collapse collapse" aria-labelledby="seller_heading_{{ $seller_data->id }}" data-bs-parent="#state_price_accordion">
                                                    <div class="accordion-body">
                                                        <div class="table-responsive">
                                                            <table class="custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        @php
                                                                            $attributes = $seller_data->getStatePrice[0]->value;
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
                                                                    @foreach ($seller_data->getStatePrice as $state_price)
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
                                                                    <tr>
                                                                        <th colspan="{{count($attributes)+2}}" class="text-end">Loading Charge</th>
                                                                        <td>{{ $seller_data->loading_charge ?? 0 }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th colspan="{{count($attributes)+2}}" class="text-end">Insurance Charge</th>
                                                                        <td>{{ $seller_data->insurance_charge ?? 0 }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th colspan="{{count($attributes)+2}}" class="text-end">Quality Charge</th>
                                                                        <td>{{ $seller_data->quality_charge ?? 0 }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th colspan="{{count($attributes)+2}}" class="text-end">GST</th>
                                                                        <td>{{ $seller_data->gst ?? 0 }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th colspan="{{count($attributes)+2}}" class="text-end">TCS</th>
                                                                        <td>{{ $seller_data->tcs ?? 0 }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty

                                        @endforelse
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="buyer" role="tabpanel" aria-labelledby="buyer-tab">
                                    <div class="accordion" id="accordionCustomer">
                                        @foreach ($customer_list ?? [] as $customer_data)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="customer_heading_{{ $customer_data->id }}">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customer_collapse_{{ $customer_data->id }}" aria-expanded="false" aria-controls="customer_collapse_{{ $customer_data->id }}">
                                                    <b>{{ $customer_data->getUserDetail ? $customer_data->getUserDetail->company_name : '' }} ({{ $customer_data->name }} {{ $customer_data->phone }})</b>
                                                    </button>
                                                </h2>
                                                <div id="customer_collapse_{{ $customer_data->id }}" class="accordion-collapse collapse" aria-labelledby="customer_heading_{{ $customer_data->id }}" data-bs-parent="#accordionCustomer">
                                                    <div class="accordion-body">
                                                        <b>Total Enquiry : </b> {{ $customer_data->total_enquiry }} <br>
                                                        <b>Total Order : </b> {{ $customer_data->total_order }} <br>
                                                        <b>Total Dispatched : </b> {{ $customer_data->total_dispatched_order }} <br>
                                                        <b>Total Cancel Order : </b> 0 <br>
                                                        <b>Total Pending Order : </b> 0 <br>
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
        </div>
    </div>
</div>
