<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $data->getSellerCommodityProduct->name }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product-enquiry.create')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="save()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <h5>Product Specification</h5>
                                <p>Category: {{ $data->getSellerCommodityProduct->getCategory->name }}</p>
                                <p>Brand: {{ $data->getSellerCommodityProduct->getBrand->name }}</p>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="submit" class="btn btn-sm btn-success">Save</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 border-end border-danger">
                                <div class="table-responsive mt-2">
                                    <table class="custom-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Variant</th>
                                                <th>Qty (Metric Ton)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($variations as $variation)
                                                <tr>
                                                    <th>
                                                        <div class="form-check mb-3">
                                                            <input type="checkbox" class="form-check-input" id="check_{{ $loop->iteration }}" value="{{ $variation->id }}" wire:model.live="variation_id">
                                                            <label class="form-check-label" for="check_{{ $loop->iteration }}">
                                                                <span class="badge bg-danger">{{ $loop->iteration }}</span>
                                                            </label>

                                                        </div>
                                                    </th>
                                                    @foreach ($variation->value as $value)

                                                        @php
                                                            $unit_name = "";
                                                            $unit_short_name = "";
                                                            if($variation->getSellerCommodityProduct && $variation->getSellerCommodityProduct->commodity_product_id){
                                                                $commodity = App\Models\CommodityProduct::find($variation->getSellerCommodityProduct->commodity_product_id);
                                                                if($commodity && $commodity->unit){
                                                                    $unit_name = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->name : '';
                                                                    $unit_short_name = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->short_name : '';
                                                                }
                                                            }
                                                        @endphp

                                                        <td>{{ $value['name'] }} : {{ $value['value'] }} {{ $unit_short_name }}</td>
                                                    @endforeach

                                                    <td>
                                                        <input type="number" class="form-control form-control-sm" placeholder="Enter Qty" wire:model="variation_quantity.{{$variation->id}}" @if(!in_array($variation->id, $variation_id)) disabled @endif>
                                                        {{-- @error('uploaded_variation.'.getAttribute($attribute)->name) <small class="text-danger">{{ $message }}</small>@enderror --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <div wire:ignore>
                                        <label for="user_id" class="form-label">User</label>
                                        <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id" wire:model="user_id">
                                            <option value="">Select User</option>
                                            @foreach ($user_list as $user_data)
                                                <option value="{{ $user_data->id }}">{{ $user_data->name }} ( {{ $user_data->phone }} )</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('user_id') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="card card-body p-2 mb-2">
                                    <h6>Billing Address</h6>
                                    <div class="mb-2">
                                        <label for="billing_address_pincode">Pincode</label>
                                        <input type="number" class="form-control form-control-sm" id="billing_address_pincode" wire:model="billing_address.pin_code" placeholder="Enter Pincode">
                                    </div>

                                    <div class="mb-2">
                                        <label for="billing_address_line_one">Address Line 1</label>
                                        <input type="text" class="form-control form-control-sm" id="billing_address_line_one" wire:model="billing_address.address_line_one" placeholder="Enter Address Line 1">
                                    </div>

                                    <div class="mb-2">
                                        <label for="billing_address_line_two">Address Line 2</label>
                                        <input type="text" class="form-control form-control-sm" id="billing_address_line_two" wire:model="billing_address.address_line_two" placeholder="Enter Address Line 2">
                                    </div>

                                    <div class="mb-2">
                                        <label for="billing_address_city">City</label>
                                        <input type="text" class="form-control form-control-sm" id="billing_address_city" wire:model="billing_address.city" placeholder="Enter City">
                                    </div>

                                    <div class="mb-2">
                                        <label for="billing_address_state">State</label>
                                        <input type="text" class="form-control form-control-sm" id="billing_address_state" wire:model="billing_address.state" placeholder="Enter State">
                                    </div>
                                </div>

                                <div class="card card-body p-2 mb-2">
                                    <h6>Delivery Address</h6>
                                    <div class="mb-2">
                                        <label for="delivery_address_pincode">Pincode</label>
                                        <input type="number" class="form-control form-control-sm" id="delivery_address_pincode" wire:model="delivery_address.pin_code" placeholder="Enter Pincode">
                                    </div>

                                    <div class="mb-2">
                                        <label for="delivery_address_line_one">Address Line 1</label>
                                        <input type="text" class="form-control form-control-sm" id="delivery_address_line_one" wire:model="delivery_address.address_line_one" placeholder="Enter Address Line 1">
                                    </div>

                                    <div class="mb-2">
                                        <label for="delivery_address_line_two">Address Line 2</label>
                                        <input type="text" class="form-control form-control-sm" id="delivery_address_line_two" wire:model="delivery_address.address_line_two" placeholder="Enter Address Line 2">
                                    </div>

                                    <div class="mb-2">
                                        <label for="delivery_address_city">City</label>
                                        <input type="text" class="form-control form-control-sm" id="delivery_address_city" wire:model="delivery_address.city" placeholder="Enter City">
                                    </div>

                                    <div class="mb-2">
                                        <label for="delivery_address_state">State</label>
                                        <input type="text" class="form-control form-control-sm" id="delivery_address_state" wire:model="delivery_address.state" placeholder="Enter State">
                                    </div>
                                </div>

                                <div class="card card-body p-2 mb-2">
                                    <h6>Consignee Detail Address</h6>

                                    <div class="mb-2">
                                        <label for="consignee_company">Company</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_company" wire:model="consignee_detail.consignee_company" placeholder="Enter Consignee Company">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_phone">Phone</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_phone" wire:model="consignee_detail.consignee_phone" placeholder="Enter Consignee Phone">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_detail_pincode">Pincode</label>
                                        <input type="number" class="form-control form-control-sm" id="consignee_detail_pincode" wire:model="consignee_detail.address.pin_code" placeholder="Enter Pincode">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_detail_line_one">Address Line 1</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_detail_line_one" wire:model="consignee_detail.address.address_line_one" placeholder="Enter Address Line 1">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_detail_line_two">Address Line 2</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_detail_line_two" wire:model="consignee_detail.address.address_line_two" placeholder="Enter Address Line 2">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_detail_city">City</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_detail_city" wire:model="consignee_detail.address.city" placeholder="Enter City">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_detail_state">State</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_detail_state" wire:model="consignee_detail.address.state" placeholder="Enter State">
                                    </div>

                                    <div class="mb-2">
                                        <label for="consignee_detail_gst_number">GST Number</label>
                                        <input type="text" class="form-control form-control-sm" id="consignee_detail_gst_number" wire:model="consignee_detail.gst_number" placeholder="Enter GST Number">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').on('change', function (e) {
                    let elementName = $(this).attr('id');
                    var data = $(this).select2("val");
                    @this.set(elementName, data);

                    if(elementName == 'document_type_id'){
                        @this.getDocumentType();
                    }
                });
                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })
            });
        </script>
    @endpush
</div>
