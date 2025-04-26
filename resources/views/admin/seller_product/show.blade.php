<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.seller-product.index', $user_id)}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        <b>Name: </b> {{ $data->name }}<br>
                        <b>Brand: </b> {{ $data->getBrand?->name }}<br>
                        <b>Packaging Type & Charges: </b> <br>
                        @foreach ($data->packaging_type as $packaging_key => $packaging_type_id)
                            @php
                                $packaging_type = \App\Models\PackagingType::find($packaging_type_id);
                            @endphp
                            @if ($packaging_type)
                                {{ $packaging_type->name }} : ₹ {{ isset($data->packaging_type_price[$packaging_type_id]) ? formatIndianNumber($data->packaging_type_price[$packaging_type_id]) : 0 }}<br>
                            @endif
                        @endforeach
                        <b>Base Price: </b> ₹ {{ formatIndianNumber($data->base_price) }}<br>
                        <b>Loading Charge: </b> ₹ {{ formatIndianNumber($data->loading_charge) }}<br>
                        <b>Insurance Charge: </b> ₹ {{ formatIndianNumber($data->insurance_charge) }}<br>
                        <b>Quality Charge: </b> ₹ {{ formatIndianNumber($data->quality_charge) }}<br>
                        <b>Other Charges: </b> <br>
                        @foreach ($data->charge_name as $charge_key => $charge_name )
                            @if ($data->charge_price[$charge_key] > 0)
                                {{ $charge_name }}:
                                @if ($data->operator[$charge_key] == '+' || $data->operator[$charge_key] == '-' || $data->operator[$charge_key] == '*' || $data->operator[$charge_key] == '/')
                                    {{ $data->operator[$charge_key] }}
                                @endif
                                {{ $data->charge_price[$charge_key] }}
                                @if ($data->operator[$charge_key] == '%')
                                    {{ $data->operator[$charge_key] }}
                                @endif
                                <br>
                            @endif
                        @endforeach
                        <b>Quality: </b> <br>
                        @foreach ($data->quality as $quality_key => $quality )
                            {{ $quality }}: ₹ {{ formatIndianNumber($data->quality_price[$quality_key]) }}<br>
                        @endforeach
                        <b>GST: </b> {{ $data->gst }} %<br>
                        <b>TCS: </b> {{ $data->tcs }} %<br>
                        <b>Commission Type: </b> {{ $data->commission_type }}<br>
                        <b>Commission: </b> ₹ {{ $data->commission_amount }}<br>
                        <b>Price Validity: </b> {{ $data->price_validity }}<br>
                    </p>
                    <p><b>Variation:</b></p>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    @php
                                        $attributes = $variation_list[0]->value;
                                    @endphp
                                    @foreach ($attributes as $attribute)
                                        <th>
                                            {{$attribute['name']}}
                                        </th>
                                    @endforeach
                                    <th>Guage Difference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($variation_list as $variation_data)
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <label class="form-check-label" for="exampleCheck{{$loop->iteration}}">
                                                    {{ $loop->iteration }}
                                                </label>
                                            </div>
                                            {{-- @if($data->is_selected)
                                                <i class="bi bi-check2-circle text-success fs-5"></i>
                                            @endif --}}
                                        </th>
                                        @foreach ($variation_data->value as $price_value)
                                            <td> {{ $price_value['value'] }} </td>
                                        @endforeach
                                        <td>₹ {{ formatIndianNumber($variation_data->price) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
