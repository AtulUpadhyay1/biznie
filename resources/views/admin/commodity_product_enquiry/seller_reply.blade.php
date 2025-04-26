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
                                <b>Pincode: </b> {{ isset($data->billing_address['pin_code']) ? $data->billing_address['pin_code'] :  $data->billing_address['pincode'] }} <br>
                                @isset($data->billing_address['address'])
                                    <b>Address: </b> {{ $data->billing_address['address'] }} <br>
                                @else
                                    <b>Address Line One: </b> {{ $data->billing_address['address_line_one'] }} <br>
                                    <b>Address Line Two: </b> {{ $data->billing_address['address_line_two'] }} <br>
                                @endisset
                                <b>City: </b> {{ $data->billing_address['city'] }} <br>
                                <b>State: </b> {{ $data->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6">
                            <p>
                                <b>User: </b> {{ $data->getCustomer->name }} <br>
                                <b>Company Name: </b> {{ $data->getCustomer->getUserDetail ? $data->getCustomer->getUserDetail->company_name : '--' }} <br>
                                <b>GST: </b> {{ $data->getCustomer->getUserDetail ? $data->getCustomer->getUserDetail->gst_number : '--' }} <br>
                                <b>Origin City: </b> {{ $data->origin_city	}} <br>
                                <b>Phone: </b> {{ $data->getCustomer->phone }} <br>
                            </p><br>
                            <p>
                                <b>Consignee Address</b> <br>
                                <b>Company: </b> {{ $data->consignee_detail['company_name'] }} <br>
                                <b>Phone: </b> {{ isset($data->consignee_detail['phone_number']) ? $data->consignee_detail['phone_number'] : $data->consignee_detail['phone'] }} <br>
                                <b>Pincode: </b> {{ isset($data->consignee_detail['pin_code']) ? $data->consignee_detail['pin_code'] : $data->consignee_detail['pincode'] }} <br>
                                @isset($data->consignee_detail['address'])
                                    <b>Address: </b> {{ $data->consignee_detail['address'] }} <br>
                                @else
                                    <b>Address Line One: </b> {{ $data->consignee_detail['address_line_one'] }} <br>
                                    <b>Address Line Two: </b> {{ $data->consignee_detail['address_line_two'] }} <br>
                                @endisset
                                <b>City: </b> {{ $data->consignee_detail['city'] }} <br>
                                <b>State: </b> {{ $data->consignee_detail['state'] }} <br>
                                <b>Gst Number: </b> {{ $data->consignee_detail['gst'] }} <br>
                            </p>
                        </div>

                        <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{$active_tab == 'transporter' ? 'active' : ''}}" id="transporter-tab" href="{{route('admin.commodity-product-enquiry.sellerReply', $hidden_id)}}?active_tab=transporter" aria-controls="transporter" wire:navigate>Transporter Reply</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$active_tab == 'seller' ? 'active' : ''}}" id="seller-tab" href="{{route('admin.commodity-product-enquiry.sellerReply', $hidden_id)}}?active_tab=seller" aria-controls="seller" wire:navigate>Seller Reply</a>
                            </li>
                        </ul>
                        <div class="tab-content border border-top-0 p-3" id="myTabContent">
                            <div class="tab-pane fade {{$active_tab == 'transporter' ? 'show active' : ''}}" id="transporter" role="tabpanel" aria-labelledby="transporter-tab">
                                <div class="row">

                                    <div class="col-12 text-end">
                                        @if($selected_transporter_id)
                                            <button class="btn btn-primary btn-xs my-2" title="Mark enquiry to transporter" wire:click="markTransporter()">Mark Transporter</button>
                                        @endif
                                    </div>
                                    <div class="accordion" id="transporter_price">
                                        @foreach ($transporter_list as $transporter_data)
                                            <div class="accordion-item">
                                                <div class="row">
                                                    <div class="col-1 text-center mt-3">
                                                        <input type="radio" id="transporter_{{ $transporter_data->id }}" class="form-check-input" value="{{ $transporter_data->id }}" wire:model.live="selected_transporter_id">
                                                        <button class="btn btn-info btn-sm p-0 ms-2" title="Update Price" data-bs-toggle="modal" data-bs-target="#updateTransporterPrice_{{ $transporter_data->id }}" wire:click="setTransporterPrice({{ $transporter_data->id }})"><i class="bi bi-pencil-square"></i></button>
                                                    </div>
                                                    <div class="col-11">
                                                        <h2 class="accordion-header" id="transporter_heading_{{ $transporter_data->id }}">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $transporter_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $transporter_data->id }}">
                                                                <b>{{ $transporter_data->getUser->name}} ({{$transporter_data->getUser->phone}})</b>, &nbsp;
                                                                <b>Price</b> : ₹ {{ formatIndianNumber($transporter_data->min_price) }} - ₹ {{ formatIndianNumber($transporter_data->max_price) }} &nbsp;
                                                                <b>Updated Price</b> : ₹ {{ $transporter_data->price ? formatIndianNumber($transporter_data->price) : 'NA' }}
                                                            </button>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="updateTransporterPrice_{{ $transporter_data->id }}" tabindex="-1" aria-labelledby="updateTransporterPriceLable_{{ $transporter_data->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="updateTransporterPriceLable_{{ $transporter_data->id }}">Update Transporter Price</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="transporter_price_{{ $transporter_data->id }}" class="form-label">Transporter Price</label>
                                                            <input type="number" class="form-control" id="transporter_price_{{ $transporter_data->id }}" placeholder="Enter Transporter Price" wire:model="transporter_price">
                                                            @error('transporter_price') <small class="text-danger">{{ $message }}</small>@enderror
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary" wire:click="updateTransporterPrice()">Update</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade {{$active_tab == 'seller' ? 'show active' : ''}}" id="seller" role="tabpanel" aria-labelledby="seller-tab">
                                <div class="row">
                                    <div class="col-8">
                                        {{-- <h5 class="my-3">Seller Reply</h5> --}}
                                    </div>
                                    <div class="col-4 text-end">
                                        @if($selected_enquiry_id)
                                            <button class="btn btn-primary btn-xs my-2" title="Update Price" data-bs-toggle="modal" data-bs-target="#updatePrice" wire:click="updatePriceForm()">Update Price</button>
                                            {{-- <button class="btn btn-primary btn-xs my-2" title="Mark enquiry to seller" wire:click="markSeller()">Mark Seller</button> --}}
                                        @endif
                                    </div>

                                    <div class="accordion" id="seller_price">
                                        @foreach ($list as $list_data)
                                            @php
                                                $repliedStatus = collect($list_data->history)->firstWhere('status', 'Replied');
                                            @endphp
                                            <div class="accordion-item">
                                                <div class="row">
                                                    <div class="col-1 text-center mt-3">
                                                        <input type="radio" id="seller_{{ $list_data->id }}" class="form-check-input" value="{{ $list_data->id }}" wire:model.live="selected_enquiry_id">
                                                        <button class="btn btn-info btn-sm p-0 ms-2" title="Update Price" data-bs-toggle="modal" data-bs-target="#updateBasePrice_{{ $list_data->id }}" wire:click="setBasePrice({{ $list_data->id }})"><i class="bi bi-pencil-square"></i></button>
                                                    </div>
                                                    <div class="col-11">
                                                        @php
                                                            $address = $list_data->loading_address[0];
                                                            $city = isset($address['city']) ? $address['city'] : '';
                                                            $state = isset($address['state']) ? $address['state'] : '';

                                                            $seller_commodity_product = App\Models\SellerCommodityProduct::where('user_id', $list_data->user_id)
                                                                ->where('commodity_product_id', $list_data->commodity_product_id)
                                                                ->where('brand_id', $list_data->brand_id)
                                                                ->first();

                                                            $defaul_ex_price = 0;
                                                            $base_price = 0;
                                                            if($seller_commodity_product->base_price != $list_data->base_price){
                                                                $base_price = $list_data->base_price;
                                                            }else{
                                                                $base_price = $seller_commodity_product->base_price;
                                                            }

                                                            $default_price = getDefaultCommodityProductVariationPrice($list_data->commodity_product_id, $list_data->brand_id, $state, $city);
                                                            $loading_charge = $seller_commodity_product->loading_charge;
                                                            $insurance_charge = $seller_commodity_product->insurance_charge;
                                                            $quality_charge = $seller_commodity_product->quality_charge;
                                                            $gst = $seller_commodity_product->gst;

                                                            $extra_charges = 0;
                                                            $other_charges = [];
                                                            foreach ($seller_commodity_product->charge_name as $charge_key => $charge_name) {
                                                                $other_charges_arr['name'] = $charge_name;
                                                                $other_charges_arr['price'] = isset($seller_commodity_product->charge_price[$charge_key]) ? $seller_commodity_product->charge_price[$charge_key] : "0";
                                                                $other_charges_arr['operator'] = isset($seller_commodity_product->operator[$charge_key]) ? $seller_commodity_product->operator[$charge_key] : "";

                                                                if($other_charges_arr['operator']){
                                                                    if($other_charges_arr['operator'] == "+"){
                                                                        $extra_charges += $other_charges_arr['price'];
                                                                    }elseif($other_charges_arr['operator'] == "-"){
                                                                        $extra_charges -= $other_charges_arr['price'];
                                                                    }elseif($other_charges_arr['operator'] == "*"){
                                                                        $extra_charges += 0;
                                                                    }elseif($other_charges_arr['operator'] == "/"){
                                                                        $extra_charges += 0;
                                                                    }elseif($other_charges_arr['operator'] == "%"){
                                                                        $extra_charges += 0;
                                                                    }
                                                                }
                                                                $other_charges[] = $other_charges_arr;
                                                            }


                                                            $gauge_diff = $default_price;
                                                            $all_charges = $loading_charge + $insurance_charge + $quality_charge + $extra_charges;
                                                            $total_amount = $base_price + $gauge_diff + $all_charges;
                                                            $tax_amount = round($total_amount * $gst / 100);
                                                            $defaul_ex_price = $total_amount + $tax_amount;
                                                            // $gst_amount         += $tax;

                                                        @endphp
                                                        <h2 class="accordion-header" id="heading_{{ $list_data->id }}">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $list_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $list_data->id }}">
                                                                <b>{{ $list_data->getUser->getBusiness->name}} ({{ getSellerType($list_data->user_id) }}) </b>,
                                                                @php
                                                                    $default_variation = getDefaultCommodityProductVariation($list_data->commodity_product_id, $list_data->brand_id, $state, $city)
                                                                @endphp
                                                                @if($default_variation)
                                                                    @foreach ($default_variation->value as $default_variations)
                                                                        <b class="ms-1">{{ $default_variations['name'] }}:</b> {{ $default_variations['value'] }}
                                                                        @if($default_variation->getCommodityProduct->unit && $default_variation->getCommodityProduct->unit[$default_variations['name']])
                                                                            ({{getProductUnit($default_variation->getCommodityProduct->unit[$default_variations['name']])->short_name}})
                                                                        @endif ,
                                                                    @endforeach
                                                                @endif
                                                                <b class="ms-1">Base Price</b> : ₹ {{ formatIndianNumber($base_price) }},
                                                                <b class="ms-1">Ex Price</b> : ₹ {{ formatIndianNumber($defaul_ex_price) }}
                                                                <b class="ms-1">
                                                                    <span title="View Calculation" data-bs-toggle="modal" data-bs-target="#viewCaculation_{{ $list_data->id }}">
                                                                        <i class="bi bi-info-circle text-danger"></i>
                                                                    </span>
                                                                </b>
                                                                @if ($repliedStatus)
                                                                    <b class="ms-1">Status: </b><span class="badge bg-success">{{ $repliedStatus['status'] }}</span>
                                                                    <span class="ms-1">{{ \Carbon\Carbon::parse($repliedStatus['created_at'])->format('d-m-Y H:i:s') }}</span>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                    </div>
                                                </div>
                                                <div id="collapse_{{ $list_data->id }}" class="accordion-collapse collapse" aria-labelledby="heading_{{ $list_data->id }}" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="col-md-6">
                                                                <b>Name : </b> {{ $list_data->getUser->name }} <br>
                                                                <b>Company Name: </b> {{ $list_data->getUser->getBusiness->name }} <br>
                                                                <b>GST : </b> {{ $list_data->getUser->getUserDetail ? $list_data->getUser->getUserDetail->gst_number : '--' }} <br>
                                                                <b>Phone : </b> {{$list_data->getUser->phone}} <br>
                                                                <b>Brand</b> : {{ $list_data->getBrand->name }} <br>
                                                                <b>State</b> : {{ $list_data->getSellerCommodityProduct->getStatePrice[0]->state }} <br>
                                                                <b>City</b> : {{ $list_data->getSellerCommodityProduct->getStatePrice[0]->city }} <br>

                                                            </div>
                                                            @foreach ($list_data->loading_address as $loading_address)
                                                                <div class="col-md-6">
                                                                    @if ($loading_address)
                                                                        <b>Loading Address: </b> <br>
                                                                        <b>Pincode: </b> {{ $loading_address['pin_code'] }} <br>
                                                                        <b>Address Line One: </b> {{ isset($loading_address['address_line_one']) ? $loading_address['address_line_one'] : '--' }} <br>
                                                                        <b>Address Line Two: </b> {{ isset($loading_address['address_line_two']) ? $loading_address['address_line_two'] : '--' }} <br>
                                                                        <b>City: </b> {{ isset($loading_address['city']) ? $loading_address['city'] : '--' }} <br>
                                                                        <b>State: </b> {{ isset($loading_address['state']) ? $loading_address['state'] : '--' }} <br>
                                                                        <b>Loading Position: </b> {{ isset($loading_address['loading_position']) ? $loading_address['loading_position'] : '--' }} / Days <br>
                                                                    @else
                                                                        <b>Loading Address Not Found.</b>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="table-responsive mt-3">
                                                            <table class="custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        @foreach ($list_data->value[0]['value'] as $variation_heading)
                                                                            <th>{{ $variation_heading['name'] }}
                                                                                @if($variation_heading['unit'])
                                                                                    ({{ $variation_heading['unit']['short_name'] }})
                                                                                @endif
                                                                            </th>
                                                                        @endforeach
                                                                        <th>Quantity (MT)</th>
                                                                        <th>Gauge Diff.</th>
                                                                        {{-- <th>EX Price</th> --}}
                                                                        <th>EX Price</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $total_charges = 0;
                                                                        $ex_price = 0;
                                                                        $seller_commodity_product = $list_data->getSellerCommodityProduct;
                                                                        $base_price = $list_data->base_price;
                                                                        // $transport_price = $list_data->transport_price;
                                                                        // $commission =  $seller_commodity_product->commission;

                                                                        foreach ($seller_commodity_product->packaging_type as $packaging_charge) {
                                                                            $packaging_price = isset($seller_commodity_product->packaging_type_price[$packaging_charge]) ? $seller_commodity_product->packaging_type_price[$packaging_charge] : 0;
                                                                            $total_charges += $packaging_price;
                                                                        }

                                                                        foreach ($seller_commodity_product->charge_name as $charge_key => $charge_name) {
                                                                            $other_charges_price = isset($seller_commodity_product->charge_price[$charge_key]) ? $seller_commodity_product->charge_price[$charge_key] : 0;
                                                                            $other_charges_operator = isset($seller_commodity_product->operator[$charge_key]) ? $seller_commodity_product->operator[$charge_key] : '';
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

                                                                        if($seller_commodity_product->is_quality){

                                                                            foreach ($seller_commodity_product->quality as $quality_key => $quality) {
                                                                                $other_quantity_charge_arr['name']          = $quality;
                                                                                $other_quantity_price = $seller_commodity_product->quality_price[$quality_key];
                                                                                $total_charges += $other_quantity_price;
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    @foreach ($list_data->value as $variation)

                                                                        @php
                                                                            $final_variation_price = 0;
                                                                            $tax               = ($variation['price'] + $list_data->base_price) * $seller_commodity_product->gst / 100;
                                                                            $per_unit_price    = ($variation['price'] + $list_data->base_price) + $tax + $total_charges;
                                                                            $final_price       = $per_unit_price * $variation['quantity'];
                                                                            $final_variation_price  += $final_price;
                                                                            // $gst_amount         += $tax;
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{$loop->iteration}}</td>
                                                                            @php
                                                                                $variation_arr = [];
                                                                                $total_quantity = 0;
                                                                                $ex_price = 0;
                                                                                $quality_price = $list_data->quality && isset($list_data->quality['price']) ? $list_data->quality['price'] : "0";
                                                                                $packaging_charge_price = $list_data->packaging_charge && isset($list_data->packaging_charge['charge']) ? $list_data->packaging_charge['charge'] : "0";
                                                                                $all_charges = $loading_charge + $insurance_charge + $quality_charge + $quality_price + $packaging_charge_price + $extra_charges;
                                                                            @endphp
                                                                            @foreach ($variation['value'] as $value)
                                                                                @php
                                                                                    $variation_data_arr['id'] = $value['id'];
                                                                                    $variation_data_arr['name'] = $value['name'];
                                                                                    $variation_data_arr['value'] = $value['value'];
                                                                                    $variation_arr[] = $variation_data_arr;
                                                                                    $quantity = $variation['quantity'];
                                                                                @endphp
                                                                                <td>{{ $value['value'] }}</td>
                                                                            @endforeach
                                                                            @php
                                                                                $gauge_diff = App\Models\SellerCommodityProductStatePrice::where('user_id', $list_data->user_id)
                                                                                    ->where('commodity_product_id', $list_data->commodity_product_id)
                                                                                    ->where('brand_id', $list_data->brand_id)
                                                                                    ->where('state', $state)
                                                                                    ->where('city', $city)
                                                                                    // ->whereJsonContains('value', $variation['value'])
                                                                                    ->where(function($query) use ($variation_arr) {
                                                                                        foreach ($variation_arr as $variation) {
                                                                                            $query->whereJsonContains('value', $variation);
                                                                                        }
                                                                                    })
                                                                                ->first();

                                                                                if ($gauge_diff) {
                                                                                    $price = $gauge_diff->price;
                                                                                    $per_unit_price = $gauge_diff->price + $base_price + $all_charges;
                                                                                    $tax = round(($per_unit_price) * $gst / 100);
                                                                                    $per_unit_price += $tax;
                                                                                    $final_price = $per_unit_price * $quantity;
                                                                                    $total_quantity         += $quantity;
                                                                                    $ex_price += $final_price;
                                                                                }
                                                                            @endphp
                                                                            <td>{{ $variation['quantity'] }}</td>
                                                                            <td>₹ {{ $gauge_diff->price }}</td>
                                                                            {{-- <td>₹ {{ formatIndianNumber($final_variation_price) }}</td> --}}
                                                                            <td>₹ {{ formatIndianNumber($ex_price) }}</td>
                                                                        </tr>

                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="viewCaculation_{{ $list_data->id }}" tabindex="-1" aria-labelledby="viewCaculationLable_{{ $list_data->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="viewCaculationLable_{{ $list_data->id }}">Calculation</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    <b>Base Price:</b> ₹ {{ formatIndianNumber($list_data->base_price) }} <br>
                                                                    @if ($default_price > 0)
                                                                        <b>Guage Difference:</b> + ₹ {{ formatIndianNumber($default_price) }} <br>
                                                                    @endif
                                                                    @if ($loading_charge > 0)
                                                                        <b>Loading Charge:</b> + ₹ {{ formatIndianNumber($loading_charge) }} <br>
                                                                    @endif

                                                                    @if($insurance_charge > 0)
                                                                        <b>Insurance Charge:</b> + ₹ {{ formatIndianNumber($insurance_charge) }} <br>
                                                                    @endif

                                                                    @foreach ($other_charges as $other_charge)
                                                                        @if($other_charge['price'] > 0)
                                                                            <b>{{ $other_charge['name'] }}:</b> {{ $other_charge['operator'] }} ₹ {{ formatIndianNumber($other_charge['price']) }}<br>
                                                                        @endif
                                                                    @endforeach
                                                                    @php
                                                                        $total_cal = $list_data->base_price + $default_price + $loading_charge + $insurance_charge + $extra_charges;
                                                                    @endphp
                                                                    <br>
                                                                    <b>Total:</b> ₹ {{ formatIndianNumber($total_cal) }}<br>
                                                                    <b>GST:</b> + {{ $gst }} % <br>
                                                                    <span class="text-success">
                                                                        <b>Ex Price:</b> ₹ {{ formatIndianNumber($defaul_ex_price) }} <br>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade " id="updateBasePrice_{{ $list_data->id }}" tabindex="-1" aria-labelledby="updateBasePriceLable_{{ $list_data->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="updateBasePriceLable_{{ $list_data->id }}">Update Base Price</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="base_price_{{ $list_data->id }}" class="form-label">Base Price</label>
                                                            <input type="number" class="form-control" id="base_price_{{ $list_data->id }}" placeholder="Enter Base Price" wire:model="base_price">
                                                            @error('base_price') <small class="text-danger">{{ $message }}</small>@enderror
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary" wire:click="updateBasePrice()">Update</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $total_variation_price = 0;
                            $total_transport_price = 0;
                        @endphp
                        <div class="modal fade bd-example-modal-lg" id="updatePrice" tabindex="-1" aria-labelledby="updatePriceLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updatePriceLabel">
                                            @if ($set_enquiry_data)
                                                <b>{{ $set_enquiry_data->getUser->name}} ({{ getSellerType($set_enquiry_data->user_id) }})</b>,
                                                <b>Brand</b> : {{ $set_enquiry_data->getBrand->name }},
                                                <b>State</b> : {{ $selected_seller_commodity_product->getStatePrice[0]->state }},
                                                <b>City</b> : {{ $selected_seller_commodity_product->getStatePrice[0]->city }}
                                            @else
                                                <b>Loading...</b>
                                            @endif
                                                {{-- <b>Base Price</b> : {{ $set_enquiry_data->base_price }} --}}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if ($set_enquiry_data)
                                            <div class="table-responsive">
                                                <table class="custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            @foreach ($set_enquiry_data->value[0]['value'] as $variation_heading)
                                                                <th>{{ $variation_heading['name'] }}
                                                                    @if($variation_heading['unit'])
                                                                        ({{ $variation_heading['unit']['short_name'] }})
                                                                    @endif
                                                                </th>
                                                            @endforeach
                                                            <th>Quantity</th>
                                                            <th>Gauge Diff.</th>
                                                            <th>Final Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($set_enquiry_data->value as $variation)
                                                            <tr>
                                                                <td>{{$loop->iteration}}</td>
                                                                @foreach ($variation['value'] as $value)
                                                                    <td>{{ $value['value'] }}</td>
                                                                @endforeach
                                                                <td>{{ $variation['quantity'] }}</td>
                                                                <td>
                                                                    <input type="number" class="form-control" wire:model="set_enquiry_data_price.{{$loop->index}}">
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        // $variation_price = ($variation['price'] != "" ? $variation['price'] : 0) + ($set_enquiry_data_base_price != "" ? $set_enquiry_data_base_price : 0) + $all_charges;
                                                                        // $total_variation_price += $variation_price;

                                                                        $variation_price = ($variation['price'] != "" ? $variation['price'] : 0) + ($set_enquiry_data_base_price != "" ? $set_enquiry_data_base_price : 0) + $all_charges;
                                                                        $tax = round(($variation_price) * $selected_seller_commodity_product->gst / 100);
                                                                        $variation_price += $tax;
                                                                        $final_price = $variation_price * $variation['quantity'];
                                                                        $total_variation_price += $final_price;
                                                                        $total_transport_price +=  $variation['quantity'] * $transport_price;
                                                                    @endphp
                                                                    ₹ {{ formatIndianNumber($total_variation_price) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td style="border-right: hidden;">
                                                                <label for="transport_price" class="form-label">Transport Price</label>
                                                            </td>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value']) }}">
                                                                <input type="number" class="form-control" id="transport_price" placeholder="Enter Transport Price" wire:model.live="transport_price">
                                                                @error('transport_price') <small class="text-danger">{{ $message }}</small>@enderror

                                                            </td>

                                                            <td style="border-right: hidden;">
                                                                <label for="base_price" class="form-label">Base Price</label>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="number" class="form-control" id="base_price" placeholder="Enter Base Price" wire:model.live="set_enquiry_data_base_price">
                                                                @error('set_enquiry_data_base_price') <small class="text-danger">{{ $message }}</small>@enderror
                                                            </td>

                                                        </tr>

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}">
                                                                @if ($selected_transporter)
                                                                    Name : {{ $selected_transporter->getUser->name }} <br>
                                                                    Phone : {{ $selected_transporter->getUser->phone }} <br>
                                                                    Price : ₹ {{ formatIndianNumber($selected_transporter->min_price) }} - ₹ {{ formatIndianNumber($selected_transporter->max_price) }}<br>
                                                                    Given Price : ₹ {{ formatIndianNumber($selected_transporter->price) }}<br>
                                                                @else
                                                                    <a href="{{route('admin.commodity-product-enquiry.sellerReply', $hidden_id)}}?active_tab=transporter" wire:navigate>Select Transpoter</a>
                                                                @endif
                                                            </td>
                                                            <td style="border-right: hidden;">
                                                                <label for="commission" class="form-label">Commission <br>
                                                                    ({{ ucfirst($selected_seller_commodity_product->commission_type) }})
                                                                </label>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="number" class="form-control" id="commission" placeholder="Enter Commission Price" wire:model.live="commission">
                                                                @error('commission') <small class="text-danger">{{ $message }}</small>@enderror
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td style="border-right: hidden;">
                                                                <label for="seller_credit_days" class="form-label">Seller Credit Days</label>
                                                            </td>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value']) }}">
                                                                <input type="number" class="form-control" id="seller_credit_days" placeholder="Enter Seller Credit Days" wire:model="seller_credit_days">
                                                                @error('seller_credit_days') <small class="text-danger">{{ $message }}</small>@enderror
                                                            </td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">Loading Charge</label>
                                                            </td>
                                                            <td colspan="2">
                                                                ₹ {{ formatIndianNumber($selected_seller_commodity_product->loading_charge) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td style="border-right: hidden;">
                                                                <label for="customer_credit_days" class="form-label">Buyer Credit Days</label>
                                                            </td>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value']) }}">
                                                                <input type="number" class="form-control" id="customer_credit_days" placeholder="Enter Customer Credit Days" wire:model="customer_credit_days">
                                                                @error('customer_credit_days') <small class="text-danger">{{ $message }}</small>@enderror
                                                            </td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">Insurance Charge</label>
                                                            </td>
                                                            <td colspan="2">
                                                                ₹ {{ formatIndianNumber($selected_seller_commodity_product->insurance_charge) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;"></td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">Quality Charge</label>
                                                            </td>
                                                            <td colspan="2">
                                                                ₹ {{ formatIndianNumber($selected_seller_commodity_product->quality_charge) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;"></td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">GST Charge</label>
                                                            </td>
                                                            <td colspan="2">
                                                                ₹ {{ formatIndianNumber($selected_seller_commodity_product->gst) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;"></td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">TCS Charge</label>
                                                            </td>
                                                            <td colspan="2">
                                                                ₹ {{ formatIndianNumber($selected_seller_commodity_product->tcs) }}
                                                            </td>
                                                        </tr>

                                                        @foreach ($selected_seller_commodity_product->charge_name as $charge_key => $charge_name)
                                                            <tr>
                                                                <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;"></td>
                                                                <td style="border-right: hidden;">
                                                                    {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                    <label for="" class="form-label">{{ $charge_name }}</label>
                                                                </td>
                                                                <td colspan="2">
                                                                    {{ $selected_seller_commodity_product->operator[$charge_key] }} {{ $selected_seller_commodity_product->charge_price[$charge_key] }}
                                                                </td>
                                                            </tr>

                                                        @endforeach

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;"></td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">Total</label>
                                                            </td>
                                                            <td colspan="2">
                                                                {{-- ₹ {{ formatIndianNumber($total_transport_price) }}
                                                                <br> --}}
                                                                @php
                                                                    if($selected_seller_commodity_product->commission_type != 'include'){
                                                                        $total_variation_price += $commission != "" ? $commission : 0;
                                                                    }
                                                                    $total_variation_price += $transport_price != "" ? $transport_price : 0;
                                                                @endphp
                                                                ₹ {{ formatIndianNumber($total_variation_price) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" wire:click="markSeller()" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Update</span>
                                            <span wire:loading wire:target="markSeller">Updating...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function formatIndianCurrency(input) {
            let value = input.value.replace(/[^0-9.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            let x = value.split('.');
            let x1 = x[0];
            let x2 = x.length > 1 ? '.' + x[1] : '';
            let lastThree = x1.substring(x1.length - 3);
            let otherNumbers = x1.substring(0, x1.length - 3);
            if (otherNumbers != '') {
                lastThree = ',' + lastThree;
            }
            let result = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + x2;
            input.value = result;
        }
    </script>
</div>
