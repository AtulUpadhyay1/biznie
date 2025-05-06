<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Request For Quotation</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <p>
                        Enquiry Id : {{ $enquiry_data->unique_id }} <br>
                        Category : {{ $enquiry_data->getCommodityProduct->getCategory->name }} <br>
                        Product : {{ $enquiry_data->getCommodityProduct->name }} <br>
                        Brand : {{ $enquiry_data->getBrand->name }} <br>
                        Delivery Location : {{ $enquiry_data->consignee_detail['address_line_one'] }}
                        {{ $enquiry_data->consignee_detail['address_line_two'] }}
                        {{ $enquiry_data->consignee_detail['city'] }}
                        {{ isset($enquiry_data->consignee_detail['pin_code']) ? $enquiry_data->consignee_detail['pin_code'] : $enquiry_data->consignee_detail['pincode'] }} <br>
                        Purpose : {{ $enquiry_data->purpose }} <br>
                        Description : {{ $enquiry_data->description }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Buyer (Bill to)</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <p>
                        Company Name : {{ $enquiry_data->getUser->name }} ({{$enquiry_data->getUser->getUserDetail->company_name}})<br>
                        Phone : {{ $enquiry_data->getUser->phone }} <br>
                        GST : {{ $enquiry_data->getUser->getUserDetail->gst_number }} <br>
                        Pincode : {{ $enquiry_data->billing_address['pincode'] }} <br>
                        Address Line1 : {{ $enquiry_data->billing_address['address_line_one'] }} <br>
                        Address Line2 : {{ $enquiry_data->billing_address['address_line_two'] }} <br>
                        City : {{ $enquiry_data->billing_address['city'] }} <br>
                        State : {{ $enquiry_data->billing_address['state'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Consignee (Ship to)</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <p>
                        Company Name : {{ $enquiry_data->getUser->name }} ({{$enquiry_data->getUser->getUserDetail->company_name}})<br>
                        Phone : {{ $enquiry_data->getUser->phone }} <br>
                        GST : {{ $enquiry_data->getUser->getUserDetail->gst_number }} <br>
                        Pincode : {{ $enquiry_data->billing_address['pincode'] }} <br>
                        Address Line1 : {{ $enquiry_data->billing_address['address_line_one'] }} <br>
                        Address Line2 : {{ $enquiry_data->billing_address['address_line_two'] }} <br>
                        City : {{ $enquiry_data->billing_address['city'] }} <br>
                        State : {{ $enquiry_data->billing_address['state'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
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
                    <p>
                        Name : {{ $seller->name }} ({{$seller->getBusiness->name}})<br>
                        Phone : {{ $seller->phone }} <br>
                        GST : {{ $sellerDetail->gst_number }} <br>
                        Pincode : {{ $sellerDetail->postal_code }} <br>
                        Address Line One : {{ $sellerDetail->address_line_one }} <br>
                        Address Line Two : {{ $sellerDetail->address_line_two }} <br>
                        City : {{ $sellerDetail->city }} <br>
                        State : {{ $sellerDetail->state }}
                    </p>
                </div>
            </div>
        </div>

        @php
            $data = [
                'id'                => $enquiry_data->id,
                'unique_id'         => $enquiry_data->unique_id,
                'order_id'          => $enquiry_data->getCommodityProductOrder ? $enquiry_data->getCommodityProductOrder->id : NULL,
                'brand'             => $enquiry_data->getBrand ? [
                    'id'            => $enquiry_data->getBrand->id,
                    'name'          => $enquiry_data->getBrand->name
                ] : [],
                'unit'              => $enquiry_data->getCommodityProduct->getUnit ? [
                    'id'            => $enquiry_data->getCommodityProduct->getUnit->id,
                    'name'          => $enquiry_data->getCommodityProduct->getUnit->name
                ] : [],
                'commodity_product' => $enquiry_data->getCommodityProduct ? [
                    'id'        => $enquiry_data->getCommodityProduct->id,
                    'name'      => $enquiry_data->getCommodityProduct->name,
                    'thumbnail' => $enquiry_data->getCommodityProduct->thumbnail ? imageUrl($enquiry_data->getCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),

                    'category'  => $enquiry_data->getCommodityProduct->getCategory ? [
                        'id'    => $enquiry_data->getCommodityProduct->getCategory->id,
                        'name'  => $enquiry_data->getCommodityProduct->getCategory->name,

                    ] : [],

                ] : [],

                'origin_city'       => $enquiry_data->origin_city,
                'variation'         => [],
                'selected_quality'           => $enquiry_data->quality,
                'selected_packaging_charge'  => $enquiry_data->packaging_charge,
                'unit_price'        => $enquiry_data->unit_price,
                'billing_address'   => $enquiry_data->billing_address,
                'delivery_address'  => $enquiry_data->delivery_address,
                'consignee_detail'  => $enquiry_data->consignee_detail,
                'purpose'           => $enquiry_data->purpose,
                'description'       => $enquiry_data->description,
                'message'           => $enquiry_data->message,
                // 'price'             => $enquiry_data->price,
                'packaging_charge'  => [],
                'other_charge'      => [],
                'other_quantity_charge'    => [],
                'transporter_detail' => [],
                'total_quantity'    => 0,
                'base_price'        => 0,
                'loading_charge'    => 0,
                'insurance_charge'  => 0,
                'quality_charge'    => 0,
                'gst'               => 0,
                'tcs'               => 0,
                'gst_amount'        => 0,
                'tcs_amount'        => 0,
                'total_charges'     => 0,
                'commission_type'   => '',
                'commission'        => 0,
                'final_variation_price' => 0,
                'ex_price'          => 0,
                'transport_price'   => 0,
                'for_price'         => 0,
                'required_booking_amount' => 0,
                'is_mark'           => false,
                'status'            => $enquiry_data->status,
                'created_at'        => dateTimeFormat($enquiry_data->created_at),
                'credit_days'       => $enquiry_data->customer_credit_days ? $enquiry_data->customer_credit_days : $enquiry_data->getUser->credit_days,
            ];
            $markedSeller = $enquiry_data->getMarkedSellerProductEnquiry;
            if($markedSeller){

                // $data['variation']  = $markedSeller->value;
                $data['base_price'] = $markedSeller->base_price;
                $data['transport_price'] = $markedSeller->transport_price;
                $data['commission_type'] = $markedSeller->commission_type;
                $data['commission'] = (int)$markedSeller->commission;
                $data['is_mark']    = $markedSeller->is_mark ? true : false;
                if($data['commission_type'] == 'exclude'){
                    $data['base_price'] += $data['commission'];
                }

                $seller_commodity_product   = App\Models\SellerCommodityProduct::where('user_id', $markedSeller->user_id)->where('commodity_product_id', $markedSeller->commodity_product_id)->where('brand_id', $markedSeller->brand_id)->first();

                $packaging_arr = [];
                foreach ($seller_commodity_product->packaging_type ?? [] as $packaging_charge) {
                    $packaging_arr['name'] = getPackagingType($packaging_charge)->name;
                    $packaging_arr['price'] = isset($seller_commodity_product->packaging_type_price[$packaging_charge]) ? $seller_commodity_product->packaging_type_price[$packaging_charge] : 0;
                    $data['total_charges'] += $packaging_arr['price'];
                    $data['packaging_charge'][] = $packaging_arr;
                }

                $other_charges_arr = [];
                $extra_charges = 0;

                foreach ($seller_commodity_product->charge_name ?? [] as $charge_key => $charge_name) {
                    $other_charges_arr['name'] = $charge_name;
                    $other_charges_arr['price'] = isset($seller_commodity_product->charge_price[$charge_key]) ? $seller_commodity_product->charge_price[$charge_key] : 0;
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

                    $data['other_charge'][] = $other_charges_arr;
                }

                if($seller_commodity_product && $seller_commodity_product->is_quality){
                    $other_quantity_charge_arr = [];
                    foreach ($seller_commodity_product->quality ?? [] as $quality_key => $quality) {
                        $other_quantity_charge_arr['name']          = $quality;
                        $other_quantity_charge_arr['quality_price'] = $seller_commodity_product->quality_price[$quality_key];
                        $data['total_charges'] += $other_quantity_charge_arr['quality_price'];
                        $data['other_quantity_charge'][] = $other_quantity_charge_arr;
                    }
                }

                foreach($markedSeller->value as $variation){

                    $variation['total_price']       = ($variation['price'] + $data['base_price']) + $extra_charges + $seller_commodity_product->loading_charge + $seller_commodity_product->insurance_charge;
                    $variation['tax']               = round(($variation['total_price']) * $seller_commodity_product->gst / 100);
                    $variation['per_unit_price']    = $variation['total_price'] + $variation['tax'];
                    $variation['final_price']       = $variation['per_unit_price'] * $variation['quantity'];
                    $data['variation'][]            = $variation;
                    $data['total_quantity']         += $variation['quantity'];
                    $data['final_variation_price']  += $variation['final_price'];
                    $data['gst_amount']             += $variation['tax'];
                }

                $data['loading_charge']     = $seller_commodity_product->loading_charge;
                $data['insurance_charge']   = $seller_commodity_product->insurance_charge;
                $data['quality_charge']     = $seller_commodity_product->quality_charge ?? 0;
                $data['gst']                = $seller_commodity_product->gst;
                $data['tcs']                = $seller_commodity_product->tcs;

                $data['tcs_amount']         = $data['final_variation_price']*$data['tcs_amount']/100;

                $data['ex_price']           = $data['final_variation_price'] + $data['tcs_amount'];

                $data['for_price']          = $data['ex_price'] + $data['transport_price'] * $data['total_quantity'];
                $data['required_booking_amount'] = $data['for_price'] * 30 / 100;
                // if($data['commission_type'] == 'exclude'){
                //     $data['final_variation_price'] += $data['commission'];
                // }
                // $data['status']     = $enquiry_data->getMarkedSellerProductEnquiry->status;
            }else{
                $data['variation']   = $enquiry_data->variation;
            }
        @endphp

        @php
            $seller_enq_data = [
                'id'                => $seller_enquiry_data->id,
                'unique_id'         => $seller_enquiry_data->unique_id,
                'order_id'          => $seller_enquiry_data->getCommodityProductOrder ? $seller_enquiry_data->getCommodityProductOrder->id : NULL,
                'brand'             => $seller_enquiry_data->getBrand ? [
                        'id'        => $seller_enquiry_data->getBrand->id,
                        'name'      => $seller_enquiry_data->getBrand->name
                    ] : [],
                'unit'              => $seller_enquiry_data->getSellerCommodityProduct->getUnit ? [
                        'id'        => $seller_enquiry_data->getSellerCommodityProduct->getUnit->id,
                        'name'      => $seller_enquiry_data->getSellerCommodityProduct->getUnit->name
                    ] : [],
                'commodity_product' => $seller_enquiry_data->getSellerCommodityProduct ? [
                        'id'        => $seller_enquiry_data->getSellerCommodityProduct->id,
                        'name'      => $seller_enquiry_data->getSellerCommodityProduct->name,
                        'thumbnail' => $seller_enquiry_data->getSellerCommodityProduct->thumbnail ? imageUrl($seller_enquiry_data->getSellerCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),

                        'category'  => $seller_enquiry_data->getSellerCommodityProduct->getCategory ? [
                            'id'    => $seller_enquiry_data->getSellerCommodityProduct->getCategory->id,
                            'name'  => $seller_enquiry_data->getSellerCommodityProduct->getCategory->name,

                        ] : [],

                    ] : [],

                'origin_city'       => $seller_enquiry_data->origin_city,
                'variation'         => [],
                'billing_address'   => $seller_enquiry_data->billing_address,
                'delivery_address'  => $seller_enquiry_data->delivery_address,
                'consignee_detail'  => $seller_enquiry_data->consignee_detail,
                'purpose'           => $seller_enquiry_data->purpose,
                'description'       => $seller_enquiry_data->description,
                'message'           => $seller_enquiry_data->message,
                'delivery_by'       => $seller_enquiry_data->delivery_by,
                'selected_quality'           => $seller_enquiry_data->quality,
                'selected_packaging_charge'  => $seller_enquiry_data->packaging_charge,
                'loading_address'   => $seller_enquiry_data->loading_address,
                // 'price'             => $seller_enquiry_data->price,
                'packaging_charge'  => [],
                'other_charge'      => [],
                'other_quantity_charge'    => [],
                'total_quantity'    => 0,
                'base_price'        => $seller_enquiry_data->base_price,
                'loading_charge'    => 0,
                'insurance_charge'  => 0,
                'quality_charge'    => 0,
                'gst'               => 0,
                'tcs'               => 0,
                'gst_amount'        => 0,
                'tcs_amount'        => 0,
                'total_charges'     => 0,
                'commission_type'   => $seller_enquiry_data->commission_type,
                'commission'        => 0,
                'final_variation_price' => 0,
                'ex_price'          => 0,
                'transport_price'   => 0,
                'for_price'         => 0,
                'required_booking_amount' => 0,
                'is_mark'           => false,
                'status'            => $seller_enquiry_data->status,
                'created_at'        => dateTimeFormat($seller_enquiry_data->created_at),
                'credit_days'       => $seller_enquiry_data->seller_credit_days ? $seller_enquiry_data->seller_credit_days : auth()->user()->credit_days,
            ];

            $seller_commodity_product   = App\Models\SellerCommodityProduct::where('user_id', $seller_enquiry_data->user_id)->where('commodity_product_id', $seller_enquiry_data->commodity_product_id)->where('brand_id', $seller_enquiry_data->brand_id)->first();
            $seller_enq_data['loading_charge'] = $seller_commodity_product->loading_charge;
            $seller_enq_data['insurance_charge'] = $seller_commodity_product->insurance_charge;
            $seller_enq_data['quality_charge'] = $seller_commodity_product->quality_charge;
            $seller_enq_data['gst'] = $seller_commodity_product->gst;

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
            $seller_enq_data['other_charges'] = $other_charges;

            $seller_enq_data['quality_price'] = $seller_enquiry_data->quality && isset($seller_enquiry_data->quality['price']) ? $seller_enquiry_data->quality['price'] : "0";
            $seller_enq_data['packaging_charge_price'] = $seller_enquiry_data->packaging_charge && isset($seller_enquiry_data->packaging_charge['charge']) ? $seller_enquiry_data->packaging_charge['charge'] : "0";
            $all_charges = $seller_enq_data['loading_charge'] + $seller_enq_data['insurance_charge'] + $seller_enq_data['quality_charge'] + $seller_enq_data['quality_price'] + $seller_enq_data['packaging_charge_price'] + $extra_charges;

            $selected_variations = $seller_enquiry_data->value;
            $variation_arr = [];
            foreach ($selected_variations as $variation) {
                $get_state_price = App\Models\SellerCommodityProductStatePrice::find($variation['id']);
                if ($get_state_price) {
                    $variation['price'] = $get_state_price->price;
                    $variation['per_unit_price'] = $get_state_price->price + $seller_enquiry_data->base_price + $all_charges;
                    $variation['tax'] = round(($variation['per_unit_price']) * $seller_enq_data['gst'] / 100);
                    $variation['per_unit_price'] += $variation['tax'];
                    $variation['final_price'] = $variation['per_unit_price'] * $variation['quantity'];
                    $seller_enq_data['total_quantity']         += $variation['quantity'];
                    $seller_enq_data['ex_price'] += $variation['final_price'];
                }
                $variation_arr[] = $variation;
            }
            $seller_enq_data['variation'] = $variation_arr;
            $seller_enq_data['commission'] = $seller_enquiry_data->commission;
        @endphp

        @if ($markedSeller)

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h5>Product Pricing For Buyer</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            Basic Price : ₹ {{ $data['base_price'] }} / Metric Ton <br>
                            Freight : ₹ {{ $data['transport_price'] }} / Metric Ton
                        </p> <br>
                        @if($data['selected_quality'])
                            <p>
                                Quality:
                                {{ $data['selected_quality']['name'] }} : ₹ {{ $data['selected_quality']['price'] }} <br>
                            </p>
                        @endif
                        @if($data['selected_packaging_charge'])
                            <p>
                                Packaging:
                                {{ $data['selected_packaging_charge']['name'] }} : ₹ {{ $data['selected_packaging_charge']['charge'] }} <br>
                            </p>
                            <br>
                        @endif
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Requirements</th>
                                    <th>Ex Price</th>
                                    <th>Ex Price x Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['variation'] as $variation)
                                    <tr>
                                        <td>
                                            @foreach ($variation['value'] as $value)
                                                {{ $value['value'] }} {{ $value['unit']['short_name'] }}@if (!$loop->last),@endif
                                            @endforeach
                                            - {{ $variation['quantity'] }} MT
                                        </td>
                                        <td>₹ {{ formatIndianNumber($variation['per_unit_price']) }} / MT</td>
                                        <td>₹ {{ formatIndianNumber($variation['final_price']) }}</td>
                                    </tr>
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
                        <p>
                            Basic Price : ₹ {{ $seller_enq_data['base_price'] }} / Metric Ton <br>
                            Freight : ₹ {{ $data['transport_price'] }} / Metric Ton
                        </p> <br>
                        @if($data['selected_quality'])
                            <p>
                                Quality:
                                {{ $data['selected_quality']['name'] }} : ₹ {{ $data['selected_quality']['price'] }} <br>
                            </p>
                        @endif
                        @if($data['selected_packaging_charge'])
                            <p>
                                Packaging:
                                {{ $data['selected_packaging_charge']['name'] }} : ₹ {{ $data['selected_packaging_charge']['charge'] }} <br>
                            </p>
                            <br>
                        @endif
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
                                                {{ $value['value'] }} {{ $value['unit']['short_name'] }}@if (!$loop->last),@endif
                                            @endforeach
                                            - {{ $variation['quantity'] }} MT
                                        </td>
                                        <td>₹ {{ formatIndianNumber($variation['per_unit_price']) }} / MT</td>
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
                                For Price : {{ formatIndianNumber($variation['per_unit_price'] + $data['transport_price']) }} Metric Ton
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
                        <p><span class="text-danger">Credit Days : </span> {{ $data['credit_days'] }} Days</p>
                        <p><span class="text-danger">Basic Price : </span> ₹ {{ $data['base_price'] }} </p>
                        <p><span class="text-danger">Ex-Factory Price : </span> {{ formatIndianNumber($data['final_variation_price']) }}</p>
                        <p><span class="text-danger">Total Quantity : </span> {{ $data['total_quantity'] }} Metric Ton</p>
                        <p><span class="text-danger">Total Ex-Factory Price : </span> {{ formatIndianNumber($data['final_variation_price']) }}</p>
                        @if ($data['commission_type'] == 'exclude')
                            <p><span class="text-danger">Commission (Excluded) : </span> {{ formatIndianNumber($data['commission']) }}</p>
                        @endif
                        <small class="text-success">Rate included - loading charges, insurance charges, Packaging charges, Quality inspection charges, TCS & GST </small>
                        <hr>

                        <p><span class="text-danger">Freight : </span> {{ $data['transport_price'] }}/Metric Ton</p>
                        <p><span class="text-danger">Total Freight : </span> {{ $data['total_quantity'] * $data['transport_price'] }}</p>
                        <small class="text-success">
                            Freight May Change +/- 100. <br>
                            (Freight Depends On Demand & Supply of Trucks On Loading Day)
                        </small>
                        <hr>

                        <p><span class="text-danger">Total Amount Payable (Ex-Factory+Freight) : </span> {{ formatIndianNumber($data['final_variation_price'] + ($data['total_quantity'] * $data['transport_price'])) }}</p>
                        <small class="text-danger fw-bold">Disclaimer</small> <br>
                        <small class="text-success fw-bold">The invoice amount will be updated after the goods are loaded and the final quantity is confirmed.</small>
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
                        <p><span class="text-danger">Credit Days : </span> {{ $seller_enq_data['credit_days'] }} Days</p>
                        <p><span class="text-danger">Basic Price : </span> {{ formatIndianNumber($seller_enq_data['base_price']) }}</p>
                        <p><span class="text-danger">Total Quantity : </span> {{ $seller_enq_data['total_quantity'] }} MT</p>
                        <p><span class="text-danger">Total Ex-Factory Price : </span> {{ formatIndianNumber($seller_enq_data['ex_price']) }}</p>
                        @if ($seller_enq_data['commission_type'] == 'include')
                            @php
                                $commission = $seller_enq_data['commission'] * ($seller_enq_data['total_quantity'] + $seller_enq_data['gst'] / 100)
                            @endphp
                            <p><span class="text-danger">Commission (Included) : </span> {{ formatIndianNumber($commission) }} ({{ $seller_enq_data['commission'] }} / MT) ({{ $seller_enq_data['gst'] }}% GST)</p>
                        @endif
                        <small class="text-success">Rate included - loading charges, insurance charges, packaging charges, quality inspection charges & GST. TCS may apply (If applicable).
                            <br>
                            The credit period starts form the date the invoice is generated.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h5>Order Payment</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <small class="text-danger">You have to pay minimum order amount to confirm order</small>
                        <p class="fw-bold"><span class="text-danger">Order Amount : </span> <span class="text-success">₹ {{ formatIndianNumber($data['required_booking_amount']) }}</span></p>
                        <p class="fw-bold">Wallet Balance : ₹ {{ formatIndianNumber($enquiry_data->getUser->cash_balance + $enquiry_data->getUser->credit_balance) }}</p>
                        <small class="text-danger">Disclaimer</small> <br>
                        <ol class="text-primary small">
                            <li>This order can not be cancelled.</li>
                            <li>Advance amount will not be refunded/returned if confirmed order is cancelled.</li>
                            <li>You can not return the goods.</li>
                            <li>In Case goods are completely different Biznie's management will decide mode of reimbursement.</li>
                        </ol>
                        {{-- <input type="text" name="token_amount" value="{{ $data['required_booking_amount'] }}">
                        <input type="text" name="total_amount" value="{{ $data['final_variation_price'] + ($data['total_quantity'] * $data['transport_price']) }}"> --}}
                        <textarea rows="5" placeholder="Write Your Message" class="form-control" wire:model="message"></textarea>
                        <div class="text-end">
                            {{-- <button class="btn btn-success floa-end mt-2" wire:click="enquiryToOrder()"> Confirm & Pay </button> --}}
                            <button class="btn btn-info floa-end mt-2" wire:click="enquiryToOrder()" wire:loading.attr="disabled">
                                <span wire:loading.remove>Proceed Without OTP</span>
                                <span wire:loading wire:target="enquiryToOrder">Processing your request...</span>
                            </button>
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#otpVeryfiy" wire:click="sendOtp()" wire:loading.attr="disabled">
                                <span wire:loading.remove>Generate OTP</span>
                                <span wire:loading wire:target="sendOtp">Sending OTP...</span>
                            </button>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="otpVeryfiy" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="otpVeryfiyLabel" aria-hidden="true" wire:ignore.self>
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="otpVeryfiyLabel">OTP Verification</h5>
                                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                                    </div>
                                    <div class="modal-body">
                                        <label for="otp">Otp Send On {{ $enquiry_data->getMarkedSellerProductEnquiry->getUser->phone }}</label>
                                        <input type="number" class="form-control" id="otp" placeholder="Enter OTP" wire:model="otp">
                                        <div class="text-end">
                                            <small id="resend-otp" class="d-none" wire:click="sendOtp()">
                                                <a href="javascript:;" onclick="restartTimer()">Resend OTP</a>
                                            </small>
                                            <small id="timer"></small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-success btn-sm" wire:click="verifyOtp()" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Verify</span>
                                            <span wire:loading wire:target="verifyOtp">Verifying...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                @this.setAmount({{ $data['required_booking_amount'] }}, {{ $data['final_variation_price'] + ($data['total_quantity'] * $data['transport_price']) }});
            });
        </script>

        <script>
            const modalElement = document.getElementById('otpVeryfiy');
            const resendOtpElement = document.getElementById('resend-otp');
            const timerElement = document.getElementById('timer');
            let timer;

            function startTimer(seconds) {
                let remaining = seconds;
                resendOtpElement.classList.add('d-none');

                clearInterval(timer);
                timer = setInterval(() => {
                    if (remaining > 0) {
                        timerElement.textContent = `Resend OTP in ${remaining--} seconds`;
                    } else {
                        clearInterval(timer);
                        timerElement.textContent = '';
                        resendOtpElement.classList.remove('d-none');
                    }
                }, 1000);
            }

            function restartTimer() {
                startTimer(60);
            }

            modalElement.addEventListener('shown.bs.modal', function () {
                startTimer(60);
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                clearInterval(timer);
                timerElement.textContent = '';
                resendOtpElement.classList.add('d-none');
            });
        </script>
    @endpush
</div>
