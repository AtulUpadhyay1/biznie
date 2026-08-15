<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4>{{ $page_title }}</h4>
                        <small> ( {{ $data->order_id }} ) </small>
                        <span
                            class="bz-status {{ $data->status == 'cancel' ? 'bz-status--danger' : ($data->status == 'pending' ? 'bz-status--warning' : 'bz-status--info') }} ms-1">{{ $data->status }}
                        </span>
                    </div>
                    <div class="bz-toolbar">
                        <a href="{{ route('admin.commodity-product-order.index') }}" class="btn btn-secondary btn-sm"
                            wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                    <div class="w-100 text-center">
                        {{-- <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a> --}}
                        @include('admin.commodity_product_order.menu', ['is_active' => 'show'])
                    </div>
                </div>

                <div class="card-body">
                    @php
                        $custmoer_enq_data = [
                            'id' => $enquiry_data->id,
                            'unique_id' => $enquiry_data->unique_id,
                            'order_id' => $enquiry_data->getCommodityProductOrder
                                ? $enquiry_data->getCommodityProductOrder->id
                                : null,
                            'brand' => $enquiry_data->getBrand
                                ? [
                                    'id' => $enquiry_data->getBrand->id,
                                    'name' => $enquiry_data->getBrand->name,
                                ]
                                : [],
                            'unit' => $enquiry_data->getCommodityProduct->getUnit
                                ? [
                                    'id' => $enquiry_data->getCommodityProduct->getUnit->id,
                                    'name' => $enquiry_data->getCommodityProduct->getUnit->name,
                                ]
                                : [],
                            'commodity_product' => $enquiry_data->getCommodityProduct
                                ? [
                                    'id' => $enquiry_data->getCommodityProduct->id,
                                    'name' => $enquiry_data->getCommodityProduct->name,
                                    'thumbnail' => $enquiry_data->getCommodityProduct->thumbnail
                                        ? imageUrl($enquiry_data->getCommodityProduct->thumbnail)
                                        : asset('common/images/no-photo.png'),

                                    'category' => $enquiry_data->getCommodityProduct->getCategory
                                        ? [
                                            'id' => $enquiry_data->getCommodityProduct->getCategory->id,
                                            'name' => $enquiry_data->getCommodityProduct->getCategory->name,
                                        ]
                                        : [],
                                ]
                                : [],

                            'origin_city' => $enquiry_data->origin_city,
                            'variation' => [],
                            'selected_quality' => $enquiry_data->quality,
                            'selected_packaging_charge' => $enquiry_data->packaging_charge,
                            'unit_price' => $enquiry_data->unit_price,
                            'billing_address' => $enquiry_data->billing_address,
                            'delivery_address' => $enquiry_data->delivery_address,
                            'consignee_detail' => $enquiry_data->consignee_detail,
                            'purpose' => $enquiry_data->purpose,
                            'description' => $enquiry_data->description,
                            'message' => $enquiry_data->message,
                            // 'price'             => $enquiry_data->price,
                            'packaging_charge' => [],
                            'other_charge' => [],
                            'other_quantity_charge' => [],
                            'transporter_detail' => [],
                            'total_quantity' => 0,
                            'base_price' => 0,
                            'loading_charge' => 0,
                            'insurance_charge' => 0,
                            'quality_charge' => 0,
                            'gst' => 0,
                            'tcs' => 0,
                            'gst_amount' => 0,
                            'tcs_amount' => 0,
                            'total_charges' => 0,
                            'commission_type' => '',
                            'commission' => 0,
                            'final_variation_price' => 0,
                            'ex_price' => 0,
                            'transport_price' => 0,
                            'for_price' => 0,
                            'required_booking_amount' => 0,
                            'is_mark' => false,
                            'status' => $enquiry_data->status,
                            'created_at' => dateTimeFormat($enquiry_data->created_at),
                            'credit_days' => $enquiry_data->getUser->credit_days,
                        ];
                        $markedSeller = $enquiry_data->getMarkedSellerProductEnquiry;
                        if ($markedSeller) {
                            $custmoer_enq_data['credit_days'] = $markedSeller->customer_credit_days
                                ? $markedSeller->customer_credit_days
                                : $enquiry_data->getUser->credit_days;
                            // $custmoer_enq_data['variation']  = $markedSeller->value;
                            $custmoer_enq_data['base_price'] = $markedSeller->base_price;
                            $custmoer_enq_data['transport_price'] = $markedSeller->transport_price;
                            $custmoer_enq_data['commission_type'] = $markedSeller->commission_type;
                            $custmoer_enq_data['commission'] = (int) $markedSeller->commission;
                            $custmoer_enq_data['is_mark'] = $markedSeller->is_mark ? true : false;
                            if ($custmoer_enq_data['commission_type'] == 'exclude') {
                                $custmoer_enq_data['base_price'] += $custmoer_enq_data['commission'];
                            }

                            $seller_commodity_product = App\Models\SellerCommodityProduct::where(
                                'user_id',
                                $markedSeller->user_id,
                            )
                                ->where('commodity_product_id', $markedSeller->commodity_product_id)
                                ->where('brand_id', $markedSeller->brand_id)
                                ->first();

                            $packaging_arr = [];
                            foreach ($seller_commodity_product->packaging_type ?? [] as $packaging_charge) {
                                $packaging_arr['name'] = getPackagingType($packaging_charge)->name;
                                $packaging_arr['price'] = isset(
                                    $seller_commodity_product->packaging_type_price[$packaging_charge],
                                )
                                    ? $seller_commodity_product->packaging_type_price[$packaging_charge]
                                    : 0;
                                $custmoer_enq_data['total_charges'] += $packaging_arr['price'];
                                $custmoer_enq_data['packaging_charge'][] = $packaging_arr;
                            }

                            $other_charges_arr = [];
                            $extra_charges = 0;

                            foreach ($seller_commodity_product->charge_name ?? [] as $charge_key => $charge_name) {
                                $other_charges_arr['name'] = $charge_name;
                                $other_charges_arr['price'] = isset(
                                    $seller_commodity_product->charge_price[$charge_key],
                                )
                                    ? $seller_commodity_product->charge_price[$charge_key]
                                    : 0;
                                $other_charges_arr['operator'] = isset($seller_commodity_product->operator[$charge_key])
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

                                $custmoer_enq_data['other_charge'][] = $other_charges_arr;
                            }

                            if ($seller_commodity_product && $seller_commodity_product->is_quality) {
                                $other_quantity_charge_arr = [];
                                foreach ($seller_commodity_product->quality ?? [] as $quality_key => $quality) {
                                    $other_quantity_charge_arr['name'] = $quality;
                                    $other_quantity_charge_arr['quality_price'] =
                                        $seller_commodity_product->quality_price[$quality_key];
                                    $custmoer_enq_data['total_charges'] += $other_quantity_charge_arr['quality_price'];
                                    $custmoer_enq_data['other_quantity_charge'][] = $other_quantity_charge_arr;
                                }
                            }

                            foreach ($markedSeller->value as $variation) {
                                $variation['total_price'] =
                                    $variation['price'] +
                                    $custmoer_enq_data['base_price'] +
                                    $extra_charges +
                                    $seller_commodity_product->loading_charge +
                                    $seller_commodity_product->insurance_charge;
                                $variation['tax'] = round(
                                    ($variation['total_price'] * $seller_commodity_product->gst) / 100,
                                );
                                $variation['per_unit_price'] = $variation['total_price'] + $variation['tax'];
                                $variation['final_price'] = $variation['per_unit_price'] * $variation['quantity'];
                                $custmoer_enq_data['variation'][] = $variation;
                                $custmoer_enq_data['total_quantity'] += $variation['quantity'];
                                $custmoer_enq_data['final_variation_price'] += $variation['final_price'];
                                $custmoer_enq_data['gst_amount'] += $variation['tax'];
                            }

                            $custmoer_enq_data['loading_charge'] = $seller_commodity_product->loading_charge;
                            $custmoer_enq_data['insurance_charge'] = $seller_commodity_product->insurance_charge;
                            $custmoer_enq_data['quality_charge'] = $seller_commodity_product->quality_charge ?? 0;
                            $custmoer_enq_data['gst'] = $seller_commodity_product->gst;
                            $custmoer_enq_data['tcs'] = $seller_commodity_product->tcs;

                            $custmoer_enq_data['tcs_amount'] =
                                ($custmoer_enq_data['final_variation_price'] * $custmoer_enq_data['tcs_amount']) / 100;

                            $custmoer_enq_data['ex_price'] =
                                $custmoer_enq_data['final_variation_price'] + $custmoer_enq_data['tcs_amount'];

                            $custmoer_enq_data['for_price'] =
                                $custmoer_enq_data['ex_price'] +
                                $custmoer_enq_data['transport_price'] * $custmoer_enq_data['total_quantity'];
                            $custmoer_enq_data['required_booking_amount'] =
                                ($custmoer_enq_data['for_price'] * 30) / 100;
                            // if($custmoer_enq_data['commission_type'] == 'exclude'){
                            //     $custmoer_enq_data['final_variation_price'] += $custmoer_enq_data['commission'];
                            // }
                            // $custmoer_enq_data['status']     = $enquiry_data->getMarkedSellerProductEnquiry->status;
                        } else {
                            $custmoer_enq_data['variation'] = $enquiry_data->variation;
                        }
                        $markedTransporter = $enquiry_data->getMarkedTransporterEnquiry;
                        if ($markedTransporter) {
                            $custmoer_enq_data['transport_price'] = $markedTransporter->price;
                        }
                        $custmoer_enq_data['total_freight'] = 0;
                        if ($data->transporter_invoices && count($data->transporter_invoices) > 0) {
                            foreach ($data->transporter_invoices as $transporter_invoices) {
                                $custmoer_enq_data['total_freight'] += $transporter_invoices['amount'];
                            }
                        } else {
                            $custmoer_enq_data['total_freight'] =
                                $custmoer_enq_data['transport_price'] * $custmoer_enq_data['total_quantity'];
                        }
                    @endphp

                    @php
                        $seller_enq_data = [
                            'id' => $seller_enquiry_data->id,
                            'unique_id' => $seller_enquiry_data->unique_id,
                            'order_id' => $seller_enquiry_data->getCommodityProductOrder
                                ? $seller_enquiry_data->getCommodityProductOrder->id
                                : null,
                            'brand' => $seller_enquiry_data->getBrand
                                ? [
                                    'id' => $seller_enquiry_data->getBrand->id,
                                    'name' => $seller_enquiry_data->getBrand->name,
                                ]
                                : [],
                            'unit' => $seller_enquiry_data->getSellerCommodityProduct->getUnit
                                ? [
                                    'id' => $seller_enquiry_data->getSellerCommodityProduct->getUnit->id,
                                    'name' => $seller_enquiry_data->getSellerCommodityProduct->getUnit->name,
                                ]
                                : [],
                            'commodity_product' => $seller_enquiry_data->getSellerCommodityProduct
                                ? [
                                    'id' => $seller_enquiry_data->getSellerCommodityProduct->id,
                                    'name' => $seller_enquiry_data->getSellerCommodityProduct->name,
                                    'thumbnail' => $seller_enquiry_data->getSellerCommodityProduct->thumbnail
                                        ? imageUrl($seller_enquiry_data->getSellerCommodityProduct->thumbnail)
                                        : asset('common/images/no-photo.png'),

                                    'category' => $seller_enquiry_data->getSellerCommodityProduct->getCategory
                                        ? [
                                            'id' => $seller_enquiry_data->getSellerCommodityProduct->getCategory->id,
                                            'name' =>
                                                $seller_enquiry_data->getSellerCommodityProduct->getCategory->name,
                                        ]
                                        : [],
                                ]
                                : [],

                            'origin_city' => $seller_enquiry_data->origin_city,
                            'variation' => [],
                            'billing_address' => $seller_enquiry_data->billing_address,
                            'delivery_address' => $seller_enquiry_data->delivery_address,
                            'consignee_detail' => $seller_enquiry_data->consignee_detail,
                            'purpose' => $seller_enquiry_data->purpose,
                            'description' => $seller_enquiry_data->description,
                            'message' => $seller_enquiry_data->message,
                            'delivery_by' => $seller_enquiry_data->delivery_by,
                            'selected_quality' => $seller_enquiry_data->quality,
                            'selected_packaging_charge' => $seller_enquiry_data->packaging_charge,
                            'loading_address' => $seller_enquiry_data->loading_address,
                            // 'price'             => $seller_enquiry_data->price,
                            'packaging_charge' => [],
                            'other_charge' => [],
                            'other_quantity_charge' => [],
                            'total_quantity' => 0,
                            'base_price' => $seller_enquiry_data->base_price,
                            'loading_charge' => 0,
                            'insurance_charge' => 0,
                            'quality_charge' => 0,
                            'gst' => 0,
                            'tcs' => 0,
                            'gst_amount' => 0,
                            'tcs_amount' => 0,
                            'total_charges' => 0,
                            'commission_type' => $seller_enquiry_data->commission_type,
                            'commission' => 0,
                            'final_variation_price' => 0,
                            'ex_price' => 0,
                            'transport_price' => 0,
                            'for_price' => 0,
                            'required_booking_amount' => 0,
                            'is_mark' => false,
                            'status' => $seller_enquiry_data->status,
                            'created_at' => dateTimeFormat($seller_enquiry_data->created_at),
                            'credit_days' => $seller_enquiry_data->seller_credit_days
                                ? $seller_enquiry_data->seller_credit_days
                                : auth()->user()->credit_days,
                        ];

                        $seller_commodity_product = App\Models\SellerCommodityProduct::where(
                            'user_id',
                            $seller_enquiry_data->user_id,
                        )
                            ->where('commodity_product_id', $seller_enquiry_data->commodity_product_id)
                            ->where('brand_id', $seller_enquiry_data->brand_id)
                            ->first();
                        $seller_enq_data['loading_charge'] = $seller_commodity_product->loading_charge;
                        $seller_enq_data['insurance_charge'] = $seller_commodity_product->insurance_charge;
                        $seller_enq_data['quality_charge'] = $seller_commodity_product->quality_charge;
                        $seller_enq_data['gst'] = $seller_commodity_product->gst;

                        $extra_charges = 0;
                        $other_charges = [];
                        foreach ($seller_commodity_product->charge_name as $charge_key => $charge_name) {
                            $other_charges_arr['name'] = $charge_name;
                            $other_charges_arr['price'] = isset($seller_commodity_product->charge_price[$charge_key])
                                ? $seller_commodity_product->charge_price[$charge_key]
                                : '0';
                            $other_charges_arr['operator'] = isset($seller_commodity_product->operator[$charge_key])
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
                        $seller_enq_data['other_charges'] = $other_charges;

                        $seller_enq_data['quality_price'] =
                            $seller_enquiry_data->quality && isset($seller_enquiry_data->quality['price'])
                                ? $seller_enquiry_data->quality['price']
                                : '0';
                        $seller_enq_data['packaging_charge_price'] =
                            $seller_enquiry_data->packaging_charge &&
                            isset($seller_enquiry_data->packaging_charge['charge'])
                                ? $seller_enquiry_data->packaging_charge['charge']
                                : '0';
                        $all_charges =
                            $seller_enq_data['loading_charge'] +
                            $seller_enq_data['insurance_charge'] +
                            $seller_enq_data['quality_charge'] +
                            $seller_enq_data['quality_price'] +
                            $seller_enq_data['packaging_charge_price'] +
                            $extra_charges;

                        $selected_variations = $seller_enquiry_data->value;
                        $variation_arr = [];
                        foreach ($selected_variations as $variation) {
                            $get_state_price = App\Models\SellerCommodityProductStatePrice::find($variation['id']);
                            if ($get_state_price) {
                                $variation['price'] = $get_state_price->price;
                                $variation['per_unit_price'] =
                                    $get_state_price->price + $seller_enquiry_data->base_price + $all_charges;
                                $variation['total_price'] = $variation['per_unit_price'];
                                $variation['tax'] = round(
                                    ($variation['per_unit_price'] * $seller_enq_data['gst']) / 100,
                                );
                                $variation['per_unit_price'] += $variation['tax'];
                                $variation['final_price'] = $variation['per_unit_price'] * $variation['quantity'];
                                $seller_enq_data['total_quantity'] += $variation['quantity'];
                                $seller_enq_data['ex_price'] += $variation['final_price'];
                            }
                            $variation_arr[] = $variation;
                        }
                        $seller_enq_data['variation'] = $variation_arr;
                        $seller_enq_data['commission'] = $seller_enquiry_data->commission;
                    @endphp
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
                                            <dt>Enquiry Id</dt>
                                            <dd>{{ $enquiry_data->unique_id }}</dd>
                                        </div>
                                        <div>
                                            <dt>Category</dt>
                                            <dd>{{ $enquiry_data->getCommodityProduct->getCategory->name }}</dd>
                                        </div>
                                        <div>
                                            <dt>Product</dt>
                                            <dd>{{ $enquiry_data->getCommodityProduct->name }}</dd>
                                        </div>
                                        <div>
                                            <dt>Brand</dt>
                                            <dd>{{ $enquiry_data->getBrand->name }}</dd>
                                        </div>
                                        <div>
                                            <dt>Purpose</dt>
                                            <dd>{{ $enquiry_data->purpose }}</dd>
                                        </div>
                                        <div>
                                            <dt>Description</dt>
                                            <dd>{{ $enquiry_data->description }}</dd>
                                        </div>
                                        @if ($custmoer_enq_data['selected_quality'])
                                            <div>
                                                <dt>Quality</dt>
                                                <dd>{{ $custmoer_enq_data['selected_quality']['name'] }} : ₹
                                                    {{ $custmoer_enq_data['selected_quality']['price'] }}</dd>
                                            </div>
                                        @endif
                                        @if ($custmoer_enq_data['selected_packaging_charge'])
                                            <div>
                                                <dt>Packaging</dt>
                                                <dd>{{ $custmoer_enq_data['selected_packaging_charge']['name'] }} :
                                                    ₹
                                                    {{ $custmoer_enq_data['selected_packaging_charge']['charge'] }}
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
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->company_name }}</dd>
                                        </div>
                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $enquiry_data->getUser->phone }}</dd>
                                        </div>
                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->gst_number }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line1</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->address_line_one }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line2</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->address_line_two }}</dd>
                                        </div>
                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->city }}</dd>
                                        </div>
                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->state }}</dd>
                                        </div>
                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->postal_code }}</dd>
                                        </div>
                                        <div>
                                            <dt>Credit Days</dt>
                                            <dd>{{ $custmoer_enq_data['credit_days'] }} Days</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Seller Details</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    @php
                                        $seller = $enquiry_data->getMarkedSellerProductEnquiry->getUser;
                                        $sellerDetail = $seller->getSellerKycDetail;
                                    @endphp
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $seller->getBusiness->name }}</dd>
                                        </div>
                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $seller->phone }}</dd>
                                        </div>
                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $sellerDetail->gst_number }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line One</dt>
                                            <dd>{{ $sellerDetail->address_line_one }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line Two</dt>
                                            <dd>{{ $sellerDetail->address_line_two }}</dd>
                                        </div>
                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $sellerDetail->city }}</dd>
                                        </div>
                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $sellerDetail->state }}</dd>
                                        </div>
                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ $sellerDetail->postal_code }}</dd>
                                        </div>
                                        <div>
                                            <dt>Credit Days</dt>
                                            <dd>{{ $seller_enq_data['credit_days'] }} Days</dd>
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
                                            <dd>{{ $enquiry_data->billing_address['company_name'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $enquiry_data->billing_address['phone'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $enquiry_data->billing_address['gst'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line1</dt>
                                            <dd>{{ $enquiry_data->billing_address['address_line_one'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line2</dt>
                                            <dd>{{ $enquiry_data->billing_address['address_line_two'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $enquiry_data->billing_address['state'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $enquiry_data->billing_address['city'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ isset($enquiry_data->consignee_detail['pin_code']) ? $enquiry_data->consignee_detail['pin_code'] : (isset($enquiry_data->billing_address['pincode']) ? $enquiry_data->billing_address['pincode'] : '') }}
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
                                            <dd>{{ $enquiry_data->consignee_detail['company_name'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['phone'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['gst'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line1</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['address_line_one'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Address Line2</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['address_line_two'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['state'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['city'] }}</dd>
                                        </div>
                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ isset($enquiry_data->consignee_detail['pin_code']) ? $enquiry_data->consignee_detail['pin_code'] : (isset($enquiry_data->billing_address['pincode']) ? $enquiry_data->billing_address['pincode'] : '') }}
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
                                                <dd>{{ $loading_address->address_line_one }}</dd>
                                            </div>
                                            <div>
                                                <dt>Address Line Two</dt>
                                                <dd>{{ $loading_address->address_line_two }}</dd>
                                            </div>
                                            <div>
                                                <dt>City</dt>
                                                <dd>{{ $loading_address->city }}</dd>
                                            </div>
                                            <div>
                                                <dt>State</dt>
                                                <dd>{{ $loading_address->state }}</dd>
                                            </div>
                                            <div>
                                                <dt>Pincode</dt>
                                                <dd>{{ $loading_address->pincode }}</dd>
                                            </div>
                                        </dl>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($markedSeller)

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h5>Product Pricing For Buyer</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <dl class="bz-kv-list">
                                            <div>
                                                <dt>Basic Price</dt>
                                                <dd>₹ {{ $custmoer_enq_data['base_price'] }} / Metric Ton</dd>
                                            </div>
                                            <div>
                                                <dt>Freight</dt>
                                                <dd>₹ {{ $custmoer_enq_data['transport_price'] }} / Metric Ton</dd>
                                            </div>
                                            @if ($custmoer_enq_data['selected_quality'])
                                                <div>
                                                    <dt>Quality</dt>
                                                    <dd>{{ $custmoer_enq_data['selected_quality']['name'] }} : ₹
                                                        {{ $custmoer_enq_data['selected_quality']['price'] }}</dd>
                                                </div>
                                            @endif
                                            @if ($custmoer_enq_data['selected_packaging_charge'])
                                                <div>
                                                    <dt>Packaging</dt>
                                                    <dd>{{ $custmoer_enq_data['selected_packaging_charge']['name'] }}
                                                        : ₹
                                                        {{ $custmoer_enq_data['selected_packaging_charge']['charge'] }}
                                                    </dd>
                                                </div>
                                            @endif
                                        </dl>
                                        <table class="table table-hover mt-3">
                                            <thead>
                                                <tr>
                                                    <th>Requirements</th>
                                                    <th>Ex Price</th>
                                                    <th>Ex Price x Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($custmoer_enq_data['variation'] as $variation)
                                                    <tr>
                                                        <td>
                                                            @foreach ($variation['value'] as $value)
                                                                {{ $value['value'] }}
                                                                {{ $value['unit']['short_name'] }}@if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                            - {{ $variation['quantity'] }} MT
                                                        </td>
                                                        <td>
                                                            ₹ {{ formatIndianNumber($variation['per_unit_price']) }} /
                                                            MT
                                                            <button type="button" class="btn btn-sm btn-icon"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#customerPriceCalculationModal{{ $loop->index }}"
                                                                title="View Calculation">
                                                                <i class="bi bi-info-circle"></i>
                                                            </button>
                                                        </td>
                                                        <td>₹ {{ formatIndianNumber($variation['final_price']) }}</td>
                                                    </tr>

                                                    <!-- Customer Price Calculation Modal -->
                                                    <div class="modal fade"
                                                        id="customerPriceCalculationModal{{ $loop->index }}"
                                                        tabindex="-1"
                                                        aria-labelledby="customerPriceCalculationModalLabel{{ $loop->index }}"
                                                        aria-hidden="true" wire:ignore.self>
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="customerPriceCalculationModalLabel{{ $loop->index }}">
                                                                        View Calculation</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <b>Base Price:</b> Rs
                                                                    {{ formatIndianNumber($custmoer_enq_data['base_price']) }}
                                                                    <br>
                                                                    <b>Guage Difference:</b> + Rs
                                                                    {{ formatIndianNumber($variation['price']) }} <br>
                                                                    @if ($custmoer_enq_data['loading_charge'] > 0)
                                                                        <b>Loading Charge:</b> + Rs
                                                                        {{ formatIndianNumber($custmoer_enq_data['loading_charge']) }}
                                                                        <br>
                                                                    @endif
                                                                    @if ($custmoer_enq_data['insurance_charge'] > 0)
                                                                        <b>Insurance Charge:</b> + Rs
                                                                        {{ formatIndianNumber($custmoer_enq_data['insurance_charge']) }}
                                                                        <br>
                                                                    @endif
                                                                    @if ($custmoer_enq_data['quality_charge'] > 0)
                                                                        <b>Quality Charge: </b>+ Rs
                                                                        {{ formatIndianNumber($custmoer_enq_data['quality_charge']) }}
                                                                        <br>
                                                                    @endif
                                                                    @foreach ($custmoer_enq_data['other_charge'] as $charge)
                                                                        <b>{{ $charge['name'] }}:</b>
                                                                        {{ $charge['operator'] }} Rs
                                                                        {{ formatIndianNumber($charge['price']) }} <br>
                                                                    @endforeach
                                                                    <br>
                                                                    <b>Total:</b> Rs
                                                                    {{ formatIndianNumber($variation['total_price']) }}
                                                                    <br>
                                                                    <b>GST:</b> + {{ $custmoer_enq_data['gst'] }} %
                                                                    <br>
                                                                    <b>Ex Price:</b> Rs
                                                                    {{ formatIndianNumber($variation['per_unit_price']) }}
                                                                    <br>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h5>Product Pricing For Seller</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <dl class="bz-kv-list">
                                            <div>
                                                <dt>Basic Price</dt>
                                                <dd>₹ {{ $seller_enq_data['base_price'] }} / Metric Ton</dd>
                                            </div>
                                            <div>
                                                <dt>Freight</dt>
                                                <dd>₹ {{ $custmoer_enq_data['transport_price'] }} / Metric Ton</dd>
                                            </div>
                                            @if ($custmoer_enq_data['selected_quality'])
                                                <div>
                                                    <dt>Quality</dt>
                                                    <dd>{{ $custmoer_enq_data['selected_quality']['name'] }} : ₹
                                                        {{ $custmoer_enq_data['selected_quality']['price'] }}</dd>
                                                </div>
                                            @endif
                                            @if ($custmoer_enq_data['selected_packaging_charge'])
                                                <div>
                                                    <dt>Packaging</dt>
                                                    <dd>{{ $custmoer_enq_data['selected_packaging_charge']['name'] }}
                                                        : ₹
                                                        {{ $custmoer_enq_data['selected_packaging_charge']['charge'] }}
                                                    </dd>
                                                </div>
                                            @endif
                                        </dl>
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Requirements</th>
                                                    <th>Ex Price</th>
                                                    <th>Ex Price x Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($seller_enq_data['variation'] as $variation)
                                                    <tr>
                                                        <td>
                                                            @foreach ($variation['value'] as $value)
                                                                {{ $value['value'] }}
                                                                {{ $value['unit']['short_name'] }}@if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                            - {{ $variation['quantity'] }} MT
                                                        </td>
                                                        <td>
                                                            ₹ {{ formatIndianNumber($variation['per_unit_price']) }} /
                                                            MT
                                                            <button type="button" class="btn btn-sm btn-icon"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#sellerPriceCalculationModal{{ $loop->index }}"
                                                                title="View Calculation">
                                                                <i class="bi bi-info-circle"></i>
                                                            </button>
                                                            <div class="modal fade"
                                                                id="sellerPriceCalculationModal{{ $loop->index }}"
                                                                tabindex="-1"
                                                                aria-labelledby="sellerPriceCalculationModalLabel{{ $loop->index }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="sellerPriceCalculationModalLabel{{ $loop->index }}">
                                                                                View Calculation</h5>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <b>Base Price:</b> Rs
                                                                            {{ formatIndianNumber($seller_enq_data['base_price']) }}
                                                                            <br>
                                                                            <b>Guage Difference:</b> + Rs
                                                                            {{ formatIndianNumber($variation['price']) }}
                                                                            <br>
                                                                            @if ($seller_enq_data['loading_charge'] > 0)
                                                                                <b>Loading Charge:</b> + Rs
                                                                                {{ formatIndianNumber($seller_enq_data['loading_charge']) }}
                                                                                <br>
                                                                            @endif
                                                                            @if ($seller_enq_data['insurance_charge'] > 0)
                                                                                <b>Insurance Charge:</b> + Rs
                                                                                {{ formatIndianNumber($seller_enq_data['insurance_charge']) }}
                                                                                <br>
                                                                            @endif
                                                                            @if ($seller_enq_data['quality_charge'] > 0)
                                                                                <b>Quality Charge: </b>+ Rs
                                                                                {{ formatIndianNumber($seller_enq_data['quality_charge']) }}
                                                                                <br>
                                                                            @endif
                                                                            @foreach ($seller_enq_data['other_charges'] as $charge)
                                                                                <b>{{ $charge['name'] }}:</b>
                                                                                {{ $charge['operator'] }} Rs
                                                                                {{ formatIndianNumber($charge['price']) }}
                                                                                <br>
                                                                            @endforeach
                                                                            <br>
                                                                            <b>Total:</b> Rs
                                                                            {{ formatIndianNumber($variation['total_price']) }}
                                                                            <br>
                                                                            <b>GST:</b> +
                                                                            {{ $seller_enq_data['gst'] }} % <br>
                                                                            <b>Ex Price:</b> Rs
                                                                            {{ formatIndianNumber($variation['per_unit_price']) }}
                                                                            <br>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary btn-sm"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>₹ {{ formatIndianNumber($variation['final_price']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- @foreach ($seller_enq_data['variation'] as $variation)
                                            <p>
                                                @foreach ($variation['value'] as $value)
                                                    Size : {{ $value['value'] }} {{ $value['unit']['short_name'] }} <br>
                                                @endforeach
                                                Quantity : {{ $variation['quantity'] }} MT <br>
                                                Ex-Factory Price : {{ formatIndianNumber($variation['per_unit_price']) }} Metric Ton <br>
                                                For Price : {{ formatIndianNumber($variation['per_unit_price'] + $custmoer_enq_data['transport_price']) }} Metric Ton
                                                <br>
                                                <span class="text-danger">Ex-Price x Qty : {{ formatIndianNumber($variation['final_price']) }}</span>
                                            </p> <br>
                                        @endforeach --}}
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h5>Buyer Price Calculation</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Credit Days</strong></td>
                                                    <td>{{ $custmoer_enq_data['credit_days'] }} Days</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Basic Price</strong></td>
                                                    <td>₹ {{ formatIndianNumber($custmoer_enq_data['base_price']) }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Ex-Factory Price</strong></td>
                                                    <td>₹
                                                        {{ formatIndianNumber($custmoer_enq_data['final_variation_price']) }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Total Quantity</strong></td>
                                                    <td>{{ $custmoer_enq_data['total_quantity'] }} Metric Ton</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Total Ex-Factory Price</strong></td>
                                                    <td>₹
                                                        {{ formatIndianNumber($custmoer_enq_data['final_variation_price']) }}
                                                    </td>
                                                </tr>

                                                @if ($custmoer_enq_data['commission_type'] == 'exclude')
                                                    <tr>
                                                        <td><strong>Commission (Excluded)</strong></td>
                                                        <td>₹
                                                            {{ formatIndianNumber($custmoer_enq_data['commission']) }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                <tr>
                                                    <td><strong>Load Within</strong></td>
                                                    <td>{{ $seller_enquiry_data->load_within }} Days</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="text-success">
                                                        <small>
                                                            Rate included – loading charges, insurance charges,
                                                            packaging charges,
                                                            quality inspection charges, TCS & GST
                                                        </small>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                        <hr>
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Freight</strong></td>
                                                    <td>
                                                        ₹
                                                        {{ formatIndianNumber($custmoer_enq_data['transport_price']) }}
                                                        / Metric Ton
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="text-success">
                                                        <small>
                                                            Freight may change ±100.<br>
                                                            (Freight depends on demand & supply of trucks on loading
                                                            day)
                                                        </small>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Total Amount Payable<br>(Ex-Factory + Freight)</strong>
                                                    </td>
                                                    <td>
                                                        ₹
                                                        {{ formatIndianNumber(
                                                            $custmoer_enq_data['final_variation_price'] +
                                                                $custmoer_enq_data['total_quantity'] * $custmoer_enq_data['transport_price'],
                                                        ) }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2">
                                                        <small class="text-danger fw-bold">Disclaimer</small><br>
                                                        <small class="text-success fw-bold">
                                                            The invoice amount will be updated after the goods are
                                                            loaded
                                                            and the final quantity is confirmed.
                                                        </small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h5>Seller Price Calculation</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Credit Days</strong></td>
                                                    <td>{{ $seller_enq_data['credit_days'] }} Days</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Basic Price</strong></td>
                                                    <td>₹ {{ formatIndianNumber($seller_enq_data['base_price']) }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Total Quantity</strong></td>
                                                    <td>{{ $seller_enq_data['total_quantity'] }} MT</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Total Ex-Factory Price</strong></td>
                                                    <td>₹ {{ formatIndianNumber($seller_enq_data['ex_price']) }}</td>
                                                </tr>

                                                @if ($seller_enq_data['commission_type'] == 'include')
                                                    @php
                                                        $commission =
                                                            $seller_enq_data['commission'] *
                                                            ($seller_enq_data['total_quantity'] +
                                                                $seller_enq_data['gst'] / 100);
                                                    @endphp
                                                    <tr>
                                                        <td><strong>Commission (Included)</strong></td>
                                                        <td>
                                                            ₹ {{ formatIndianNumber($commission) }}
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $seller_enq_data['commission'] }} / MT ·
                                                                {{ $seller_enq_data['gst'] }}% GST
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endif

                                                <tr>
                                                    <td colspan="2" class="text-success">
                                                        <small>
                                                            Rate included – loading charges, insurance charges,
                                                            packaging charges,
                                                            quality inspection charges & GST.
                                                            TCS may apply (if applicable).
                                                            <br>
                                                            The credit period starts from the date the invoice is
                                                            generated.
                                                        </small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="bz-panel">
                                <dl class="bz-kv-list">
                                    <div>
                                        <dt>Total Amount</dt>
                                        <dd class="fw-bold">₹
                                            {{ formatIndianNumber($data->buyer_invoice_amount ?? $data->total_amount) }}</dd>
                                    </div>
                                    <div>
                                        <dt>Paid Amount</dt>
                                        <dd class="text-success fw-bold">₹
                                            {{ formatIndianNumber($data->paid_amount) }}</dd>
                                    </div>
                                    <div>
                                        <dt>Remaining Amount</dt>
                                        <dd class="text-danger fw-bold">
                                            ₹
                                            {{ formatIndianNumber($data->buyer_invoice_amount)
                                                ? formatIndianNumber($data->buyer_invoice_amount - $data->paid_amount)
                                                : formatIndianNumber($data->due_amount) }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p>
                                @if ($data->seller_credit_due_date)
                                    <b>Seller Credit Due Date:</b> {{ dateFormat($data->seller_credit_due_date) }}
                                    <br>
                                    ({{ \Carbon\Carbon::parse($data->seller_credit_due_date)->diffInDays(now()) + 1 }}
                                    day{{ \Carbon\Carbon::parse($data->seller_credit_due_date)->diffInDays(now()) + 1 > 1 ? 's' : '' }}
                                    left)
                                @endif
                                <br>
                                @if ($data->customer_credit_due_date)
                                    <b>Customer Credit Due Date:</b> {{ dateFormat($data->customer_credit_due_date) }}
                                    <br>
                                    ({{ \Carbon\Carbon::parse($data->customer_credit_due_date)->diffInDays(now()) + 1 }}
                                    day{{ \Carbon\Carbon::parse($data->customer_credit_due_date)->diffInDays(now()) + 1 > 1 ? 's' : '' }}
                                    left)
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-3 mb-2">
                            <div class="card">
                                <div class="card-body p-0">
                                    @if ($data->quality_check_image && count($data->quality_check_image) > 0)
                                        @foreach ($data->quality_check_image as $check_image)
                                            <a href="{{ imageUrl($check_image) }}" target="_blank">
                                                <img src="{{ imageUrl($check_image) }}" class="img-thumbnail"
                                                    alt="Quality Check Image" title="Quality Check Image">
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="text-center p-4">
                                            <button class="btn btn-sm btn-inverse-primary" type="button"
                                                data-bs-toggle="modal" data-bs-target="#fileUploadModal"
                                                wire:click="setUploadType('quality_check_image')">Upload</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <span>
                                        Quality Check
                                        @if ($data->quality_check_image_status && $data->quality_check_image_status != 'pending')
                                            <span
                                                class="bz-status {{ $data->quality_check_image_status == 'approved' ? 'bz-status--success' : 'bz-status--danger' }}">
                                                {{ ucfirst($data->quality_check_image_status) }} </span>
                                            By {{ ucfirst($data->quality_check_image_status_updated_by) }}
                                        @endif
                                    </span>
                                    @if (!$data->quality_check_image_status || $data->quality_check_image_status == 'pending')
                                        <span class="d-flex align-items-center gap-1">
                                            <button class="btn btn-icon btn-sm btn-secondary" title="Approved"
                                                wire:click="qualityCheckImageStatus('approved')"><i
                                                    class="bi bi-check-lg"></i></button>
                                            <button class="btn btn-icon btn-sm btn-danger" title="Rejected"
                                                wire:click="qualityCheckImageStatus('rejected')"><i
                                                    class="bi bi-x-lg"></i></button>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="card">
                                <div class="card-body p-0">
                                    @if ($data->quality_check_certificate)
                                        <a href="{{ imageUrl($data->quality_check_certificate) }}" target="_blank">
                                            <img src="{{ imageUrl($data->quality_check_certificate) }}"
                                                class="img-thumbnail" alt="Quality Check Certificate"
                                                title="Quality Check Certificate">
                                        </a>
                                    @else
                                        <div class="text-center p-4">
                                            <button class="btn btn-sm btn-inverse-primary" type="button"
                                                data-bs-toggle="modal" data-bs-target="#fileUploadModal"
                                                wire:click="setUploadType('quality_check_certificate')">Upload</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    Quality Check Certificate
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bz-toggle-row">
                                <span class="bz-toggle-row__label">
                                    <label class="form-label mb-0" for="customer_quality_check_visibility">
                                        Customer Quality Check Visibility
                                    </label>
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="It enable product order quality check visible to customer.">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </span>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input"
                                        id="customer_quality_check_visibility" wire:click="customerQualityCheck()"
                                        {{ $data->customer_quality_check_visibility ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 mb-2">
                            <div class="card">
                                <div
                                    class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h5 class="mb-0">Buyer Invoice List</h5>
                                    @if ($data->getDrivers->sum('amount') == 0)
                                        <div class="bz-toolbar">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#invoiceModal">
                                                <i class="bi bi-receipt-cutoff"></i>Add Invoice
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="custom-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Invoice</th>
                                                    <th>E - Waybill</th>
                                                    <th>E - Waybill Expiry Date</th>
                                                    <th>Transport Receipt</th>
                                                    <th>Amount</th>
                                                    <th>Debit Note</th>
                                                    <th>Debit Note Amount</th>
                                                    <th>Credit Note</th>
                                                    <th>Credit Note Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($data->getDrivers->sum('amount') > 0)
                                                    @foreach ($data->getDrivers as $driver_data)
                                                        <tr>
                                                            <td>{{ $loop->index + 1 }}</td>
                                                            <td>
                                                                <b>Vehicle Number:
                                                                </b>{{ $driver_data->vehicle_number }} <br>
                                                                <a href="{{ imageUrl($driver_data->invoice) }}"
                                                                    target="_blank">Invoice File <i
                                                                        class="bi bi-download"></i></a>
                                                            </td>
                                                            <td>
                                                                @if ($driver_data->ebill)
                                                                    <a href="{{ imageUrl($driver_data->ebill) }}"
                                                                        target="_blank">E-Bill <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($driver_data->ebill_expiry_date)
                                                                    {{ dateFormat($driver_data->ebill_expiry_date) }}
                                                                    <br>
                                                                    @php
                                                                        $expiryDate = \Carbon\Carbon::parse(
                                                                            $driver_data->ebill_expiry_date,
                                                                        );
                                                                        $daysLeft = $expiryDate->isToday()
                                                                            ? 0
                                                                            : ($expiryDate->isPast()
                                                                                ? 0
                                                                                : $expiryDate->diffInDays(now()) + 1);
                                                                    @endphp
                                                                    @if ($daysLeft <= 0)
                                                                        <b
                                                                            class="text-danger">{{ $daysLeft == 0 && $expiryDate->isToday() ? '0 days left' : 'Expired' }}</b>
                                                                    @elseif ($daysLeft <= 3)
                                                                        <b class="text-warning">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @else
                                                                        <b class="text-success">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($driver_data->transport_receipt)
                                                                    <a href="{{ imageUrl($driver_data->transport_receipt) }}"
                                                                        target="_blank">Transport Receipt <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹ {{ formatIndianNumber($driver_data->amount) }}
                                                            </td>
                                                            <td>
                                                                @if ($driver_data->debit_note)
                                                                    <a href="{{ imageUrl($driver_data->debit_note) }}"
                                                                        target="_blank">Debit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ $driver_data->debit_note_amount ? formatIndianNumber($driver_data->debit_note_amount) : '--' }}
                                                            </td>
                                                            <td>
                                                                @if ($driver_data->credit_note)
                                                                    <a href="{{ imageUrl($driver_data->credit_note) }}"
                                                                        target="_blank">Credit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ $driver_data->credit_note_amount ? formatIndianNumber($driver_data->credit_note_amount) : '--' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @elseif ($data->all_invoices && count($data->all_invoices) > 0)
                                                    @foreach ($data->all_invoices as $invoice)
                                                        <tr>
                                                            <td>{{ $loop->index + 1 }}</td>
                                                            <td>
                                                                <a href="{{ imageUrl($invoice['invoice_file']) }}"
                                                                    target="_blank">
                                                                    {{ $invoice['name'] }} <i
                                                                        class="bi bi-download"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if ($invoice['ebill'])
                                                                    <a href="{{ imageUrl($invoice['ebill']) }}"
                                                                        target="_blank">E-Bill <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($invoice['ebill_expiry_date'])
                                                                    {{ dateFormat($invoice['ebill_expiry_date']) }}
                                                                    <br>
                                                                    @php
                                                                        $expiryDate = \Carbon\Carbon::parse(
                                                                            $invoice['ebill_expiry_date'],
                                                                        );
                                                                        $daysLeft = $expiryDate->isToday()
                                                                            ? 0
                                                                            : ($expiryDate->isPast()
                                                                                ? 0
                                                                                : $expiryDate->diffInDays(now()) + 1);
                                                                    @endphp
                                                                    @if ($daysLeft <= 0)
                                                                        <b
                                                                            class="text-danger">{{ $daysLeft == 0 && $expiryDate->isToday() ? '0 days left' : 'Expired' }}</b>
                                                                    @elseif ($daysLeft <= 3)
                                                                        <b class="text-warning">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @else
                                                                        <b class="text-success">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($invoice['transport_receipt'])
                                                                    <a href="{{ imageUrl($invoice['transport_receipt']) }}"
                                                                        target="_blank">Transport Receipt <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹ {{ formatIndianNumber($invoice['amount']) }}
                                                            </td>
                                                            <td>
                                                                @if (isset($invoice['debit_note']))
                                                                    <a href="{{ imageUrl($invoice['debit_note']) }}"
                                                                        target="_blank">Debit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ isset($invoice['debit_note_amount']) ? formatIndianNumber($invoice['debit_note_amount']) : '--' }}
                                                            </td>
                                                            <td>
                                                                @if (isset($invoice['credit_note']))
                                                                    <a href="{{ imageUrl($invoice['credit_note']) }}"
                                                                        target="_blank">Credit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ isset($invoice['credit_note_amount']) ? formatIndianNumber($invoice['credit_note_amount']) : '--' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <x-table-no-data colspan="10" icon="bi-receipt"
                                                        title="No invoices yet"
                                                        text="No invoices have been added for this order." />
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 mb-2">
                            <div class="card">
                                <div
                                    class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h5 class="mb-0">Seller Invoice List</h5>
                                    @if ($data->getDrivers->sum('amount') == 0)
                                        <div class="bz-toolbar">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#sellerInvoiceModal">
                                                <i class="bi bi-receipt-cutoff"></i>Add Invoice
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="custom-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Invoice</th>
                                                    <th>E - Waybill</th>
                                                    <th>E - Waybill Expiry Date</th>
                                                    <th>Transport Receipt</th>
                                                    <th>Amount</th>
                                                    <th>Debit Note</th>
                                                    <th>Debit Note Amount</th>
                                                    <th>Credit Note</th>
                                                    <th>Credit Note Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($data->getDrivers->sum('amount') > 0)
                                                    @foreach ($data->getDrivers as $driver_data)
                                                        @if ($driver_data->seller_invoices)
                                                            @php
                                                                $seller_invoices = $driver_data->seller_invoices;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->index + 1 }}</td>
                                                                <td>
                                                                    <b>Vehicle Number:
                                                                    </b>{{ $driver_data->vehicle_number }} <br>
                                                                    <a href="{{ imageUrl($seller_invoices['invoice']) }}"
                                                                        target="_blank">Invoice File <i
                                                                            class="bi bi-download"></i></a>
                                                                </td>
                                                                <td>
                                                                    @if ($seller_invoices['ebill'])
                                                                        <a href="{{ imageUrl($seller_invoices['ebill']) }}"
                                                                            target="_blank">E-Bill <i
                                                                                class="bi bi-download"></i></a>
                                                                    @else
                                                                        <span class="text-muted">Not Available</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($seller_invoices['ebill_expiry_date'])
                                                                        {{ dateFormat($seller_invoices['ebill_expiry_date']) }}
                                                                        <br>
                                                                        @php
                                                                            $expiryDate = \Carbon\Carbon::parse(
                                                                                $seller_invoices['ebill_expiry_date'],
                                                                            );
                                                                            $daysLeft = $expiryDate->isToday()
                                                                                ? 0
                                                                                : ($expiryDate->isPast()
                                                                                    ? 0
                                                                                    : $expiryDate->diffInDays(now()) +
                                                                                        1);
                                                                        @endphp
                                                                        @if ($daysLeft <= 0)
                                                                            <b
                                                                                class="text-danger">{{ $daysLeft == 0 && $expiryDate->isToday() ? '0 days left' : 'Expired' }}</b>
                                                                        @elseif ($daysLeft <= 3)
                                                                            <b class="text-warning">{{ $daysLeft }}
                                                                                days left</b>
                                                                        @else
                                                                            <b class="text-success">{{ $daysLeft }}
                                                                                days left</b>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">Not Available</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($seller_invoices['transport_receipt'])
                                                                        <a href="{{ imageUrl($seller_invoices['transport_receipt']) }}"
                                                                            target="_blank">Transport Receipt <i
                                                                                class="bi bi-download"></i></a>
                                                                    @else
                                                                        <span class="text-muted">Not Available</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    ₹
                                                                    {{ formatIndianNumber($seller_invoices['amount']) }}
                                                                </td>
                                                                <td>
                                                                    @if (isset($seller_invoices['debit_note']))
                                                                        <a href="{{ imageUrl($seller_invoices['debit_note']) }}"
                                                                            target="_blank">Debit Note <i
                                                                                class="bi bi-download"></i></a>
                                                                    @else
                                                                        <span class="text-muted">Not Available</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    ₹
                                                                    {{ isset($seller_invoices['debit_note_amount']) ? formatIndianNumber($seller_invoices['debit_note_amount']) : '--' }}
                                                                </td>
                                                                <td>
                                                                    @if (isset($seller_invoices['credit_note']))
                                                                        <a href="{{ imageUrl($seller_invoices['credit_note']) }}"
                                                                            target="_blank">Credit Note <i
                                                                                class="bi bi-download"></i></a>
                                                                    @else
                                                                        <span class="text-muted">Not Available</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    ₹
                                                                    {{ isset($seller_invoices['credit_note_amount']) ? formatIndianNumber($seller_invoices['credit_note_amount']) : '--' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @elseif ($data->seller_invoices && count($data->seller_invoices) > 0)
                                                    @foreach ($data->seller_invoices as $invoice)
                                                        <tr>
                                                            <td>{{ $loop->index + 1 }}</td>
                                                            <td>
                                                                <a href="{{ imageUrl($invoice['invoice_file']) }}"
                                                                    target="_blank">
                                                                    {{ $invoice['name'] }} <i
                                                                        class="bi bi-download"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if ($invoice['ebill'])
                                                                    <a href="{{ imageUrl($invoice['ebill']) }}"
                                                                        target="_blank">E-Bill <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($invoice['ebill_expiry_date'])
                                                                    {{ dateFormat($invoice['ebill_expiry_date']) }}
                                                                    <br>
                                                                    @php
                                                                        $expiryDate = \Carbon\Carbon::parse(
                                                                            $invoice['ebill_expiry_date'],
                                                                        );
                                                                        $daysLeft = $expiryDate->isToday()
                                                                            ? 0
                                                                            : ($expiryDate->isPast()
                                                                                ? 0
                                                                                : $expiryDate->diffInDays(now()) + 1);
                                                                    @endphp
                                                                    @if ($daysLeft <= 0)
                                                                        <b
                                                                            class="text-danger">{{ $daysLeft == 0 && $expiryDate->isToday() ? '0 days left' : 'Expired' }}</b>
                                                                    @elseif ($daysLeft <= 3)
                                                                        <b class="text-warning">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @else
                                                                        <b class="text-success">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($invoice['transport_receipt'])
                                                                    <a href="{{ imageUrl($invoice['transport_receipt']) }}"
                                                                        target="_blank">Transport Receipt <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹ {{ formatIndianNumber($invoice['amount']) }}
                                                            </td>
                                                            <td>
                                                                @if (isset($invoice['debit_note']))
                                                                    <a href="{{ imageUrl($invoice['debit_note']) }}"
                                                                        target="_blank">Debit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ isset($invoice['debit_note_amount']) ? formatIndianNumber($invoice['debit_note_amount']) : '--' }}
                                                            </td>
                                                            <td>
                                                                @if (isset($invoice['credit_note']))
                                                                    <a href="{{ imageUrl($invoice['credit_note']) }}"
                                                                        target="_blank">Credit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ isset($invoice['credit_note_amount']) ? formatIndianNumber($invoice['credit_note_amount']) : '--' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <x-table-no-data colspan="10" icon="bi-receipt"
                                                        title="No invoices yet"
                                                        text="No invoices have been added for this order." />
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 mb-2">
                            <div class="card">
                                <div
                                    class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h5 class="mb-0">Transporter Invoice List</h5>
                                    @if ($data->getDrivers->sum('amount') == 0)
                                        <div class="bz-toolbar">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#transporterInvoiceModal">
                                                <i class="bi bi-receipt-cutoff"></i>Add Invoice
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="custom-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Invoice</th>
                                                    <th>E - Waybill</th>
                                                    <th>E - Waybill Expiry Date</th>
                                                    <th>Transport Receipt</th>
                                                    <th>Amount</th>
                                                    <th>Debit Note</th>
                                                    <th>Debit Note Amount</th>
                                                    <th>Credit Note</th>
                                                    <th>Credit Note Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($data->transporter_invoices && count($data->transporter_invoices) > 0)
                                                    @foreach ($data->transporter_invoices as $invoice)
                                                        <tr>
                                                            <td>{{ $loop->index + 1 }}</td>
                                                            <td>
                                                                <a href="{{ imageUrl($invoice['invoice_file']) }}"
                                                                    target="_blank">
                                                                    {{ $invoice['name'] }} <i
                                                                        class="bi bi-download"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if ($invoice['ebill'])
                                                                    <a href="{{ imageUrl($invoice['ebill']) }}"
                                                                        target="_blank">E-Bill <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($invoice['ebill_expiry_date'])
                                                                    {{ dateFormat($invoice['ebill_expiry_date']) }}
                                                                    <br>
                                                                    @php
                                                                        $expiryDate = \Carbon\Carbon::parse(
                                                                            $invoice['ebill_expiry_date'],
                                                                        );
                                                                        $daysLeft = $expiryDate->isToday()
                                                                            ? 0
                                                                            : ($expiryDate->isPast()
                                                                                ? 0
                                                                                : $expiryDate->diffInDays(now()) + 1);
                                                                    @endphp
                                                                    @if ($daysLeft <= 0)
                                                                        <b
                                                                            class="text-danger">{{ $daysLeft == 0 && $expiryDate->isToday() ? '0 days left' : 'Expired' }}</b>
                                                                    @elseif ($daysLeft <= 3)
                                                                        <b class="text-warning">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @else
                                                                        <b class="text-success">{{ $daysLeft }}
                                                                            days left</b>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($invoice['transport_receipt'])
                                                                    <a href="{{ imageUrl($invoice['transport_receipt']) }}"
                                                                        target="_blank">Transport Receipt <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹ {{ formatIndianNumber($invoice['amount']) }}
                                                            </td>
                                                            <td>
                                                                @if (isset($invoice['debit_note']))
                                                                    <a href="{{ imageUrl($invoice['debit_note']) }}"
                                                                        target="_blank">Debit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ isset($invoice['debit_note_amount']) ? formatIndianNumber($invoice['debit_note_amount']) : '--' }}
                                                            </td>
                                                            <td>
                                                                @if (isset($invoice['credit_note']))
                                                                    <a href="{{ imageUrl($invoice['credit_note']) }}"
                                                                        target="_blank">Credit Note <i
                                                                            class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted">Not Available</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                ₹
                                                                {{ isset($invoice['credit_note_amount']) ? formatIndianNumber($invoice['credit_note_amount']) : '--' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <x-table-no-data colspan="10" icon="bi-receipt"
                                                        title="No invoices yet"
                                                        text="No invoices have been added for this order." />
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5>Vehicle List</h5>
                            <div class="bz-toolbar">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#notesModal">
                                    <i class="bi bi-journal-text"></i>Notes
                                </button>
                                <a href="{{ route('admin.commodity-product-order-driver.create', $data->id) }}"
                                    class="btn btn-danger btn-sm" wire:navigate><i class="bi bi-plus-lg"></i>Add</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row profile-body">
                                @foreach ($data->getDrivers as $driver)
                                    <div class="d-none d-md-block col-md-3 left-wrapper mb-3">
                                        <div class="card rounded">
                                            <div class="card-body p-3">
                                                <div class="text-center mb-1">
                                                    <img class="bz-avatar-img bz-avatar-img--lg"
                                                        src="{{ imageUrl($driver->photo) }}"
                                                        onerror="this.onerror=null; this.src='{{ asset('admin_css/assets/images/avatar.png') }}'"
                                                        alt="profile">
                                                </div>
                                                <div class="text-center mb-2">
                                                    <h6 class="card-title mb-0">{{ $driver->vehicle_number }}</h6>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="bz-section-label">Transporter
                                                        Name:</label>
                                                    <p class="text-muted">{{ $driver->transporter_name ?? '--' }}</p>
                                                </div>

                                                <div class="mt-1">
                                                    <label class="bz-section-label">Transporter
                                                        Phone Number:</label>
                                                    <p class="text-muted">
                                                        {{ $driver->transporter_phone_number ?? '--' }}</p>
                                                </div>

                                                <div class="mt-1">
                                                    <label class="bz-section-label">Total
                                                        Quantity:</label>
                                                    <p class="text-muted">
                                                        {{ $driver->final_quantity_by_seller ? array_sum($driver->final_quantity_by_seller) : 0 }}
                                                    </p>
                                                </div>

                                                <div class="mt-1">
                                                    <label class="bz-section-label">Amount:</label>
                                                    <p class="text-muted">
                                                        {{ formatIndianNumber($driver->amount) ?? 0 }}</p>
                                                </div>

                                                <div class="mt-1">
                                                    <label class="bz-section-label">Driver
                                                        Name:</label>
                                                    <p class="text-muted">{{ $driver->name ?? '--' }}</p>
                                                </div>

                                                <div class="mt-1">
                                                    <label class="bz-section-label">Driver Phone
                                                        Number:</label>
                                                    <p class="text-muted">{{ $driver->phone }}</p>
                                                </div>

                                                <div class="mt-1">
                                                    <label class="bz-section-label">Tracking
                                                        Number:</label>
                                                    <p class="text-muted">{{ $driver->tracking_number ?? '--' }}</p>
                                                </div>


                                                <div class="mt-1">
                                                    <label class="bz-section-label">Advance
                                                        Amount:</label>
                                                    <p class="text-muted">₹
                                                        {{ formatIndianNumber($driver->advance_amount) ?? '--' }}</p>
                                                </div>
                                            </div>
                                            <div class="card-footer p-1">
                                                <a href="{{ route('admin.commodity-product-order-driver.show', [$data->id, $driver->id]) }}"
                                                    class="btn btn-icon btn-sm m-1 btn-secondary" title="View"
                                                    wire:navigate>
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.commodity-product-order-driver.edit', [$data->id, $driver->id]) }}"
                                                    class="btn btn-icon btn-sm m-1 btn-secondary" title="Edit"
                                                    wire:navigate>
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:;"
                                                    class="btn btn-icon btn-sm m-1 btn-danger" title="Remove"
                                                    wire:click="driverDelete({{ $driver->id }})">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
                                                @if ($driver->generate_invoice)
                                                    <button type="button"
                                                        class="btn btn-icon btn-sm m-1 btn-secondary"
                                                        title="Print Invoice"
                                                        wire:click="generateInvoice({{ $driver->id }})">
                                                        <i class="bi bi-printer"></i>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-icon btn-sm m-1 btn-secondary"
                                                        title="Generate Invoice" type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#invoiceGenrateModal_{{ $driver->id }}">
                                                        <i class="bi bi-gear-wide-connected"></i>
                                                    </button>
                                                @endif
                                                <button type="button"
                                                    class="btn btn-icon btn-sm m-1 btn-secondary" title="eBill"
                                                    type="button" data-bs-toggle="modal"
                                                    data-bs-target="#eBillModal_{{ $driver->id }}">
                                                    <i class="bi bi-receipt"></i>
                                                </button>

                                                <a href="{{ route('admin.commodity-product-order-driver.quantity', [$data->id, $driver->id]) }}"
                                                    class="btn btn-secondary btn-sm m-1" title="View"
                                                    wire:navigate>
                                                    <i class="bi bi-clipboard2-data"></i> Update
                                                    Quantity / Invoice
                                                </a>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="invoiceGenrateModal_{{ $driver->id }}"
                                        tabindex="-1"
                                        aria-labelledby="invoiceGenrateModalLabel_{{ $driver->id }}"
                                        aria-hidden="true" data-bs-backdrop="static" wire:ignore.self>
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form wire:submit.prevent="generateInvoice({{ $driver->id }})">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="invoiceGenrateModalLabel_{{ $driver->id }}">
                                                            Generate Invoice</h5>
                                                        {{-- <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                                                    </div>
                                                    <div class="modal-body">
                                                        <div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="radio" class="form-check-input"
                                                                    name="generate_invoice_{{ $driver->id }}" id="generate_invoice_seller_{{ $driver->id }}"
                                                                    value="seller" wire:model="generate_invoice">
                                                                <label class="form-check-label" for="generate_invoice_seller_{{ $driver->id }}">
                                                                    By Seller
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="radio" class="form-check-input"
                                                                    name="generate_invoice_{{ $driver->id }}" id="generate_invoice_biznie_{{ $driver->id }}"
                                                                    value="biznie" wire:model="generate_invoice">
                                                                <label class="form-check-label" for="generate_invoice_biznie_{{ $driver->id }}">
                                                                    By Biznie
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @error('generate_invoice')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="reset" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            data-bs-dismiss="modal">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="eBillModal_{{ $driver->id }}" tabindex="-1"
                                        aria-labelledby="eBillModalLabel_{{ $driver->id }}" aria-hidden="true"
                                        data-bs-backdrop="static" wire:ignore.self>
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form wire:submit.prevent="updateeBill({{ $driver->id }})">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="eBillModalLabel_{{ $driver->id }}">eBill Upload
                                                        </h5>
                                                        {{-- <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label"
                                                            for="eBill_file_{{ $driver->id }}">eBill File</label>
                                                        <input type="file" id="eBill_file_{{ $driver->id }}"
                                                            class="form-control @error('eBill_file') is-invalid @enderror"
                                                            wire:model="eBill_file">
                                                        <label for="eBill_file_{{ $driver->id }}">
                                                            @if ($eBill_file)
                                                                @php
                                                                    $extension = strtolower(
                                                                        $eBill_file->getClientOriginalExtension(),
                                                                    );
                                                                @endphp
                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <img src="{{ $eBill_file->temporaryUrl() }}"
                                                                        class="label-banner">
                                                                @else
                                                                    <span class="text-warning">Preview not available
                                                                        for {{ $extension }} files.</span>
                                                                @endif
                                                            @elseif ($driver->ebill)
                                                                @php
                                                                    $extension = strtolower(
                                                                        pathinfo(
                                                                            imageUrl($driver->ebill),
                                                                            PATHINFO_EXTENSION,
                                                                        ),
                                                                    );
                                                                @endphp
                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <img src="{{ imageUrl($driver->ebill) }}"
                                                                        class="label-banner">
                                                                @else
                                                                    <a href="{{ imageUrl($driver->ebill) }}"
                                                                        target="_blank">View Attachment</a>
                                                                @endif
                                                            @else
                                                                <img class="label-thumbnail"
                                                                    src="{{ asset('admin_css/assets/images/others/placeholder.jpg') }}">
                                                            @endif
                                                        </label>
                                                        @error('eBill_file')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="reset" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm">Save</button>
                                                    </div>
                                                </form>
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

    <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel"
        aria-hidden="true" data-bs-backdrop="static" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="uploadFile()">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileUploadModalLabel">Upload </h5>
                        {{-- <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="uploaded_file">File</label>
                            <input type="file" id="uploaded_file"
                                class="form-control @error('uploaded_file') is-invalid @enderror"
                                wire:model="uploaded_file">
                            @error('uploaded_file')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="notesModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="uploadVehicleNotes()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="notesModalLabel">Vehicle Notes</h1>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="vehicle_notes">Vehicle Notes</label>
                            <textarea class="form-control" id="vehicle_notes" cols="30" rows="10" placeholder="Enter Vehicle Notes..."
                                wire:model="vehicle_notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="invoiceModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="uploadInvoice()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="invoiceModalLabel">Invoice / Amount</h1>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="invoice_name">Invoice Name</label>
                                <input type="text" id="invoice_name"
                                    class="form-control @error('invoice_name') is-invalid @enderror"
                                    wire:model="invoice_name" placeholder="Enter Invoice Name">
                                @error('invoice_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="invoice_file">Invoice</label>
                                <input type="file" id="invoice_file"
                                    class="form-control @error('invoice_file') is-invalid @enderror"
                                    wire:model="invoice_file" placeholder="Enter Invoice Name">
                                @error('invoice_file')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="invoice_amount">Invoice Amount</label>
                                <input type="number" id="invoice_amount"
                                    class="form-control @error('invoice_amount') is-invalid @enderror"
                                    wire:model="invoice_amount" placeholder="Enter Invoice Amount">
                                @error('invoice_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="ebill">E - Waybill</label>
                                <input type='file' id="ebill"
                                    class="form-control @error('ebill') is-invalid @enderror" wire:model="ebill">
                                @error('ebill')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="ebill_expiry_date">E - Waybill Expiry Date</label>
                                <input type='date' id="ebill_expiry_date"
                                    class="form-control @error('ebill_expiry_date') is-invalid @enderror"
                                    wire:model="ebill_expiry_date">
                                @error('ebill_expiry_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transport_receipt">Transport Receipt</label>
                                <input type='file' id="transport_receipt"
                                    class="form-control @error('transport_receipt') is-invalid @enderror"
                                    wire:model="transport_receipt">
                                @error('transport_receipt')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="debit_note">Debit Note</label>
                                <input type='file' id="debit_note"
                                    class="form-control @error('debit_note') is-invalid @enderror"
                                    wire:model="debit_note">
                                @error('debit_note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="debit_note_amount">Debit Note Amount</label>
                                <input type="number" id="debit_note_amount"
                                    class="form-control @error('debit_note_amount') is-invalid @enderror"
                                    wire:model="debit_note_amount" placeholder="Enter Debit Note Amount">
                                @error('debit_note_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="credit_note">Credit Note</label>
                                <input type='file' id="credit_note"
                                    class="form-control @error('credit_note') is-invalid @enderror"
                                    wire:model="credit_note">
                                @error('credit_note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="credit_note_amount">Credit Note Amount</label>
                                <input type="number" id="credit_note_amount"
                                    class="form-control @error('credit_note_amount') is-invalid @enderror"
                                    wire:model="credit_note_amount" placeholder="Enter Credit Note Amount">
                                @error('credit_note_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sellerInvoiceModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="sellerInvoiceModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="sellerUploadInvoice()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="sellerInvoiceModalLabel">Seller Invoice / Amount</h1>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller_invoice_name">Invoice Name</label>
                                <input type="text" id="seller_invoice_name"
                                    class="form-control @error('seller_invoice_name') is-invalid @enderror"
                                    wire:model="seller_invoice_name" placeholder="Enter Invoice Name">
                                @error('seller_invoice_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller_invoice_file">Invoice</label>
                                <input type="file" id="seller_invoice_file"
                                    class="form-control @error('seller_invoice_file') is-invalid @enderror"
                                    wire:model="seller_invoice_file" placeholder="Enter Invoice Name">
                                @error('seller_invoice_file')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller_invoice_amount">Invoice Amount</label>
                                <input type="number" id="seller_invoice_amount"
                                    class="form-control @error('seller_invoice_amount') is-invalid @enderror"
                                    wire:model="seller_invoice_amount" placeholder="Enter Invoice Amount">
                                @error('seller_invoice_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller_ebill">E - Waybill</label>
                                <input type='file' id="seller_ebill"
                                    class="form-control @error('seller_ebill') is-invalid @enderror"
                                    wire:model="seller_ebill">
                                @error('seller_ebill')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller_ebill_expiry_date">E - Waybill Expiry
                                    Date</label>
                                <input type='date' id="seller_ebill_expiry_date"
                                    class="form-control @error('seller_ebill_expiry_date') is-invalid @enderror"
                                    wire:model="seller_ebill_expiry_date">
                                @error('seller_ebill_expiry_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="seller_transport_receipt">Transport Receipt</label>
                                <input type='file' id="seller_transport_receipt"
                                    class="form-control @error('seller_transport_receipt') is-invalid @enderror"
                                    wire:model="seller_transport_receipt">
                                @error('seller_transport_receipt')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="seller_debit_note">Debit Note</label>
                                <input type='file' id="seller_debit_note"
                                    class="form-control @error('seller_debit_note') is-invalid @enderror"
                                    wire:model="seller_debit_note">
                                @error('seller_debit_note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="seller_debit_note_amount">Debit Note Amount</label>
                                <input type="number" id="seller_debit_note_amount"
                                    class="form-control @error('seller_debit_note_amount') is-invalid @enderror"
                                    wire:model="seller_debit_note_amount" placeholder="Enter Debit Note Amount">
                                @error('debit_note_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="seller_credit_note">Credit Note</label>
                                <input type='file' id="seller_credit_note"
                                    class="form-control @error('seller_credit_note') is-invalid @enderror"
                                    wire:model="seller_credit_note">
                                @error('seller_credit_note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="seller_credit_note_amount">Credit Note Amount</label>
                                <input type="number" id="seller_credit_note_amount"
                                    class="form-control @error('seller_credit_note_amount') is-invalid @enderror"
                                    wire:model="seller_credit_note_amount" placeholder="Enter Credit Note Amount">
                                @error('seller_credit_note_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transporterInvoiceModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="transporterInvoiceModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="transporterUploadInvoice()">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="transporterInvoiceModalLabel">Transporter Invoice / Amount
                        </h1>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transporter_invoice_name">Invoice Name</label>
                                <input type="text" id="transporter_invoice_name"
                                    class="form-control @error('transporter_invoice_name') is-invalid @enderror"
                                    wire:model="transporter_invoice_name" placeholder="Enter Invoice Name">
                                @error('transporter_invoice_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transporter_invoice_file">Invoice</label>
                                <input type="file" id="transporter_invoice_file"
                                    class="form-control @error('transporter_invoice_file') is-invalid @enderror"
                                    wire:model="transporter_invoice_file" placeholder="Enter Invoice Name">
                                @error('transporter_invoice_file')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transporter_invoice_amount">Invoice Amount</label>
                                <input type="number" id="transporter_invoice_amount"
                                    class="form-control @error('transporter_invoice_amount') is-invalid @enderror"
                                    wire:model="transporter_invoice_amount" placeholder="Enter Invoice Amount">
                                @error('transporter_invoice_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transporter_ebill">E - Waybill</label>
                                <input type='file' id="transporter_ebill"
                                    class="form-control @error('transporter_ebill') is-invalid @enderror"
                                    wire:model="transporter_ebill">
                                @error('transporter_ebill')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transporter_ebill_expiry_date">E - Waybill Expiry
                                    Date</label>
                                <input type='date' id="transporter_ebill_expiry_date"
                                    class="form-control @error('transporter_ebill_expiry_date') is-invalid @enderror"
                                    wire:model="transporter_ebill_expiry_date">
                                @error('transporter_ebill_expiry_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="transporter_transport_receipt">Transport
                                    Receipt</label>
                                <input type='file' id="transporter_transport_receipt"
                                    class="form-control @error('transporter_transport_receipt') is-invalid @enderror"
                                    wire:model="transporter_transport_receipt">
                                @error('transporter_transport_receipt')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="transporter_debit_note">Debit Note</label>
                                <input type='file' id="transporter_debit_note"
                                    class="form-control @error('transporter_debit_note') is-invalid @enderror"
                                    wire:model="transporter_debit_note">
                                @error('transporter_debit_note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="transporter_debit_note_amount">Debit Note
                                    Amount</label>
                                <input type="number" id="transporter_debit_note_amount"
                                    class="form-control @error('transporter_debit_note_amount') is-invalid @enderror"
                                    wire:model="transporter_debit_note_amount" placeholder="Enter Debit Note Amount">
                                @error('transporter_debit_note_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="transporter_credit_note">Credit Note</label>
                                <input type='file' id="transporter_credit_note"
                                    class="form-control @error('transporter_credit_note') is-invalid @enderror"
                                    wire:model="transporter_credit_note">
                                @error('transporter_credit_note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="transporter_credit_note_amount">Credit Note
                                    Amount</label>
                                <input type="number" id="transporter_credit_note_amount"
                                    class="form-control @error('transporter_credit_note_amount') is-invalid @enderror"
                                    wire:model="transporter_credit_note_amount"
                                    placeholder="Enter Credit Note Amount">
                                @error('transporter_credit_note_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
