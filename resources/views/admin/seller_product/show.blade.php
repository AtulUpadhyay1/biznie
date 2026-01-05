<div>
    <style>
        .table-sm>:not(caption)>*>* {
            padding: 0.25rem .55rem;
        }
    </style>
    @section('title', config('app.name') . ' | ' . $page_title)
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
                            <a href="{{ route('admin.seller-product.index', $user_id) }}"
                                class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i
                                    class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <!-- Section 1: Basic Info & Pricing -->
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100" style="border-color: #dee2e6 !important;">
                                <h5 class="mb-3 pb-2 border-bottom">Basic Info & Pricing</h5>
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td><b>Name:</b></td>
                                            <td>
                                                @if ($data->name)
                                                    {{ $data->name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Brand:</b></td>
                                            <td>
                                                @if ($data->getBrand)
                                                    {{ $data->getBrand?->name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Base Price:</b></td>
                                            <td>₹ @if ($data->base_price)
                                                    {{ formatIndianNumber($data->base_price) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Price Validity:</b></td>
                                            <td>
                                                @if ($data->price_validity)
                                                    {{ $data->price_validity }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @foreach ($data->packaging_type as $packaging_key => $packaging_type_id)
                                            @php
                                                $packaging_type = \App\Models\PackagingType::find($packaging_type_id);
                                            @endphp
                                            @if ($packaging_type)
                                                <tr>
                                                    <td><b>{{ $packaging_type->name }}:</b></td>
                                                    <td>₹
                                                        {{ isset($data->packaging_type_price[$packaging_type_id]) ? formatIndianNumber($data->packaging_type_price[$packaging_type_id]) : 0 }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Section 2: Charges & Quality -->
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100" style="border-color: #dee2e6 !important;">
                                <h5 class="mb-3 pb-2 border-bottom">Charges & Quality</h5>
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td><b>Loading Charge:</b></td>
                                            <td>₹ @if ($data->loading_charge)
                                                    {{ formatIndianNumber($data->loading_charge) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Insurance Charge:</b></td>
                                            <td>₹ @if ($data->insurance_charge)
                                                    {{ formatIndianNumber($data->insurance_charge) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Quality Charge:</b></td>
                                            <td>
                                                @if ($data->quality_charge)
                                                    ₹ {{ formatIndianNumber($data->quality_charge) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @foreach ($data->charge_name as $charge_key => $charge_name)
                                            @if ($data->charge_price[$charge_key] > 0)
                                                <tr>
                                                    <td><b>{{ $charge_name }}:</b></td>
                                                    <td>
                                                        @if (
                                                            $data->operator[$charge_key] == '+' ||
                                                                $data->operator[$charge_key] == '-' ||
                                                                $data->operator[$charge_key] == '*' ||
                                                                $data->operator[$charge_key] == '/')
                                                            {{ $data->operator[$charge_key] }}
                                                        @endif
                                                        {{ $data->charge_price[$charge_key] }}
                                                        @if ($data->operator[$charge_key] == '%')
                                                            {{ $data->operator[$charge_key] }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @foreach ($data->quality as $quality_key => $quality)
                                            <tr>
                                                <td><b>{{ $quality }}:</b></td>
                                                <td>₹ {{ formatIndianNumber($data->quality_price[$quality_key]) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Section 3: Tax & Commission -->
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100" style="border-color: #dee2e6 !important;">
                                <h5 class="mb-3 pb-2 border-bottom">Tax & Commission</h5>
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td><b>GST:</b></td>
                                            <td>
                                                @if ($data->gst)
                                                    {{ $data->gst }} %
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>TCS:</b></td>
                                            <td>
                                                @if ($data->tcs)
                                                    {{ $data->tcs }} %
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Commission Type:</b></td>
                                            <td>
                                                @if ($data->commission_type)
                                                    {{ $data->commission_type }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Commission:</b></td>
                                            <td>
                                                @if ($data->commission_amount)
                                                    ₹ {{ $data->commission_amount }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3"><strong>Variation:</strong></h5>
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
                                            {{ $attribute['name'] }}
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
                                                <label class="form-check-label"
                                                    for="exampleCheck{{ $loop->iteration }}">
                                                    {{ $loop->iteration }}
                                                </label>
                                            </div>
                                            {{-- @if ($data->is_selected)
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
