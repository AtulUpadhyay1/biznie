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
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product-enquiry.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
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
                                @isset($data->billing_address['address'])
                                    <b>Address: </b> {{ $data->billing_address['address'] }} <br>
                                @else
                                    <b>Address 1: </b> {{ $data->billing_address['address_line_one'] }} <br>
                                    <b>Address 2: </b> {{ $data->billing_address['address_line_two'] }} <br>
                                @endisset
                                <b>City: </b> {{ $data->billing_address['city'] }} <br>
                                <b>State: </b> {{ $data->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6">
                            <p>
                                <b>User: </b> {{ $data->getUser->name }} <br>
                                <b>Origin City: </b> {{ $data->origin_city	}} <br>
                                <b>Phone: </b> {{ $data->getUser->phone }} <br>
                            </p><br>
                            <p>
                                <b>Delivery Address</b> <br>
                                <b>Pincode: </b> {{ $data->delivery_address['pin_code'] }} <br>
                                @isset($data->delivery_address['address'])
                                    <b>Address: </b> {{ $data->delivery_address['address'] }} <br>
                                @else
                                    <b>Address 1: </b> {{ $data->delivery_address['address_line_one'] }} <br>
                                    <b>Address 2: </b> {{ $data->delivery_address['address_line_two'] }} <br>
                                @endisset
                                <b>City: </b> {{ $data->delivery_address['city'] }} <br>
                                <b>State: </b> {{ $data->delivery_address['state'] }} <br>
                            </p>
                        </div>

                        <h5 class="my-3">Selected Product Variation</h5>

                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($data->variation[0]['value'] as $variation_heading)
                                            <th>{{ $variation_heading['name'] }}</th>
                                        @endforeach
                                        {{-- <th>Price</th> --}}
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->variation as $variation)
                                        <tr>
                                            <td>{{$variation['id']}}</td>
                                            @foreach ($variation['value'] as $value)
                                                <td>{{ $value['value'] }}</td>
                                            @endforeach
                                            {{-- <td>{{ $variation['price'] }}</td> --}}
                                            <td>{{ $variation['quantity'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-8">
                            <h5 class="my-3">Available seller for selected variation</h5>
                        </div>

                        <div class="col-4 text-end">
                            @if (count($this->user_id))
                                <button class="btn btn-primary btn-xs my-2" title="Send enquiry to seller" wire:click="sendEnquiry()">Send Enquiry</button>
                            @endif
                        </div>

                        <div class="accordion" id="state_price">
                            @foreach ($seller_list as $seller_data)
                                <div class="accordion-item">
                                    <div class="row">
                                        <div class="col-1 text-center mt-3">
                                            <input type="checkbox" id="seller_{{ $seller_data->user_id }}" class="form-check-input" value="{{ $seller_data->user_id }}" wire:model.live="user_id">
                                        </div>
                                        <div class="col-11">
                                            <h2 class="accordion-header" id="heading_{{ $seller_data->id }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $seller_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $seller_data->id }}">
                                                <b>{{ $seller_data->getUser->name}} ({{ getSellerType($seller_data->user_id) }})</b>, &nbsp;<b>Brand</b> : {{ $seller_data->getBrand->name }}, &nbsp;<b>State</b> : {{ $seller_data->getStatePrice[0]->state }}, &nbsp;<b>City</b> : {{ $seller_data->getStatePrice[0]->city }}, &nbsp; <b>Base Price</b> : {{ $seller_data->base_price }}
                                                </button>
                                            </h2>
                                        </div>
                                    </div>
                                    <div id="collapse_{{ $seller_data->id }}" class="accordion-collapse collapse" aria-labelledby="heading_{{ $seller_data->id }}" data-bs-parent="#accordionExample">
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
                                                            <th>Final Price</th>
                                                            <th>Stock</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $total_charges = 0;
                                                            $ex_price = 0;

                                                            foreach ($seller_data->packaging_type as $packaging_charge) {
                                                                $packaging_price = $seller_data->packaging_type_price[$packaging_charge];
                                                                $total_charges += $packaging_price;
                                                            }

                                                            foreach ($seller_data->charge_name as $charge_key => $charge_name) {

                                                                $other_charges_price = $seller_data->charge_price[$charge_key];
                                                                $other_charges_operator = $seller_data->operator[$charge_key];

                                                                if($other_charges_operator == "+"){
                                                                    $total_charges += $other_charges_price;
                                                                }elseif($other_charges_operator == "-"){
                                                                    $total_charges -= $other_charges_price;
                                                                }elseif($other_charges_operator == "*"){
                                                                    $total_charges += $ex_price * $other_charges_price;
                                                                }elseif($other_charges_operator == "/"){
                                                                    $total_charges += $ex_price / $other_charges_price;
                                                                }elseif($other_charges_operator == "%"){
                                                                    $total_charges += $ex_price * ($other_charges_price / 100);
                                                                }

                                                                if($seller_data->is_quality){

                                                                    foreach ($seller_data->quality as $quality_key => $quality) {
                                                                        $other_quantity_charge_arr['name']          = $quality;
                                                                        $other_quantity_price = $seller_data->quality_price[$quality_key];
                                                                        $total_charges += $other_quantity_price;
                                                                    }
                                                                }
                                                            }
                                                        @endphp

                                                        @foreach ($seller_data->getStatePrice as $state_price)
                                                            <tr>
                                                                <th>
                                                                    {{ $loop->iteration }}
                                                                    @if($state_price->is_selected)
                                                                        <i class="bi bi-check2-circle text-success fs-5"></i>
                                                                    @endif
                                                                </th>
                                                                @foreach ($state_price->value as $price_value)
                                                                    <td>{{ $price_value['value'] }} </td>
                                                                @endforeach
                                                                <td> {{ $state_price->price }} </td>
                                                                <td>
                                                                    @php
                                                                        $final_variation_price = 0;
                                                                        $tax = ($state_price->price + $seller_data->base_price) * $state_price->gst / 100;
                                                                        $per_unit_price = ($state_price->price + $seller_data->base_price) + $tax + $total_charges;
                                                                        $final_price    = $per_unit_price;
                                                                        $final_variation_price += $final_price;
                                                                        // $gst_amount += $tax
                                                                    @endphp
                                                                    {{ $final_variation_price }}
                                                                </td>
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
