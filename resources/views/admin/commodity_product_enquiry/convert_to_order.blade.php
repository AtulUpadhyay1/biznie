<div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Request For Quotation</h5>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Enquiry Id : {{ $enquiry_data->unique_id }} <br>
                        Category : {{ $enquiry_data->getCommodityProduct->getCategory->name }} <br>
                        Product : {{ $enquiry_data->getCommodityProduct->name }} <br>
                        Brand : {{ $enquiry_data->getBrand->name }} <br>
                        Delivery Location : {{ $enquiry_data->delivery_address['address_line_one'] }}
                        {{ $enquiry_data->delivery_address['address_line_two'] }}
                        {{ $enquiry_data->delivery_address['city'] }}
                        {{ $enquiry_data->delivery_address['pin_code'] }}
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
                <div class="card-body">
                    <p>
                        Name : {{ $enquiry_data->getUser->name }} <br>
                        Address : {{ $enquiry_data->billing_address['address_line_one'] }}
                        {{ $enquiry_data->billing_address['address_line_two'] }}
                        {{ $enquiry_data->billing_address['city'] }} <br>
                        Pincode : {{ $enquiry_data->billing_address['pin_code'] }} <br>
                        GSTIN/UIN : 78945613212546VN <br>
                        Phone : {{ $enquiry_data->getUser->phone }}
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
                <div class="card-body">
                    <p>
                        Phone : {{ $enquiry_data->consignee_detail['consignee_phone'] }} <br>
                        GST Number : {{ $enquiry_data->consignee_detail['gst_number'] }} <br>
                        Company : {{ $enquiry_data->consignee_detail['consignee_company'] }} <br>
                        Address : {{ $enquiry_data->consignee_detail['address']['address_line_one'] }}
                        {{ $enquiry_data->consignee_detail['address']['address_line_two'] }}
                        {{ $enquiry_data->consignee_detail['address']['city'] }} <br>
                        Pincode : {{ $enquiry_data->consignee_detail['address']['pin_code'] }}
                    </p>
                </div>
            </div>
        </div>
        @php
            $data = [
                'id' => $enquiry_data->id,
                'unique_id' => $enquiry_data->unique_id,
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
                'commission' => 0,
                'final_variation_price' => 0,
                'ex_price' => 0,
                'transport_price' => 0,
                'for_price' => 0,
                'required_booking_amount' => 0,
                'is_mark' => false,
                'status' => $enquiry_data->status,
            ];
            $markedSeller = $enquiry_data->getMarkedSellerProductEnquiry;
            if ($markedSeller) {
                // $data['variation']  = $markedSeller->value;
                $data['base_price'] = $markedSeller->base_price;
                $data['transport_price'] = $markedSeller->transport_price;
                $data['commission'] = $markedSeller->commission;
                $data['is_mark'] = $markedSeller->is_mark ? true : false;

                $seller_commodity_product = App\Models\SellerCommodityProduct::where('user_id', $markedSeller->user_id)
                    ->where('commodity_product_id', $markedSeller->commodity_product_id)
                    ->where('brand_id', $markedSeller->brand_id)
                    ->first();

                $packaging_arr = [];
                foreach ($seller_commodity_product->packaging_type as $packaging_charge) {
                    $packaging_arr['name'] = getPackagingType($packaging_charge)->name;
                    $packaging_arr['price'] = $seller_commodity_product->packaging_type_price[$packaging_charge];
                    $data['total_charges'] += $packaging_arr['price'];
                    $data['packaging_charge'][] = $packaging_arr;
                }

                $other_charges_arr = [];
                foreach ($seller_commodity_product->charge_name as $charge_key => $charge_name) {
                    $other_charges_arr['name'] = $charge_name;
                    $other_charges_arr['price'] = $seller_commodity_product->charge_price[$charge_key];
                    $other_charges_arr['operator'] = $seller_commodity_product->operator[$charge_key];

                    if ($other_charges_arr['operator'] == '+') {
                        $data['total_charges'] += $other_charges_arr['price'];
                    } elseif ($other_charges_arr['operator'] == '-') {
                        $data['total_charges'] -= $other_charges_arr['price'];
                    } elseif ($other_charges_arr['operator'] == '*') {
                        $data['total_charges'] += $data['ex_price'] * $other_charges_arr['price'];
                    } elseif ($other_charges_arr['operator'] == '/') {
                        $data['total_charges'] += $data['ex_price'] / $other_charges_arr['price'];
                    } elseif ($other_charges_arr['operator'] == '%') {
                        $data['total_charges'] += $data['ex_price'] * ($other_charges_arr['price'] / 100);
                    }
                    $data['other_charge'][] = $other_charges_arr;
                }

                if ($seller_commodity_product->is_quality) {
                    $other_quantity_charge_arr = [];
                    foreach ($seller_commodity_product->quality as $quality_key => $quality) {
                        $other_quantity_charge_arr['name'] = $quality;
                        $other_quantity_charge_arr['quality_price'] =
                            $seller_commodity_product->quality_price[$quality_key];
                        $data['total_charges'] += $other_quantity_charge_arr['quality_price'];
                        $data['other_quantity_charge'][] = $other_quantity_charge_arr;
                    }
                }

                foreach ($markedSeller->value as $variation) {
                    $variation['tax'] =
                        (($variation['price'] + $data['base_price']) * $seller_commodity_product->gst) / 100;
                    $variation['per_unit_price'] =
                        $variation['price'] + $data['base_price'] + $variation['tax'] + $data['total_charges'];
                    $variation['final_price'] = $variation['per_unit_price'] * $variation['quantity'];
                    $data['variation'][] = $variation;
                    $data['total_quantity'] += $variation['quantity'];
                    $data['final_variation_price'] += $variation['final_price'];
                    $data['gst_amount'] += $variation['tax'];
                }

                $data['loading_charge'] = $seller_commodity_product->loading_charge;
                $data['insurance_charge'] = $seller_commodity_product->insurance_charge;
                $data['quality_charge'] = $seller_commodity_product->quality_charge ?? 0;
                $data['gst'] = $seller_commodity_product->gst;
                $data['tcs'] = $seller_commodity_product->tcs;

                $data['tcs_amount'] = ($data['final_variation_price'] * $data['tcs_amount']) / 100;

                $data['ex_price'] = $data['final_variation_price'] + $data['tcs_amount'];

                $data['for_price'] = $data['ex_price'] + $data['transport_price'];
                $data['required_booking_amount'] = ($data['for_price'] * 30) / 100;
                // $data['status']     = $enquiry_data->getMarkedSellerProductEnquiry->status;
            } else {
                $data['variation'] = $enquiry_data->variation;
            }
        @endphp

        @if ($markedSeller)

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h5>Your Requirement</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            Basic Price : ₹ {{ $data['base_price'] + $data['commission'] }} / Metric Ton <br>
                            Freight : ₹ {{ $data['transport_price'] }} / Metric Ton
                        </p> <br>
                        @foreach ($data['variation'] as $variation)
                            <p>
                                @foreach ($variation['value'] as $value)
                                    Size : {{ $value['value'] }} {{ $value['unit']['short_name'] }} <br>
                                @endforeach
                                Quantity : {{ $variation['quantity'] }} MT <br>
                                Ex-Factory Price : {{ $variation['per_unit_price'] }} Metric Ton <br>
                                For Price : {{ formatIndianNumber($variation['per_unit_price'] + $data['transport_price']) }} Metric Ton
                                <br>
                                <span class="text-danger">Ex-Price x Qty : {{ formatIndianNumber($variation['final_price']) }}</span>
                            </p> <br>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h5>Price Calculation</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><span class="text-danger">Total Quantity : </span> {{ $data['total_quantity'] }} Metric Ton</p>
                        <p><span class="text-danger">Total Ex-Factory Price : </span> {{ formatIndianNumber($data['final_variation_price']) }}</p>
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
                            <button class="btn btn-success floa-end mt-2" wire:click="enquiryToOrder()"> Confirm & Pay </button>
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
    @endpush
</div>
