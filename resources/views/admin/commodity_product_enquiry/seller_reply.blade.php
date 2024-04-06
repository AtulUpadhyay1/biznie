<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4> {{ $page_title }} - {{ $data->unique_id }}</h4>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <b>Product: </b> {{ $data->getCommodityProduct->name }} <br>
                                <b>Brand: </b> {{ $data->getBrand->name }} <br>
                                <b>Purpose: </b> {{ $data->purpose }} <br>
                            </p><br>
                            <p>
                                <b>Billing Address</b> <br>
                                <b>Pincode: </b> {{ $data->billing_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $data->billing_address['address'] }} <br>
                                <b>City: </b> {{ $data->billing_address['city'] }} <br>
                                <b>State: </b> {{ $data->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6">
                            <p>
                                <b>User: </b> {{ $data->getCustomer->name }} <br>
                                <b>Origin City: </b> {{ $data->origin_city	}} <br>
                                <b>Phone: </b> {{ $data->getCustomer->phone }} <br>
                            </p><br>
                            <p>
                                <b>Delivery Address</b> <br>
                                <b>Pincode: </b> {{ $data->delivery_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $data->delivery_address['address'] }} <br>
                                <b>City: </b> {{ $data->delivery_address['city'] }} <br>
                                <b>State: </b> {{ $data->delivery_address['state'] }} <br>
                            </p>
                        </div>

                        <div class="col-8">
                            <h5 class="my-3">Seller Reply</h5>
                        </div>

                        <div class="col-4 text-end">
                            @if($selected_enquiry_id)
                                <button class="btn btn-primary btn-xs my-2" title="Mark enquiry to seller" wire:click="markSeller()">Mark Seller</button>
                            @endif
                        </div>

                        <div class="accordion" id="seller_price">
                            @foreach ($list as $list_data)
                                <div class="accordion-item">
                                    <div class="row">
                                        <div class="col-1 text-center mt-3">
                                            <input type="radio" id="seller_{{ $list_data->id }}" class="form-check-input" value="{{ $list_data->id }}" wire:model.live="selected_enquiry_id">
                                        </div>
                                        <div class="col-11">
                                            <h2 class="accordion-header" id="heading_{{ $list_data->id }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $list_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $list_data->id }}">
                                                    <b>{{ $list_data->getUser->name}}</b>, &nbsp;
                                                    <b>Brand</b> : {{ $list_data->getBrand->name }}, &nbsp;
                                                    <b>State</b> : {{ $list_data->getSellerCommodityProduct->getStatePrice[0]->state }}, &nbsp;
                                                    <b>City</b> : {{ $list_data->getSellerCommodityProduct->getStatePrice[0]->city }}, &nbsp;
                                                    <b>Base Price</b> : {{ $list_data->base_price }}
                                                </button>
                                            </h2>
                                        </div>
                                    </div>
                                    <div id="collapse_{{ $list_data->id }}" class="accordion-collapse collapse" aria-labelledby="heading_{{ $list_data->id }}" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            @foreach ($list_data->value[0]['value'] as $variation_heading)
                                                                <th>{{ $variation_heading['name'] }}</th>
                                                            @endforeach
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($list_data->value as $variation)
                                                            {{-- @if ($variation['is_selected'] == '1') --}}
                                                                <tr>
                                                                    <td>{{$loop->iteration}}</td>
                                                                    @foreach ($variation['value'] as $value)
                                                                        <td>{{ $value['value'] }}</td>
                                                                    @endforeach
                                                                    <td>{{ $variation['price'] }}</td>
                                                                    <td>{{ $variation['quantity'] }}</td>
                                                                </tr>
                                                            {{-- @endif --}}
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
