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
                                <b>Pincode: </b> {{ $data->consignee_detail['pin_code'] }} <br>
                                @isset($data->consignee_detail['address'])
                                    <b>Address: </b> {{ $data->consignee_detail['address'] }} <br>
                                @else
                                    <b>Address Line One: </b> {{ $data->consignee_detail['address_line_one'] }} <br>
                                    <b>Address Line Two: </b> {{ $data->consignee_detail['address_line_two'] }} <br>
                                @endisset
                                <b>City: </b> {{ $data->consignee_detail['city'] }} <br>
                                <b>State: </b> {{ $data->consignee_detail['state'] }} <br>
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

                                                            $seller_commodity_product = $list_data->getSellerCommodityProduct;

                                                            $defaul_ex_price = 0;
                                                            $default_price = getDefaultCommodityProductVariationPrice($list_data->commodity_product_id, $list_data->brand_id, $state, $city);
                                                            if($default_price){
                                                                $default_tax            = ($default_price + $list_data->base_price) * $seller_commodity_product->gst / 100;
                                                                $per_unit_price         = ($default_price + $list_data->base_price) + $default_tax;
                                                                $default_final_price    = $per_unit_price * 1;
                                                                $defaul_ex_price        += $default_final_price;
                                                            }
                                                            // $gst_amount         += $tax;

                                                        @endphp
                                                        <h2 class="accordion-header" id="heading_{{ $list_data->id }}">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $list_data->id }}" aria-expanded="false" aria-controls="collapse_{{ $list_data->id }}">
                                                                <b>{{ $list_data->getUser->getBusiness->name}} ({{ getSellerType($list_data->user_id) }}) </b>,
                                                                <b class="ms-1">Base Price</b> : ₹ {{ formatIndianNumber($list_data->base_price) }},
                                                                <b class="ms-1">Ex Price</b> : ₹ {{ formatIndianNumber($defaul_ex_price) }},

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
                                                            @php
                                                                $default_variation = getDefaultCommodityProductVariation($list_data->commodity_product_id, $list_data->brand_id, $state, $city)
                                                            @endphp
                                                            @if($default_variation)
                                                                <div class="col-md-12">
                                                                    <b>Default Variation:</b>

                                                                    @foreach ($default_variation->value as $default_variations)
                                                                        {{ $default_variations['name'] }} : {{ $default_variations['value'] }}
                                                                        @if($default_variation->getCommodityProduct->unit && $default_variation->getCommodityProduct->unit[$default_variations['name']])
                                                                            ({{getProductUnit($default_variation->getCommodityProduct->unit[$default_variations['name']])->short_name}})
                                                                        @endif
                                                                        <br>
                                                                    @endforeach
                                                                </div>
                                                            @endif
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
                                                                    <b>Loading Address: </b> <br>
                                                                    <b>Pincode: </b> {{ $loading_address['pin_code'] }} <br>
                                                                    <b>Address Line One: </b> {{ isset($loading_address['address_line_one']) ? $loading_address['address_line_one'] : '--' }} <br>
                                                                    <b>Address Line Two: </b> {{ isset($loading_address['address_line_two']) ? $loading_address['address_line_two'] : '--' }} <br>
                                                                    <b>City: </b> {{ isset($loading_address['city']) ? $loading_address['city'] : '--' }} <br>
                                                                    <b>State: </b> {{ isset($loading_address['state']) ? $loading_address['state'] : '--' }} <br>
                                                                    <b>Loading Position: </b> {{ isset($loading_address['loading_position']) ? $loading_address['loading_position'] : '--' }} / Days <br>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="table-responsive mt-3">
                                                            <table class="custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        @foreach ($list_data->value[0]['value'] as $variation_heading)
                                                                            <th>{{ $variation_heading['name'] }}</th>
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
                                                                        $transport_price = $list_data->transport_price;
                                                                        $commission =  $seller_commodity_product->commission;

                                                                        foreach ($seller_commodity_product->packaging_type as $packaging_charge) {
                                                                            $packaging_price = $seller_commodity_product->packaging_type_price[$packaging_charge];
                                                                            $total_charges += $packaging_price;
                                                                        }

                                                                        foreach ($seller_commodity_product->charge_name as $charge_key => $charge_name) {
                                                                            $other_charges_price = $seller_commodity_product->charge_price[$charge_key];
                                                                            $other_charges_operator = $seller_commodity_product->operator[$charge_key];

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
                                                                            @foreach ($variation['value'] as $value)
                                                                                <td>{{ $value['value'] }}</td>
                                                                            @endforeach
                                                                            <td>{{ $variation['quantity'] }}</td>
                                                                            <td>₹ {{ formatIndianNumber($variation['price']) }}</td>
                                                                            {{-- <td>₹ {{ formatIndianNumber($final_variation_price) }}</td> --}}
                                                                            <td>₹ {{ formatIndianNumber($final_variation_price + $list_data->base_price) }}</td>
                                                                        </tr>

                                                                    @endforeach
                                                                </tbody>
                                                            </table>
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

                        @if ($set_enquiry_data)
                            @php
                                $total_variation_price = 0;
                                $total_transport_price = 0;
                            @endphp
                            <div class="modal fade bd-example-modal-lg" id="updatePrice" tabindex="-1" aria-labelledby="updatePriceLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <x-loader />
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updatePriceLabel">
                                                <b>{{ $set_enquiry_data->getUser->name}} ({{ getSellerType($set_enquiry_data->user_id) }})</b>,
                                                <b>Brand</b> : {{ $set_enquiry_data->getBrand->name }},
                                                <b>State</b> : {{ $set_enquiry_data->getSellerCommodityProduct->getStatePrice[0]->state }},
                                                <b>City</b> : {{ $set_enquiry_data->getSellerCommodityProduct->getStatePrice[0]->city }}
                                                {{-- <b>Base Price</b> : {{ $set_enquiry_data->base_price }} --}}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            @foreach ($set_enquiry_data->value[0]['value'] as $variation_heading)
                                                                <th>{{ $variation_heading['name'] }}</th>
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
                                                                        $variation_price = $variation['price'] + str_replace(',', '', $set_enquiry_data_base_price);
                                                                        $total_variation_price += $variation_price;

                                                                        $total_transport_price +=  $variation['quantity'] * $transport_price;
                                                                    @endphp
                                                                    ₹ {{ formatIndianNumber($variation_price) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td style="border-right: hidden;">
                                                                <label for="transport_price" class="form-label">Transport Price</label>
                                                            </td>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value']) }}">
                                                                <input type="text" class="form-control" id="transport_price" placeholder="Enter Transport Price" wire:model="transport_price" oninput="formatIndianCurrency(this)">
                                                                @error('transport_price') <small class="text-danger">{{ $message }}</small>@enderror

                                                            </td>

                                                            <td style="border-right: hidden;">
                                                                <label for="base_price" class="form-label">Base Price</label>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="text" class="form-control" id="base_price" placeholder="Enter Base Price" wire:model="set_enquiry_data_base_price" oninput="formatIndianCurrency(this)">
                                                                @error('set_enquiry_data_base_price') <small class="text-danger">{{ $message }}</small>@enderror
                                                            </td>

                                                        </tr>

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
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
                                                                    ({{ ucfirst($set_enquiry_data->getSellerCommodityProduct->commission_type) }})
                                                                </label>
                                                            </td>
                                                            <td colspan="2">
                                                                <input type="text" class="form-control" id="commission" placeholder="Enter Commission Price" wire:model="commission" oninput="formatIndianCurrency(this)">
                                                                @error('commission') <small class="text-danger">{{ $message }}</small>@enderror
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;"></td>
                                                            <td style="border-right: hidden;">
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                <label for="" class="form-label">Total</label>
                                                            </td>
                                                            <td colspan="2">
                                                                {{-- ₹ {{ formatIndianNumber($total_transport_price) }}
                                                                <br> --}}
                                                                ₹ {{ formatIndianNumber($total_variation_price) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" wire:click="markSeller()">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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
