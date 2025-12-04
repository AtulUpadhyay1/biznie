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
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Request For Quotation</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <p>
                                        Enquiry ID: {{ $data->unique_id }} <br>
                                        Category: {{ $data->getCommodityProduct->getCategory->name }} <br>
                                        Product: {{ $data->getCommodityProduct->name }} <br>
                                        Brand: {{ $data->getBrand->name }} <br>
                                        Purpose: {{ $data->purpose }} <br>
                                        Description: {{ $data->description }} <br>
                                        @if($data->quality)
                                            Quality: {{ $data->quality['name'] }} : ₹ {{ $data->quality['price'] }} <br>
                                        @endif
                                        @if ($data->packaging_charge)
                                            Packaging Charge: {{ $data->packaging_charge['name'] }} : ₹ {{ $data->packaging_charge['charge'] }}  <br>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Buyer Details</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <p>
                                        Company : {{ $data->getUser->getUserDetail->company_name }}<br>
                                        Phone : {{ $data->getUser->phone }} <br>
                                        GST : {{ $data->getUser->getUserDetail->gst_number }} <br>
                                        Address Line1 : {{ $data->getUser->getUserDetail->address_line_one }} <br>
                                        Address Line2 : {{ $data->getUser->getUserDetail->address_line_two }} <br>
                                        City : {{ $data->getUser->getUserDetail->city }} <br>
                                        State : {{ $data->getUser->getUserDetail->state }} <br>
                                        Pincode : {{ $data->getUser->getUserDetail->postal_code }} <br>
                                        Credit Days : {{ $data->getUser->credit_days }} Days <br>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Buyer (Bill to)</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <p>
                                        Company : {{ $data->billing_address['company_name'] }}<br>
                                        Phone : {{ $data->billing_address['phone'] }} <br>
                                        GST : {{ $data->billing_address['gst'] }} <br>
                                        Address Line1 : {{ $data->billing_address['address_line_one'] }} <br>
                                        Address Line2 : {{ $data->billing_address['address_line_two'] }} <br>
                                        State : {{ $data->billing_address['state'] }} <br>
                                        City : {{ $data->billing_address['city'] }} <br>
                                        Pincode : {{ isset($data->consignee_detail['pin_code']) ? $data->consignee_detail['pin_code'] : (isset($data->billing_address['pincode']) ? $data->billing_address['pincode'] : '') }} <br>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Consignee (Ship to)</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <p>
                                        Company : {{ $data->consignee_detail['company_name'] }}<br>
                                        Phone : {{ $data->consignee_detail['phone'] }} <br>
                                        GST : {{ $data->consignee_detail['gst'] }} <br>
                                        Address Line1 : {{ $data->consignee_detail['address_line_one'] }} <br>
                                        Address Line2 : {{ $data->consignee_detail['address_line_two'] }} <br>
                                        State : {{ $data->consignee_detail['state'] }} <br>
                                        City : {{ $data->consignee_detail['city'] }} <br>
                                        Pincode : {{ isset($data->consignee_detail['pin_code']) ? $data->consignee_detail['pin_code'] : (isset($data->billing_address['pincode']) ? $data->billing_address['pincode'] : '') }} <br>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Loading Address</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    @if($loading_address)
                                        <p>
                                            Address Line One : {{ $loading_address->address_line_one }} <br>
                                            Address Line Two : {{ $loading_address->address_line_two }} <br>
                                            City : {{ $loading_address->city }} <br>
                                            State : {{ $loading_address->state }} <br>
                                            Pincode : {{ $loading_address->pincode }} <br>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- <div class="col-6">
                            <p>
                                <b>Product: </b> {{ $data->getCommodityProduct->name }} <br>
                                <b>Brand: </b> {{ $data->getBrand->name }} <br>
                                <b>Purpose: </b> {{ $data->purpose }} <br>
                            </p><br>
                            <p>
                                <b>Billing Address</b> <br>
                                <b>Pincode: </b> {{ isset($data->billing_address['pin_code']) ? $data->billing_address['pin_code'] : $data->billing_address['pincode'] }} <br>
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
                                <b>User: </b> {{ $data->getUser?->name }} <br>
                                <b>Company Name: </b> {{ $data->getUser?->getUserDetail?->company_name ?? '--' }} <br>
                                <b>GST: </b> {{ $data->getUser?->getUserDetail?->gst_number ?? '--' }} <br>
                                <b>Origin City: </b> {{ $data->origin_city	}} <br>
                                <b>Phone: </b> {{ $data->getUser?->phone }} <br>
                            </p><br>
                            <p>
                                <b>Consignee Detail</b> <br>
                                <b>Company: </b> {{ $data->consignee_detail['company_name'] }} <br>
                                <b>Phone: </b> {{ isset($data->consignee_detail['phone_number']) ? $data->consignee_detail['phone_number'] : $data->consignee_detail['phone'] }} <br>
                                <b>Pincode: </b> {{ isset($data->consignee_detail['pin_code']) ? $data->consignee_detail['pin_code'] : $data->consignee_detail['pincode'] }} <br>
                                <b>Address 1: </b> {{ $data->consignee_detail['address_line_one'] }} <br>
                                <b>Address 2: </b> {{ $data->consignee_detail['address_line_two'] }} <br>
                                <b>City: </b> {{ $data->consignee_detail['city'] }} <br>
                                <b>State: </b> {{ $data->consignee_detail['state'] }} <br>
                                <b>Gst Number: </b> {{ $data->consignee_detail['gst'] }} <br>
                            </p>
                        </div> --}}

                        <h5 class="my-3">Selected Product Variation</h5>

                        <div class="table-responsive mb-3">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($data->variation[0]['value'] as $variation_heading)
                                            <th>{{ $variation_heading['name'] }}
                                                @if($variation_heading['unit'])
                                                    ({{ $variation_heading['unit']['short_name'] }})
                                                @endif
                                            </th>
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

                        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{$active_tab == 'seller' ? 'active' : ''}}" id="seller-line-tab" href="{{route('admin.commodity-product-enquiry.show', $data->id)}}?active_tab=seller" aria-controls="seller_tab" wire:navigate>Seller</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$active_tab == 'transporter' ? 'active' : ''}}" id="transporter-line-tab" href="{{route('admin.commodity-product-enquiry.show', $data->id)}}?active_tab=transporter" aria-controls="transporter" wire:navigate>Transporter</a>
                            </li>

                        </ul>
                        <div class="tab-content mt-3" id="lineTabContent">
                            <div class="tab-pane fade {{$active_tab == 'seller' ? 'show active' : ''}}" id="seller_tab" role="tabpanel" aria-labelledby="seller-line-tab">
                                <div class="row">
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
                                                    <div class="col-12">
                                                        <input type="checkbox" id="seller_{{ $seller_data->user_id }}" class="form-check-input" value="{{ $seller_data->user_id }}" wire:model.live="user_id">
                                                        <h2 class="accordion-header" id="heading_{{ $seller_data->id }}">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $seller_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $seller_data->id }}">
                                                            <b>{{ $seller_data->getUser->getBusiness->name}} ({{$seller_data->getUser->phone}}) {{ $seller_data->getUser->name}} ({{ getSellerType($seller_data->user_id) }})</b>, &nbsp;<b>Brand</b> : {{ $seller_data->getBrand->name }}, &nbsp;<b>State</b> : {{ $seller_data->getStatePrice[0]->state }}, &nbsp;<b>City</b> : {{ $seller_data->getStatePrice[0]->city }}, &nbsp; <b>Base Price</b> : {{ $seller_data->base_price }} &nbsp; <b>Update At</b> : {{ dateTimeFormat($seller_data->updated_at) }}
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
                                                                        <th>EX Price</th>
                                                                        <th>Stock</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $total_charges = 0;
                                                                        $ex_price = 0;

                                                                        foreach ($seller_data->packaging_type as $packaging_charge) {
                                                                            $packaging_price = isset($seller_data->packaging_type_price[$packaging_charge]) ? $seller_data->packaging_type_price[$packaging_charge] : 0;
                                                                            $total_charges += $packaging_price;
                                                                        }

                                                                        foreach ($seller_data->charge_name as $charge_key => $charge_name) {

                                                                            $other_charges_price = isset($seller_data->charge_price[$charge_key]) ? $seller_data->charge_price[$charge_key] : 0;
                                                                            $other_charges_operator = isset($seller_data->operator[$charge_key]) ? $seller_data->operator[$charge_key] : '';
                                                                            if($other_charges_operator){

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
                                                                            }
                                                                        }

                                                                        if($seller_data->is_quality){

                                                                            foreach ($seller_data->quality as $quality_key => $quality) {
                                                                                $other_quantity_charge_arr['name']          = $quality;
                                                                                $other_quantity_price = isset($seller_data->quality_price[$quality_key]) ? $seller_data->quality_price[$quality_key] : 0;
                                                                                $total_charges += $other_quantity_price;
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
                                                                                    $tax = ($state_price->price + $seller_data->base_price) * $seller_data->gst / 100;
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

                            <div class="tab-pane fade {{$active_tab == 'transporter' ? 'show active' : ''}}" id="transporter" role="tabpanel" aria-labelledby="transporter-line-tab">
                                <div class="row">
                                    <div class="col-8">
                                        <h5 class="my-3">Available Transporter</h5>
                                    </div>

                                    <div class="col-4 text-end">
                                        @if (count($this->transporter_user_id))
                                            <button class="btn btn-primary btn-xs my-2" title="Send enquiry to seller" wire:click="sendTransporterEnquiry()">Send Enquiry</button>
                                        @endif
                                    </div>

                                    <div class="accordion" id="state_price">
                                        @foreach ($transporter_list as $transporter_data)
                                            <div class="accordion-item">
                                                <div class="row">
                                                    <div class="col-1 text-center mt-3">
                                                        <input type="checkbox" id="transporter_{{ $transporter_data->user_id }}" class="form-check-input" value="{{ $transporter_data->user_id }}" wire:model.live="transporter_user_id">
                                                    </div>
                                                    <div class="col-11">
                                                        <h2 class="accordion-header" id="heading_{{ $transporter_data->id }}">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $transporter_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $transporter_data->id }}">
                                                            <b>{{ $transporter_data->getUser->name}} ({{$transporter_data->getUser->phone}})</b>, &nbsp;<b>State</b> : {{ $transporter_data->state }}, &nbsp;<b>City</b> : {{ $transporter_data->city }}, &nbsp; <b>Price</b> : {{ $transporter_data->min_price }} - {{ $transporter_data->max_price }}
                                                            </button>
                                                        </h2>
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
