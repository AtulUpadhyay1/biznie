<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4> {{ $page_title }} - {{ $data->unique_id }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{ route('admin.commodity-product-enquiry.index') }}"
                            class="btn btn-secondary btn-sm" wire:navigate><i
                                class="bi bi-arrow-left"></i>Back</a>
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
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Enquiry ID</dt>
                                            <dd>{{ $main_product_enquiry->unique_id }}</dd>
                                        </div>

                                        <div>
                                            <dt>Category</dt>
                                            <dd>{{ $main_product_enquiry->getCommodityProduct->getCategory->name }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Product</dt>
                                            <dd>{{ $main_product_enquiry->getCommodityProduct->name }}</dd>
                                        </div>

                                        <div>
                                            <dt>Brand</dt>
                                            <dd>{{ $main_product_enquiry->getBrand->name }}</dd>
                                        </div>

                                        <div>
                                            <dt>Purpose</dt>
                                            <dd>{{ $main_product_enquiry->purpose }}</dd>
                                        </div>

                                        <div>
                                            <dt>Description</dt>
                                            <dd>{{ $main_product_enquiry->description }}</dd>
                                        </div>

                                        @if ($main_product_enquiry->quality)
                                            <div>
                                                <dt>Quality</dt>
                                                <dd>
                                                    {{ $main_product_enquiry->quality['name'] }} –
                                                    ₹
                                                    {{ formatIndianNumber($main_product_enquiry->quality['price']) }}
                                                </dd>
                                            </div>
                                        @endif

                                        @if ($main_product_enquiry->packaging_charge)
                                            <div>
                                                <dt>Packaging Charge</dt>
                                                <dd>
                                                    {{ $main_product_enquiry->packaging_charge['name'] }} –
                                                    ₹
                                                    {{ formatIndianNumber($main_product_enquiry->packaging_charge['charge']) }}
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
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
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->company_name }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $main_product_enquiry->getUser->phone }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->gst_number }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 1</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->address_line_one }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 2</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->address_line_two }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->city }}</dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->state }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ $main_product_enquiry->getUser->getUserDetail->postal_code }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Credit Days</dt>
                                            <dd>{{ $main_product_enquiry->getUser->credit_days }} Days</dd>
                                        </div>
                                    </dl>
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
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['company_name'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['phone'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['gst'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 1</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['address_line_one'] }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 2</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['address_line_two'] }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['state'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $main_product_enquiry->billing_address['city'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>
                                                {{ $main_product_enquiry->consignee_detail['pin_code'] ??
                                                    ($main_product_enquiry->billing_address['pincode'] ?? '') }}
                                            </dd>
                                        </div>
                                    </dl>
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
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['company_name'] ?? '--' }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['phone'] ?? '--' }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['gst'] ?? '--' }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 1</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['address_line_one'] ?? '--' }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 2</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['address_line_two'] ?? '--' }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['state'] ?? '--' }}</dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $main_product_enquiry->consignee_detail['city'] ?? '--' }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>
                                                {{ $main_product_enquiry->consignee_detail['pin_code'] ?? ($data->billing_address['pincode'] ?? '--') }}
                                            </dd>
                                        </div>
                                    </dl>
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
                                    @if ($loading_address)
                                        <dl class="bz-kv-list">
                                            <div>
                                                <dt>Address Line One</dt>
                                                <dd>{{ $loading_address->address_line_one ?? '--' }}</dd>
                                            </div>

                                            <div>
                                                <dt>Address Line Two</dt>
                                                <dd>{{ $loading_address->address_line_two ?? '--' }}</dd>
                                            </div>

                                            <div>
                                                <dt>City</dt>
                                                <dd>{{ $loading_address->city ?? '--' }}</dd>
                                            </div>

                                            <div>
                                                <dt>State</dt>
                                                <dd>{{ $loading_address->state ?? '--' }}</dd>
                                            </div>

                                            <div>
                                                <dt>Pincode</dt>
                                                <dd>{{ $loading_address->pincode ?? '--' }}</dd>
                                            </div>
                                        </dl>
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
                                <b>Message: </b> {{ $data->message ?? '---' }} <br>
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
                        </div> --}}

                        <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'transporter' ? 'active' : '' }}"
                                    id="transporter-tab"
                                    href="{{ route('admin.commodity-product-enquiry.sellerReply', $hidden_id) }}?active_tab=transporter"
                                    aria-controls="transporter" wire:navigate>Transporter Reply</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $active_tab == 'seller' ? 'active' : '' }}" id="seller-tab"
                                    href="{{ route('admin.commodity-product-enquiry.sellerReply', $hidden_id) }}?active_tab=seller"
                                    aria-controls="seller" wire:navigate>Seller Reply</a>
                            </li>
                        </ul>
                        <div class="tab-content border border-top-0 p-3" id="myTabContent">
                            <div class="tab-pane fade {{ $active_tab == 'transporter' ? 'show active' : '' }}"
                                id="transporter" role="tabpanel" aria-labelledby="transporter-tab">
                                <div class="row">

                                    <div class="col-12 text-end">
                                        @if ($selected_transporter_id)
                                            <button class="btn btn-danger btn-sm my-2"
                                                title="Mark enquiry to transporter" wire:click="markTransporter()"><i
                                                    class="bi bi-check2-circle"></i>Mark Transporter</button>
                                        @endif
                                    </div>
                                    <div class="accordion" id="transporter_price">
                                        @foreach ($transporter_list as $transporter_data)
                                            <div class="accordion-item">
                                                <div class="row">
                                                    <div class="col-1 text-center mt-3">
                                                        <input type="radio"
                                                            id="transporter_{{ $transporter_data->id }}"
                                                            class="form-check-input"
                                                            value="{{ $transporter_data->id }}"
                                                            wire:model.live="selected_transporter_id">
                                                        <label class="form-check-label visually-hidden"
                                                            for="transporter_{{ $transporter_data->id }}">Select
                                                            transporter</label>
                                                        <button class="btn btn-secondary btn-sm ms-2"
                                                            title="Update Price" data-bs-toggle="modal"
                                                            data-bs-target="#updateTransporterPrice_{{ $transporter_data->id }}"
                                                            wire:click="setTransporterPrice({{ $transporter_data->id }})"><i
                                                                class="bi bi-pencil-square"></i></button>
                                                    </div>
                                                    <div class="col-11">
                                                        <h2 class="accordion-header"
                                                            id="transporter_heading_{{ $transporter_data->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#transporter_collapse_{{ $transporter_data->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="transporter_collapse_{{ $transporter_data->id }}">
                                                                <b>{{ $transporter_data->getUser->name }}
                                                                    ({{ $transporter_data->getUser->phone }})
                                                                </b>,
                                                                &nbsp;
                                                                <b>Price</b> : ₹
                                                                {{ formatIndianNumber($transporter_data->min_price) }}
                                                                - ₹
                                                                {{ formatIndianNumber($transporter_data->max_price) }}
                                                                &nbsp;
                                                                <b>Updated Price</b> : ₹
                                                                {{ $transporter_data->price ? formatIndianNumber($transporter_data->price) : 'NA' }}
                                                            </button>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade"
                                                id="updateTransporterPrice_{{ $transporter_data->id }}" tabindex="-1"
                                                aria-labelledby="updateTransporterPriceLable_{{ $transporter_data->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false"
                                                wire:ignore.self>
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="updateTransporterPriceLable_{{ $transporter_data->id }}">
                                                                Update Transporter Price</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="btn-close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="transporter_price_{{ $transporter_data->id }}"
                                                                class="form-label">Transporter Price</label>
                                                            <input type="number" class="form-control"
                                                                id="transporter_price_{{ $transporter_data->id }}"
                                                                placeholder="Enter Transporter Price"
                                                                wire:model="transporter_price">
                                                            @error('transporter_price')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                wire:click="updateTransporterPrice()">Update</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade {{ $active_tab == 'seller' ? 'show active' : '' }}"
                                id="seller" role="tabpanel" aria-labelledby="seller-tab">
                                <div class="row">
                                    <div class="col-8">
                                        {{-- <h5 class="my-3">Seller Reply</h5> --}}
                                    </div>
                                    <div class="col-4 text-end">
                                        @if ($selected_enquiry_id)
                                            <button class="btn btn-danger btn-sm my-2" title="Update Price"
                                                data-bs-toggle="modal" data-bs-target="#updatePrice"
                                                wire:click="updatePriceForm()"><i
                                                    class="bi bi-pencil-square"></i>Update Price</button>
                                            {{-- <button class="btn btn-primary btn-xs my-2" title="Mark enquiry to seller" wire:click="markSeller()">Mark Seller</button> --}}
                                        @endif
                                    </div>

                                    <div class="accordion" id="seller_price">
                                        @foreach ($list as $list_data)
                                            @php
                                                $repliedStatus = collect($list_data->history)->firstWhere(
                                                    'status',
                                                    'Replied',
                                                );
                                            @endphp
                                            <div class="accordion-item">
                                                <div class="row">
                                                    <div class="col-1 text-center mt-3">
                                                        <input type="radio" id="seller_{{ $list_data->id }}"
                                                            class="form-check-input" value="{{ $list_data->id }}"
                                                            wire:model.live="selected_enquiry_id">
                                                        <label class="form-check-label visually-hidden"
                                                            for="seller_{{ $list_data->id }}">Select seller</label>
                                                        <button class="btn btn-secondary btn-sm ms-2"
                                                            title="Update Price" data-bs-toggle="modal"
                                                            data-bs-target="#updateBasePrice_{{ $list_data->id }}"
                                                            wire:click="setBasePrice({{ $list_data->id }})"><i
                                                                class="bi bi-pencil-square"></i></button>
                                                    </div>
                                                    <div class="col-11">
                                                        @php
                                                            $address = $list_data->loading_address[0];
                                                            $city = isset($address['city']) ? $address['city'] : '';
                                                            $state = isset($address['state']) ? $address['state'] : '';

                                                            $seller_commodity_product = App\Models\SellerCommodityProduct::where(
                                                                'user_id',
                                                                $list_data->user_id,
                                                            )
                                                                ->where(
                                                                    'commodity_product_id',
                                                                    $list_data->commodity_product_id,
                                                                )
                                                                ->where('brand_id', $list_data->brand_id)
                                                                ->first();

                                                            $defaul_ex_price = 0;
                                                            $base_price = 0;
                                                            if (
                                                                $seller_commodity_product->base_price !=
                                                                $list_data->base_price
                                                            ) {
                                                                $base_price = $list_data->base_price;
                                                            } else {
                                                                $base_price = $seller_commodity_product->base_price;
                                                            }

                                                            $state = $state
                                                                ? $state
                                                                : $list_data->getSellerCommodityProduct
                                                                    ->getStatePrice[0]->state;
                                                            $city = $city
                                                                ? $city
                                                                : $list_data->getSellerCommodityProduct
                                                                    ->getStatePrice[0]->city;
                                                            $default_price = getDefaultCommodityProductVariationPrice(
                                                                $list_data->commodity_product_id,
                                                                $list_data->brand_id,
                                                                $state,
                                                                $city,
                                                            );
                                                            $loading_charge = $seller_commodity_product->loading_charge;
                                                            $insurance_charge =
                                                                $seller_commodity_product->insurance_charge;
                                                            $quality_charge = $seller_commodity_product->quality_charge;
                                                            $gst = $seller_commodity_product->gst;

                                                            $extra_charges = 0;
                                                            $other_charges = [];
                                                            foreach (
                                                                $seller_commodity_product->charge_name
                                                                as $charge_key => $charge_name
                                                            ) {
                                                                $other_charges_arr['name'] = $charge_name;
                                                                $other_charges_arr['price'] = isset(
                                                                    $seller_commodity_product->charge_price[
                                                                        $charge_key
                                                                    ],
                                                                )
                                                                    ? $seller_commodity_product->charge_price[
                                                                        $charge_key
                                                                    ]
                                                                    : '0';
                                                                $other_charges_arr['operator'] = isset(
                                                                    $seller_commodity_product->operator[$charge_key],
                                                                )
                                                                    ? $seller_commodity_product->operator[$charge_key]
                                                                    : '';

                                                                if ($other_charges_arr['operator']) {
                                                                    if ($other_charges_arr['operator'] == '+') {
                                                                        $extra_charges += $other_charges_arr['price'];
                                                                    } elseif ($other_charges_arr['operator'] == '-') {
                                                                        $extra_charges -= $other_charges_arr['price'];
                                                                    } elseif ($other_charges_arr['operator'] == '*') {
                                                                        $extra_charges += 0;
                                                                    } elseif ($other_charges_arr['operator'] == '/') {
                                                                        $extra_charges += 0;
                                                                    } elseif ($other_charges_arr['operator'] == '%') {
                                                                        $extra_charges += 0;
                                                                    }
                                                                }
                                                                $other_charges[] = $other_charges_arr;
                                                            }

                                                            $gauge_diff = $default_price;
                                                            $all_charges =
                                                                $loading_charge +
                                                                $insurance_charge +
                                                                $quality_charge +
                                                                $extra_charges;
                                                            $total_amount = $base_price + $gauge_diff + $all_charges;
                                                            $tax_amount = round(($total_amount * $gst) / 100);
                                                            $defaul_ex_price = $total_amount + $tax_amount;
                                                            // $gst_amount         += $tax;
                                                        @endphp
                                                        <h2 class="accordion-header"
                                                            id="heading_{{ $list_data->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapse_{{ $list_data->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse_{{ $list_data->id }}">
                                                                <b>{{ $list_data->getUser->getBusiness->name }}
                                                                    ({{ getSellerType($list_data->user_id) }}) </b>,
                                                                @php
                                                                    $default_variation = getDefaultCommodityProductVariation(
                                                                        $list_data->commodity_product_id,
                                                                        $list_data->brand_id,
                                                                        $state,
                                                                        $city,
                                                                    );
                                                                @endphp
                                                                @if ($default_variation)
                                                                    @foreach ($default_variation->value as $default_variations)
                                                                        <b
                                                                            class="ms-1">{{ $default_variations['name'] }}:</b>
                                                                        {{ $default_variations['value'] }}
                                                                        @if (
                                                                            $default_variation->getCommodityProduct->unit &&
                                                                                $default_variation->getCommodityProduct->unit[$default_variations['name']]
                                                                        )
                                                                            ({{ getProductUnit($default_variation->getCommodityProduct->unit[$default_variations['name']])->short_name }})
                                                                        @endif ,
                                                                    @endforeach
                                                                @endif
                                                                <b class="ms-1">Base Price</b> : ₹
                                                                {{ formatIndianNumber($base_price) }},
                                                                <b class="ms-1">Ex Price</b> : ₹
                                                                {{ formatIndianNumber($defaul_ex_price) }}
                                                                <b class="ms-1">
                                                                    <span title="View Calculation"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#viewCaculation_{{ $list_data->id }}">
                                                                        <i class="bi bi-info-circle text-danger"></i>
                                                                    </span>
                                                                </b>
                                                                @if ($repliedStatus)
                                                                    <b class="ms-1">Status: </b><span
                                                                        class="bz-status bz-status--success">{{ $repliedStatus['status'] }}</span>
                                                                    <span
                                                                        class="ms-1">{{ \Carbon\Carbon::parse($repliedStatus['created_at'])->format('d-m-Y H:i:s') }}</span>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                    </div>
                                                </div>
                                                <div id="collapse_{{ $list_data->id }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="heading_{{ $list_data->id }}"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="col-md-6">
                                                                <dl class="bz-kv-list">
                                                                    <div>
                                                                        <dt>Name</dt>
                                                                        <dd>{{ $list_data->getUser->name }}</dd>
                                                                    </div>
                                                                    <div>
                                                                        <dt>Company Name</dt>
                                                                        <dd>{{ $list_data->getUser->getBusiness->name }}
                                                                        </dd>
                                                                    </div>
                                                                    <div>
                                                                        <dt>GST</dt>
                                                                        <dd>{{ $list_data->getUser->getSellerKycDetail ? $list_data->getUser->getSellerKycDetail->gst_number : '--' }}
                                                                        </dd>
                                                                    </div>
                                                                    <div>
                                                                        <dt>Phone</dt>
                                                                        <dd>{{ $list_data->getUser->phone }}</dd>
                                                                    </div>
                                                                    <div>
                                                                        <dt>Brand</dt>
                                                                        <dd>{{ $list_data->getBrand->name }}</dd>
                                                                    </div>
                                                                    <div>
                                                                        <dt>State</dt>
                                                                        <dd>{{ $list_data->getSellerCommodityProduct->getStatePrice[0]->state }}
                                                                        </dd>
                                                                    </div>
                                                                    <div>
                                                                        <dt>City</dt>
                                                                        <dd>{{ $list_data->getSellerCommodityProduct->getStatePrice[0]->city }}
                                                                        </dd>
                                                                    </div>
                                                                </dl>
                                                            </div>
                                                            @foreach ($list_data->loading_address as $loading_address)
                                                                <div class="col-md-6">
                                                                    @if ($loading_address)
                                                                        <div class="bz-section-label">Loading Address
                                                                        </div>
                                                                        <dl class="bz-kv-list">
                                                                            <div>
                                                                                <dt>Pincode</dt>
                                                                                <dd>{{ $loading_address['pin_code'] }}
                                                                                </dd>
                                                                            </div>
                                                                            <div>
                                                                                <dt>Address Line One</dt>
                                                                                <dd>{{ isset($loading_address['address_line_one']) ? $loading_address['address_line_one'] : '--' }}
                                                                                </dd>
                                                                            </div>
                                                                            <div>
                                                                                <dt>Address Line Two</dt>
                                                                                <dd>{{ isset($loading_address['address_line_two']) ? $loading_address['address_line_two'] : '--' }}
                                                                                </dd>
                                                                            </div>
                                                                            <div>
                                                                                <dt>City</dt>
                                                                                <dd>{{ isset($loading_address['city']) ? $loading_address['city'] : '--' }}
                                                                                </dd>
                                                                            </div>
                                                                            <div>
                                                                                <dt>State</dt>
                                                                                <dd>{{ isset($loading_address['state']) ? $loading_address['state'] : '--' }}
                                                                                </dd>
                                                                            </div>
                                                                            <div>
                                                                                <dt>Loading Position</dt>
                                                                                <dd>{{ isset($loading_address['loading_position']) ? $loading_address['loading_position'] : '--' }}
                                                                                    / Days</dd>
                                                                            </div>
                                                                        </dl>
                                                                    @else
                                                                        <b>Loading Address Not Found.</b>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @if ($list_data->quality)
                                                                <div class="col-md-6 mt-3">
                                                                    <dl class="bz-kv-list">
                                                                        <div>
                                                                            <dt>Quality</dt>
                                                                            <dd>{{ $list_data->quality['name'] }}</dd>
                                                                        </div>
                                                                        <div>
                                                                            <dt>Price</dt>
                                                                            <dd>₹
                                                                                {{ formatIndianNumber($list_data->quality['price']) }}
                                                                            </dd>
                                                                        </div>
                                                                    </dl>
                                                                </div>
                                                            @endif
                                                            @if ($list_data->packaging_charge)
                                                                <div class="col-md-6 mt-3">
                                                                    <dl class="bz-kv-list">
                                                                        <div>
                                                                            <dt>Packaging Charge</dt>
                                                                            <dd>{{ $list_data->packaging_charge['name'] }}
                                                                            </dd>
                                                                        </div>
                                                                        <div>
                                                                            <dt>Price</dt>
                                                                            <dd>₹
                                                                                {{ formatIndianNumber($list_data->packaging_charge['charge']) }}
                                                                            </dd>
                                                                        </div>
                                                                    </dl>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="table-responsive mt-3">
                                                            <table class="custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        @foreach ($list_data->value[0]['value'] as $variation_heading)
                                                                            <th>{{ $variation_heading['name'] }}
                                                                                @if ($variation_heading['unit'])
                                                                                    ({{ $variation_heading['unit']['short_name'] }})
                                                                                @endif
                                                                            </th>
                                                                        @endforeach
                                                                        <th>Quantity (MT)</th>
                                                                        <th>Gauge Diff.</th>
                                                                        <th>EX Price</th>
                                                                        <th>Ex Price x Qty</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $total_charges = 0;
                                                                        $ex_price = 0;
                                                                        $seller_commodity_product =
                                                                            $list_data->getSellerCommodityProduct;
                                                                        $base_price = $list_data->base_price;
                                                                        // $transport_price = $list_data->transport_price;
                                                                        // $commission =  $seller_commodity_product->commission;

                                                                        foreach (
                                                                            $seller_commodity_product->packaging_type
                                                                            as $packaging_charge
                                                                        ) {
                                                                            $packaging_price = isset(
                                                                                $seller_commodity_product
                                                                                    ->packaging_type_price[
                                                                                    $packaging_charge
                                                                                ],
                                                                            )
                                                                                ? $seller_commodity_product
                                                                                    ->packaging_type_price[
                                                                                    $packaging_charge
                                                                                ]
                                                                                : 0;
                                                                            $total_charges += $packaging_price;
                                                                        }

                                                                        foreach (
                                                                            $seller_commodity_product->charge_name
                                                                            as $charge_key => $charge_name
                                                                        ) {
                                                                            $other_charges_price = isset(
                                                                                $seller_commodity_product->charge_price[
                                                                                    $charge_key
                                                                                ],
                                                                            )
                                                                                ? $seller_commodity_product
                                                                                    ->charge_price[$charge_key]
                                                                                : 0;
                                                                            $other_charges_operator = isset(
                                                                                $seller_commodity_product->operator[
                                                                                    $charge_key
                                                                                ],
                                                                            )
                                                                                ? $seller_commodity_product->operator[
                                                                                    $charge_key
                                                                                ]
                                                                                : '';
                                                                            if ($other_charges_operator) {
                                                                                if ($other_charges_operator == '+') {
                                                                                    $total_charges += $other_charges_price;
                                                                                } elseif (
                                                                                    $other_charges_operator == '-'
                                                                                ) {
                                                                                    $total_charges -= $other_charges_price;
                                                                                } elseif (
                                                                                    $other_charges_operator == '*'
                                                                                ) {
                                                                                    $total_charges +=
                                                                                        $ex_price *
                                                                                        $other_charges_price;
                                                                                } elseif (
                                                                                    $other_charges_operator == '/'
                                                                                ) {
                                                                                    $total_charges +=
                                                                                        $ex_price /
                                                                                        $other_charges_price;
                                                                                } elseif (
                                                                                    $other_charges_operator == '%'
                                                                                ) {
                                                                                    $total_charges +=
                                                                                        $ex_price *
                                                                                        ($other_charges_price / 100);
                                                                                }
                                                                            }
                                                                        }

                                                                        if ($seller_commodity_product->is_quality) {
                                                                            foreach (
                                                                                $seller_commodity_product->quality
                                                                                as $quality_key => $quality
                                                                            ) {
                                                                                $other_quantity_charge_arr[
                                                                                    'name'
                                                                                ] = $quality;
                                                                                $other_quantity_price =
                                                                                    $seller_commodity_product
                                                                                        ->quality_price[$quality_key];
                                                                                $total_charges += $other_quantity_price;
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    @foreach ($list_data->value as $variation)
                                                                        @php
                                                                            $final_variation_price = 0;
                                                                            $tax =
                                                                                (($variation['price'] +
                                                                                    $list_data->base_price) *
                                                                                    $seller_commodity_product->gst) /
                                                                                100;
                                                                            $per_unit_price =
                                                                                $variation['price'] +
                                                                                $list_data->base_price +
                                                                                $tax +
                                                                                $total_charges;
                                                                            $final_price =
                                                                                $per_unit_price *
                                                                                $variation['quantity'];
                                                                            $final_variation_price += $final_price;
                                                                            // $gst_amount         += $tax;
                                                                        @endphp

                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            @php
                                                                                $variation_arr = [];
                                                                                $total_quantity = 0;
                                                                                $ex_price = 0;
                                                                                $quality_price =
                                                                                    $list_data->quality &&
                                                                                    isset($list_data->quality['price'])
                                                                                        ? $list_data->quality['price']
                                                                                        : '0';
                                                                                $packaging_charge_price =
                                                                                    $list_data->packaging_charge &&
                                                                                    isset(
                                                                                        $list_data->packaging_charge[
                                                                                            'charge'
                                                                                        ],
                                                                                    )
                                                                                        ? $list_data->packaging_charge[
                                                                                            'charge'
                                                                                        ]
                                                                                        : '0';
                                                                                $all_charges =
                                                                                    $loading_charge +
                                                                                    $insurance_charge +
                                                                                    $quality_charge +
                                                                                    $quality_price +
                                                                                    $packaging_charge_price +
                                                                                    $extra_charges;
                                                                            @endphp
                                                                            @foreach ($variation['value'] as $value)
                                                                                @php
                                                                                    $variation_data_arr['id'] =
                                                                                        $value['id'];
                                                                                    $variation_data_arr['name'] =
                                                                                        $value['name'];
                                                                                    $variation_data_arr['value'] =
                                                                                        $value['value'];
                                                                                    $variation_arr[] = $variation_data_arr;
                                                                                    $quantity = $variation['quantity'];
                                                                                @endphp
                                                                                <td>{{ $value['value'] }}</td>
                                                                            @endforeach
                                                                            @php
                                                                                $gauge_diff = App\Models\SellerCommodityProductStatePrice::where(
                                                                                    'user_id',
                                                                                    $list_data->user_id,
                                                                                )
                                                                                    ->where(
                                                                                        'commodity_product_id',
                                                                                        $list_data->commodity_product_id,
                                                                                    )
                                                                                    ->where(
                                                                                        'brand_id',
                                                                                        $list_data->brand_id,
                                                                                    )
                                                                                    ->where('state', $state)
                                                                                    ->where('city', $city)
                                                                                    // ->whereJsonContains('value', $variation['value'])
                                                                                    ->where(function ($query) use (
                                                                                        $variation_arr,
                                                                                    ) {
                                                                                        foreach (
                                                                                            $variation_arr
                                                                                            as $variation
                                                                                        ) {
                                                                                            $query->whereJsonContains(
                                                                                                'value',
                                                                                                $variation,
                                                                                            );
                                                                                        }
                                                                                    })
                                                                                    ->first();

                                                                                if ($gauge_diff) {
                                                                                    $price = $gauge_diff->price;
                                                                                    $per_unit_price =
                                                                                        $gauge_diff->price +
                                                                                        $base_price +
                                                                                        $all_charges;
                                                                                    $tax = round(
                                                                                        ($per_unit_price * $gst) / 100,
                                                                                    );
                                                                                    $per_unit_price += $tax;
                                                                                    $final_price =
                                                                                        $per_unit_price * $quantity;
                                                                                    $total_quantity += $quantity;
                                                                                    $ex_price += $final_price;
                                                                                }
                                                                            @endphp
                                                                            <td>{{ $variation['quantity'] }}</td>
                                                                            <td>₹
                                                                                {{ $gauge_diff ? $gauge_diff->price : 0 }}
                                                                            </td>
                                                                            <td>₹
                                                                                {{ formatIndianNumber($per_unit_price) }}
                                                                                / MT</td>
                                                                            <td>₹ {{ formatIndianNumber($ex_price) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="viewCaculation_{{ $list_data->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="viewCaculationLable_{{ $list_data->id }}"
                                                    aria-hidden="true" data-bs-backdrop="static"
                                                    data-bs-keyboard="false" wire:ignore.self>
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="viewCaculationLable_{{ $list_data->id }}">
                                                                    Calculation</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="btn-close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    <b>Base Price:</b> ₹
                                                                    {{ formatIndianNumber($list_data->base_price) }}
                                                                    <br>
                                                                    @if ($default_price > 0)
                                                                        <b>Guage Difference:</b> + ₹
                                                                        {{ formatIndianNumber($default_price) }} <br>
                                                                    @endif
                                                                    @if ($loading_charge > 0)
                                                                        <b>Loading Charge:</b> + ₹
                                                                        {{ formatIndianNumber($loading_charge) }} <br>
                                                                    @endif

                                                                    @if ($insurance_charge > 0)
                                                                        <b>Insurance Charge:</b> + ₹
                                                                        {{ formatIndianNumber($insurance_charge) }}
                                                                        <br>
                                                                    @endif

                                                                    @foreach ($other_charges as $other_charge)
                                                                        @if ($other_charge['price'] > 0)
                                                                            <b>{{ $other_charge['name'] }}:</b>
                                                                            {{ $other_charge['operator'] }} ₹
                                                                            {{ formatIndianNumber($other_charge['price']) }}<br>
                                                                        @endif
                                                                    @endforeach
                                                                    @php
                                                                        $total_cal =
                                                                            $list_data->base_price +
                                                                            $default_price +
                                                                            $loading_charge +
                                                                            $insurance_charge +
                                                                            $extra_charges;
                                                                    @endphp
                                                                    <br>
                                                                    <b>Total:</b> ₹
                                                                    {{ formatIndianNumber($total_cal) }}<br>
                                                                    <b>GST:</b> + {{ $gst }} % <br>
                                                                    <span class="text-success">
                                                                        <b>Ex Price:</b> ₹
                                                                        {{ formatIndianNumber($defaul_ex_price) }}
                                                                        <br>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade " id="updateBasePrice_{{ $list_data->id }}"
                                                tabindex="-1"
                                                aria-labelledby="updateBasePriceLable_{{ $list_data->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false"
                                                wire:ignore.self>
                                                <div class="modal-dialog modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="updateBasePriceLable_{{ $list_data->id }}">Update
                                                                Base Price</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="btn-close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="base_price_{{ $list_data->id }}"
                                                                class="form-label">Base Price</label>
                                                            <input type="number" class="form-control"
                                                                id="base_price_{{ $list_data->id }}"
                                                                placeholder="Enter Base Price"
                                                                wire:model="base_price">
                                                            @error('base_price')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                wire:click="updateBasePrice()">Update</button>
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
                        <div class="modal fade bd-example-modal-xl" id="updatePrice" tabindex="-1"
                            aria-labelledby="updatePriceLabel" aria-hidden="true" data-bs-backdrop="static"
                            data-bs-keyboard="false" wire:ignore.self>
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updatePriceLabel">
                                            @if ($set_enquiry_data)
                                                <b>{{ $set_enquiry_data->getUser->name }}
                                                    ({{ getSellerType($set_enquiry_data->user_id) }})</b>,
                                                <b>Brand</b> : {{ $set_enquiry_data->getBrand->name }},
                                                <b>State</b> :
                                                {{ $selected_seller_commodity_product->getStatePrice[0]->state }},
                                                <b>City</b> :
                                                {{ $selected_seller_commodity_product->getStatePrice[0]->city }}
                                            @else
                                                <b>Loading...</b>
                                            @endif
                                            {{-- <b>Base Price</b> : {{ $set_enquiry_data->base_price }} --}}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="btn-close"></button>
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
                                                                    @if ($variation_heading['unit'])
                                                                        ({{ $variation_heading['unit']['short_name'] }})
                                                                    @endif
                                                                </th>
                                                            @endforeach
                                                            <th>Quantity (MT)</th>
                                                            <th>Gauge Diff.</th>
                                                            <th>EX Price</th>
                                                            <th>Ex Price x Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($set_enquiry_data->value as $variation)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                @foreach ($variation['value'] as $value)
                                                                    <td>{{ $value['value'] }}</td>
                                                                @endforeach
                                                                <td>
                                                                    {{-- {{ $variation['quantity'] }} -> {{ $set_enquiry_quantity[$loop->index] }} <br> --}}
                                                                    <input type="number" class="form-control"
                                                                        wire:model.live="set_enquiry_quantity.{{ $loop->index }}"
                                                                        min="1">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        wire:model="set_enquiry_data_price.{{ $loop->index }}">
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        // $variation_price = ($variation['price'] != "" ? $variation['price'] : 0) + ($set_enquiry_data_base_price != "" ? $set_enquiry_data_base_price : 0) + $all_charges;
                                                                        // $total_variation_price += $variation_price;
                                                                        $variation['quantity'] =
                                                                            $set_enquiry_quantity[$loop->index] != ''
                                                                                ? $set_enquiry_quantity[$loop->index]
                                                                                : 0;
                                                                        $transport_price =
                                                                            $transport_price != ''
                                                                                ? $transport_price
                                                                                : 0;
                                                                        $variation_price =
                                                                            ($variation['price'] != ''
                                                                                ? $variation['price']
                                                                                : 0) +
                                                                            ($set_enquiry_data_base_price != ''
                                                                                ? $set_enquiry_data_base_price
                                                                                : 0) +
                                                                            $all_charges;
                                                                        if (
                                                                            $selected_seller_commodity_product->commission_type ==
                                                                            'exclude'
                                                                        ) {
                                                                            $variation_price +=
                                                                                $commission != '' ? $commission : 0;
                                                                        }
                                                                        $tax = round(
                                                                            ($variation_price *
                                                                                $selected_seller_commodity_product->gst) /
                                                                                100,
                                                                        );
                                                                        $variation_price += $tax;
                                                                        $final_price =
                                                                            $variation_price * $variation['quantity'];
                                                                        $total_variation_price += $final_price;
                                                                        $total_transport_price +=
                                                                            $variation['quantity'] * $transport_price;
                                                                        $total_quantity += $variation['quantity'];
                                                                    @endphp
                                                                    ₹ {{ formatIndianNumber($variation_price) }}
                                                                </td>
                                                                <td>
                                                                    ₹ {{ formatIndianNumber($final_price) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($set_enquiry_data->quality || $set_enquiry_data->packaging_charge)
                                                            <tr>
                                                                <td colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}"></td>
                                                                @if ($set_enquiry_data->quality)
                                                                    <th>
                                                                        Quality
                                                                    </th>
                                                                    <td>
                                                                        {{ $set_enquiry_data->quality['name'] }}: ₹
                                                                        {{ formatIndianNumber($set_enquiry_data->quality['price']) }}
                                                                    </td>
                                                                @endif
                                                                @if ($set_enquiry_data->packaging_charge)
                                                                    <th>
                                                                        Packaging
                                                                    </th>
                                                                    <td>
                                                                        {{ $set_enquiry_data->packaging_charge['name'] }}:
                                                                        ₹
                                                                        {{ formatIndianNumber($set_enquiry_data->packaging_charge['charge']) }}
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <th>
                                                                <label for="transport_price"
                                                                    class="form-label">Transport Price / MT</label>
                                                            </th>
                                                            <td
                                                                colspan="{{ count($set_enquiry_data->value[0]['value']) }}">
                                                                <input type="number" class="form-control"
                                                                    id="transport_price"
                                                                    placeholder="Enter Transport Price"
                                                                    wire:model.live="transport_price">
                                                                @error('transport_price')
                                                                    <small
                                                                        class="text-danger">{{ $message }}</small>
                                                                @enderror

                                                            </td>

                                                            <th>
                                                                <label for="base_price" class="form-label">Base
                                                                    Price</label>
                                                            </th>
                                                            <td colspan="3">
                                                                <input type="number" class="form-control"
                                                                    id="base_price" placeholder="Enter Base Price"
                                                                    wire:model.live="set_enquiry_data_base_price">
                                                                @error('set_enquiry_data_base_price')
                                                                    <small
                                                                        class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>

                                                        </tr>

                                                        <tr>
                                                            <td
                                                                colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                @if ($selected_transporter)
                                                                    <dl class="bz-kv-list">
                                                                        <div>
                                                                            <dt>Name</dt>
                                                                            <dd>{{ $selected_transporter->getUser->name }}
                                                                            </dd>
                                                                        </div>
                                                                        <div>
                                                                            <dt>Phone</dt>
                                                                            <dd>{{ $selected_transporter->getUser->phone }}
                                                                            </dd>
                                                                        </div>
                                                                        <div>
                                                                            <dt>Price</dt>
                                                                            <dd>₹
                                                                                {{ formatIndianNumber($selected_transporter->min_price) }}
                                                                                - ₹
                                                                                {{ formatIndianNumber($selected_transporter->max_price) }}
                                                                            </dd>
                                                                        </div>
                                                                        <div>
                                                                            <dt>Given Price</dt>
                                                                            <dd>₹
                                                                                {{ formatIndianNumber($selected_transporter->price) }}
                                                                            </dd>
                                                                        </div>
                                                                    </dl>
                                                                @else
                                                                    <a href="{{ route('admin.commodity-product-enquiry.sellerReply', $hidden_id) }}?active_tab=transporter"
                                                                        wire:navigate>Select Transpoter</a>
                                                                @endif
                                                            </td>
                                                            <th>
                                                                <label for="commission" class="form-label">Commission
                                                                    / MT<br>
                                                                    ({{ ucfirst($selected_seller_commodity_product->commission_type) }})
                                                                </label>
                                                            </th>
                                                            <td colspan="3">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        id="commission"
                                                                        placeholder="Enter Commission Price"
                                                                        wire:model.live="commission"
                                                                        {{ $is_editable_commission ? 'readonly' : '' }}>
                                                                    @if ($selected_seller_commodity_product->commission_type == 'include')
                                                                        <span
                                                                            class="input-group-text input-group-addon">
                                                                            <div class="form-check form-switch">
                                                                                <input type="checkbox"
                                                                                    class="form-check-input"
                                                                                    id="commission_editable"
                                                                                    wire:model.live="is_editable_commission">
                                                                                <label class="form-check-label"
                                                                                    for="commission_editable"></label>
                                                                            </div>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @error('commission')
                                                                    <small
                                                                        class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th>
                                                                <label for="seller_credit_days"
                                                                    class="form-label">Seller Credit Days</label>
                                                            </th>
                                                            <td
                                                                colspan="{{ $selected_seller_commodity_product->loading_charge > 0 ? count($set_enquiry_data->value[0]['value']) : count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                <input type="number" class="form-control"
                                                                    id="seller_credit_days"
                                                                    placeholder="Enter Seller Credit Days"
                                                                    wire:model="seller_credit_days">
                                                                @error('seller_credit_days')
                                                                    <small
                                                                        class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>
                                                            @if ($selected_seller_commodity_product->loading_charge > 0)
                                                                <th>
                                                                    {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                    Loading
                                                                        Charge
                                                                </th>
                                                                <td colspan="3">
                                                                    ₹
                                                                    {{ formatIndianNumber($selected_seller_commodity_product->loading_charge) }}
                                                                </td>
                                                            @endif
                                                        </tr>

                                                        <tr>
                                                            <th>
                                                                <label for="customer_credit_days"
                                                                    class="form-label">Buyer Credit Days</label>
                                                            </th>
                                                            <td
                                                                colspan="{{ $selected_seller_commodity_product->insurance_charge > 0 ? count($set_enquiry_data->value[0]['value']) : count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                <input type="number" class="form-control"
                                                                    id="customer_credit_days"
                                                                    placeholder="Enter Customer Credit Days"
                                                                    wire:model="customer_credit_days">
                                                                @error('customer_credit_days')
                                                                    <small
                                                                        class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>
                                                            @if ($selected_seller_commodity_product->insurance_charge > 0)
                                                                <th>
                                                                    {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                    Insurance
                                                                        Charge
                                                                </th>
                                                                <td colspan="3">
                                                                    ₹
                                                                    {{ formatIndianNumber($selected_seller_commodity_product->insurance_charge) }}
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                <label for="load_within" class="form-label">Load
                                                                    Within</label>
                                                            </th>
                                                            <td
                                                                colspan="{{ $selected_seller_commodity_product->insurance_charge > 0 ? count($set_enquiry_data->value[0]['value']) : count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                <input type="number" class="form-control"
                                                                    id="load_within" placeholder="Enter Load Within"
                                                                    wire:model="load_within">
                                                                @error('load_within')
                                                                    <small
                                                                        class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>
                                                        </tr>
                                                        @if ($selected_seller_commodity_product->quality_charge > 0)
                                                            <tr>
                                                                <td colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                </td>
                                                                <th>
                                                                    {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                    Quality
                                                                        Charge
                                                                </th>
                                                                <td colspan="3">
                                                                    ₹
                                                                    {{ formatIndianNumber($selected_seller_commodity_product->quality_charge) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @foreach ($selected_seller_commodity_product->charge_name as $charge_key => $charge_name)
                                                            @if ($selected_seller_commodity_product->charge_price[$charge_key] > 0)
                                                                <tr>
                                                                    <td colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                    </td>
                                                                    <th>
                                                                        {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                        {{ $charge_name }}
                                                                    </th>
                                                                    <td colspan="3">
                                                                        {{ $selected_seller_commodity_product->operator[$charge_key] }}
                                                                        {{ $selected_seller_commodity_product->charge_price[$charge_key] }}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        @if ($selected_seller_commodity_product->gst)
                                                            <tr>
                                                                <td colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                </td>
                                                                <th>
                                                                    {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                    GST
                                                                        Charge
                                                                </th>
                                                                <td colspan="3">
                                                                    ₹
                                                                    {{ formatIndianNumber($selected_seller_commodity_product->gst) }}
                                                                    %
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($selected_seller_commodity_product->tcs > 0)
                                                            <tr>
                                                                <td colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                                </td>
                                                                <th>
                                                                    {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                    TCS
                                                                        Charge
                                                                </th>
                                                                <td colspan="3">
                                                                    ₹
                                                                    {{ formatIndianNumber($selected_seller_commodity_product->tcs) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <td colspan="{{ count($set_enquiry_data->value[0]['value']) + 1 }}">
                                                            </td>
                                                            <th>
                                                                {{-- <label for="" class="form-label">Total Transport Price</label> --}}
                                                                Total
                                                            </th>
                                                            <td colspan="3">
                                                                {{-- ₹ {{ formatIndianNumber($total_transport_price) }}
                                                                <br> --}}
                                                                ₹ {{ formatIndianNumber($total_variation_price) }}
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-danger btn-sm" wire:click="markSeller()"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="markSeller">Update</span>
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
