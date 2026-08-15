<div>
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
                        @include('admin.commodity_product_order.menu', ['is_active' => 'edit'])
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Product</dt>
                                    <dd>{{ $data->getCommodityProduct->name }}</dd>
                                </div>
                                <div>
                                    <dt>Brand</dt>
                                    <dd>{{ $data->getBrand->name }}</dd>
                                </div>
                                <div>
                                    <dt>Purpose</dt>
                                    <dd>{{ $data->purpose }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="col-4">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Customer Company</dt>
                                    <dd>{{ $data->getCustomer?->getUserDetail?->company_name ?? '--' }}</dd>
                                </div>
                                <div>
                                    <dt>Customer Name</dt>
                                    <dd>{{ $data->getCustomer?->name ?? '--' }}</dd>
                                </div>
                                <div>
                                    <dt>Customer GST</dt>
                                    <dd>{{ $data->getCustomer?->getUserDetail?->gst_number ?? '--' }}</dd>
                                </div>
                                <div>
                                    <dt>Customer Phone</dt>
                                    <dd>{{ $data->getCustomer?->phone ?? '--' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="col-4">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Business Name</dt>
                                    <dd>{{ $data->getSeller?->getBusiness?->name ?? '--' }}</dd>
                                </div>
                                <div>
                                    <dt>Seller Name</dt>
                                    <dd>{{ $data->getSeller?->name ?? '--' }}</dd>
                                </div>
                                <div>
                                    <dt>Seller Phone</dt>
                                    <dd>{{ $data->getSeller?->phone ?? '--' }}</dd>
                                </div>

                                <!-- Transporter Details (Conditional) -->
                                @if ($data->getTransporter)
                                    <div>
                                        <dt>Transporter Name</dt>
                                        <dd>{{ $data->getTransporter->name }}</dd>
                                    </div>
                                    <div>
                                        <dt>Transporter Phone</dt>
                                        <dd>{{ $data->getTransporter->phone }}</dd>
                                    </div>
                                @endif
                            </dl>
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
                                                    <label class="visually-hidden"
                                                        for="variation_quantity_{{ $variation->id }}">Quantity</label>
                                                    <input type="number" id="variation_quantity_{{ $variation->id }}"
                                                        class="form-control form-control-sm" placeholder="Enter Qty"
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
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-danger btn-sm" wire:click="update()"><i
                                    class="bi bi-check-lg"></i>Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
