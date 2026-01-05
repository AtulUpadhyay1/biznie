<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                            <small> ( {{ $data->order_id }} ) </small>
                            <span
                                class="badge rounded-pill border {{ $data->status == 'cancel' ? 'border-danger text-danger' : ($data->status == 'pending' ? 'border-warning text-warning' : 'border-primary text-primary') }} rounded-pill ms-1">{{ $data->status }}
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{ route('admin.commodity-product-order.index') }}"
                                class="btn btn-danger btn-sm btn-icon-text float-end align-items-center ms-2"
                                wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                        <div class="col-12 text-center">
                            @include('admin.commodity_product_order.menu', ['is_active' => 'edit'])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <!-- Product Details -->
                                    <tr>
                                        <td><strong>Product</strong></td>
                                        <td>{{ $data->getCommodityProduct->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Brand</strong></td>
                                        <td>{{ $data->getBrand->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Purpose</strong></td>
                                        <td>{{ $data->purpose }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-4">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <!-- Product Details -->
                                    <tr>
                                        <td><strong>Customer Company</strong></td>
                                        <td>{{ $data->getCustomer?->getUserDetail?->company_name ?? '--' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer Name</strong></td>
                                        <td>{{ $data->getCustomer?->name ?? '--' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer GST</strong></td>
                                        <td>{{ $data->getCustomer?->getUserDetail?->gst_number ?? '--' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer Phone</strong></td>
                                        <td>{{ $data->getCustomer?->phone ?? '--' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-4">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <!-- Product Details -->
                                    <tr>
                                        <td><strong>Business Name</strong></td>
                                        <td>{{ $data->getSeller?->getBusiness?->name ?? '--' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Seller Name</strong></td>
                                        <td>{{ $data->getSeller?->name ?? '--' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Seller Phone</strong></td>
                                        <td>{{ $data->getSeller?->phone ?? '--' }}</td>
                                    </tr>

                                    <!-- Transporter Details (Conditional) -->
                                    @if ($data->getTransporter)
                                        <tr>
                                            <td><strong>Transporter Name</strong></td>
                                            <td>{{ $data->getTransporter->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Transporter Phone</strong></td>
                                            <td>{{ $data->getTransporter->phone }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <h5 class="my-3">Product Variation</h5>

                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @foreach ($data->value[0]['value'] as $variation_heading)
                                            <th>{{ $variation_heading['name'] }}</th>
                                        @endforeach
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->value as $variation)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            @foreach ($variation['value'] as $value)
                                                <td>{{ $value['value'] }}</td>
                                            @endforeach
                                            <td>{{ $variation['quantity'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive mt-3">
                                <div class="row my-3">
                                    <div class="col-6">
                                        <h5>Update Product Variation</h5>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="update_for"
                                                    id="quantity" value="quantity" wire:model="update_for">
                                                <label class="form-check-label" for="quantity">
                                                    Quantity
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="update_for"
                                                    id="variant" value="variant" wire:model="update_for">
                                                <label class="form-check-label" for="variant">
                                                    Size
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="update_for"
                                                    id="both" value="both" wire:model="update_for">
                                                <label class="form-check-label" for="both">
                                                    Both
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="custom-table">
                                    @php
                                        $variation_value = 0;
                                        foreach ($variations as $variation) {
                                            $variation_value = count($variation->value);
                                        }
                                    @endphp
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th colspan="{{ $variation_value }}">Variant</th>
                                            <th>Qty (Metric Ton)</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($variations as $variation)
                                            <tr>
                                                <th>
                                                    <div class="form-check mb-3">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="check_{{ $loop->iteration }}"
                                                            value="{{ $variation->id }}"
                                                            wire:model.live="variation_id">
                                                        <label class="form-check-label"
                                                            for="check_{{ $loop->iteration }}">
                                                            <span class="badge bg-danger">{{ $loop->iteration }}</span>
                                                        </label>

                                                    </div>
                                                </th>
                                                @foreach ($variation->value as $value)
                                                    @php
                                                        $unit_name = '';
                                                        $unit_short_name = '';
                                                        if (
                                                            $variation->getSellerCommodityProduct &&
                                                            $variation->getSellerCommodityProduct->commodity_product_id
                                                        ) {
                                                            $commodity = App\Models\CommodityProduct::find(
                                                                $variation->getSellerCommodityProduct
                                                                    ->commodity_product_id,
                                                            );
                                                            if ($commodity && $commodity->unit) {
                                                                $unit_name = getProductUnit(
                                                                    $commodity->unit[$value['name']],
                                                                )
                                                                    ? getProductUnit($commodity->unit[$value['name']])
                                                                        ->name
                                                                    : '';
                                                                $unit_short_name = getProductUnit(
                                                                    $commodity->unit[$value['name']],
                                                                )
                                                                    ? getProductUnit($commodity->unit[$value['name']])
                                                                        ->short_name
                                                                    : '';
                                                            }
                                                        }
                                                    @endphp

                                                    <td>{{ $value['name'] }} : {{ $value['value'] }}
                                                        {{ $unit_short_name }}</td>
                                                @endforeach

                                                <td>
                                                    <input type="number" class="form-control form-control-sm"
                                                        placeholder="Enter Qty"
                                                        wire:model="variation_quantity.{{ $variation->id }}"
                                                        @if (!in_array($variation->id, $variation_id)) disabled @endif>
                                                    {{-- @error('uploaded_variation.' . getAttribute($attribute)->name) <small class="text-danger">{{ $message }}</small>@enderror --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <button type="button" class="btn btn-sm btn-success" wire:click="update()">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
