<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{ route('admin.seller-product.index', $user_id) }}" class="btn btn-secondary btn-sm"
                            wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <!-- Section 1: Basic Info & Pricing -->
                        <div class="col-md-4">
                            <div class="bz-panel h-100">
                                <h5 class="bz-panel__title">Basic Info &amp; Pricing</h5>
                                <dl class="bz-kv-list">
                                    <div>
                                        <dt>Name</dt>
                                        <dd>
                                            @if ($data->name)
                                                {{ $data->name }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Brand</dt>
                                        <dd>
                                            @if ($data->getBrand)
                                                {{ $data->getBrand?->name }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Base Price</dt>
                                        <dd>₹ @if ($data->base_price)
                                                {{ formatIndianNumber($data->base_price) }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Price Validity</dt>
                                        <dd>
                                            @if ($data->price_validity)
                                                {{ $data->price_validity }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    @foreach ($data->packaging_type as $packaging_key => $packaging_type_id)
                                        @php
                                            $packaging_type = \App\Models\PackagingType::find($packaging_type_id);
                                        @endphp
                                        @if ($packaging_type)
                                            <div>
                                                <dt>{{ $packaging_type->name }}</dt>
                                                <dd>₹
                                                    {{ isset($data->packaging_type_price[$packaging_type_id]) ? formatIndianNumber($data->packaging_type_price[$packaging_type_id]) : 0 }}
                                                </dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        </div>

                        <!-- Section 2: Charges & Quality -->
                        <div class="col-md-4">
                            <div class="bz-panel h-100">
                                <h5 class="bz-panel__title">Charges &amp; Quality</h5>
                                <dl class="bz-kv-list">
                                    <div>
                                        <dt>Loading Charge</dt>
                                        <dd>₹ @if ($data->loading_charge)
                                                {{ formatIndianNumber($data->loading_charge) }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Insurance Charge</dt>
                                        <dd>₹ @if ($data->insurance_charge)
                                                {{ formatIndianNumber($data->insurance_charge) }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Quality Charge</dt>
                                        <dd>
                                            @if ($data->quality_charge)
                                                ₹ {{ formatIndianNumber($data->quality_charge) }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    @foreach ($data->charge_name as $charge_key => $charge_name)
                                        @if ($data->charge_price[$charge_key] > 0)
                                            <div>
                                                <dt>{{ $charge_name }}</dt>
                                                <dd>
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
                                                </dd>
                                            </div>
                                        @endif
                                    @endforeach
                                    @foreach ($data->quality as $quality_key => $quality)
                                        <div>
                                            <dt>{{ $quality }}</dt>
                                            <dd>₹ {{ formatIndianNumber($data->quality_price[$quality_key]) }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>

                        <!-- Section 3: Tax & Commission -->
                        <div class="col-md-4">
                            <div class="bz-panel h-100">
                                <h5 class="bz-panel__title">Tax &amp; Commission</h5>
                                <dl class="bz-kv-list">
                                    <div>
                                        <dt>GST</dt>
                                        <dd>
                                            @if ($data->gst)
                                                {{ $data->gst }} %
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>TCS</dt>
                                        <dd>
                                            @if ($data->tcs)
                                                {{ $data->tcs }} %
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Commission Type</dt>
                                        <dd>
                                            @if ($data->commission_type)
                                                {{ $data->commission_type }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>Commission</dt>
                                        <dd>
                                            @if ($data->commission_amount)
                                                ₹ {{ $data->commission_amount }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <h5 class="bz-section-label">Variation</h5>
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
                                @forelse ($variation_list as $variation_data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @foreach ($variation_data->value as $price_value)
                                            <td> {{ $price_value['value'] }} </td>
                                        @endforeach
                                        <td class="bz-num">₹ {{ formatIndianNumber($variation_data->price) }}</td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
