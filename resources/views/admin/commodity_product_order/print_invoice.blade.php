<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $order_detail->order_id }}</h4>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <p>
                                <b>Product: </b> {{ $order_detail->getCommodityProduct->name }} <br>
                                <b>Brand: </b> {{ $order_detail->getBrand->name }} <br>
                                <b>Purpose: </b> {{ $order_detail->purpose }} <br>
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <p>
                                <b>Customer: </b> {{ $order_detail->getCustomer->name }} <br>
                                <b>Phone: </b> {{ $order_detail->getCustomer->phone }} <br>
                            </p>
                        </div>
                        <div class="col-4 text-end">
                            <p>
                                <b>Seller: </b> {{ $order_detail->getSeller->name }} <br>
                                <b>Phone: </b> {{ $order_detail->getSeller->phone }} <br>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <p>
                                <b>Billing Address</b> <br>
                                <b>Pincode: </b> {{ $order_detail->billing_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $order_detail->billing_address['address'] }} <br>
                                <b>City: </b> {{ $order_detail->billing_address['city'] }} <br>
                                <b>State: </b> {{ $order_detail->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p>
                                <b>Delivery Address</b> <br>
                                <b>Pincode: </b> {{ $order_detail->delivery_address['pin_code'] }} <br>
                                <b>Address: </b> {{ $order_detail->delivery_address['address'] }} <br>
                                <b>City: </b> {{ $order_detail->delivery_address['city'] }} <br>
                                <b>State: </b> {{ $order_detail->delivery_address['state'] }} <br>
                            </p>
                        </div>
                    </div>

                    <h5 class="my-3">Product Variation</h5>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @foreach ($order_detail->value[0]['value'] as $variation_heading)
                                        <th>{{ $variation_heading['name'] }}</th>
                                    @endforeach
                                    <th>Quantity</th>
                                    <th>Gauge Diff.</th>
                                    <th>Final Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order_detail->value as $variation)

                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        @foreach ($variation['value'] as $value)
                                            <td>{{ $value['value'] }}</td>
                                        @endforeach
                                        <td>{{ $variation['quantity'] }}</td>
                                        <td>{{ $variation['price'] }}</td>
                                        <td>{{ $variation['price'] + $order_detail->base_price }}</td>
                                    </tr>

                                @endforeach

                                <tr>
                                    <td colspan="{{ count($order_detail->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="base_price" class="form-label">Base Price</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $order_detail->base_price }}
                                        </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ count($order_detail->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="transport_price" class="form-label">Transport Price</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $order_detail->transport_price }}
                                        </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ count($order_detail->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="commission" class="form-label">Commission</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $order_detail->commission }}
                                        </td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
