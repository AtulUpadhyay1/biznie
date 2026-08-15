<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $data->getSellerCommodityProduct->name }}</h4>
                    <div class="bz-toolbar">
                        <a href="{{route('admin.commodity-product-enquiry.create')}}" class="btn btn-secondary btn-sm" wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
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
                            <div class="col-md-2 d-flex align-items-start justify-content-end">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 border-end">
                                <div class="table-responsive mt-2">
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
                                                    <td>
                                                        <div class="form-check mb-3">
                                                            <input type="checkbox" class="form-check-input" id="check_{{ $loop->iteration }}" value="{{ $variation->id }}" wire:model.live="variation_id">
                                                            <label class="form-check-label" for="check_{{ $loop->iteration }}">
                                                                <span class="badge bg-danger">{{ $loop->iteration }}</span>
                                                            </label>

                                                        </div>
                                                    </td>
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
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="mb-3">
                                            <div class="bz-section-label">Select Quality</div>
                                            @foreach ($data->getCommodityProduct->quality as $quality_key => $quality)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="quality" value="{{ $quality }}" id="quality_{{ $quality_key }}" wire:model.live="selected_quality">
                                                    <label class="form-check-label" for="quality_{{ $quality_key }}">
                                                        {{ $quality }} <span class="text-danger">(₹ {{ formatIndianNumber($data->getCommodityProduct->quality_price[$quality_key]) }})</span>
                                                    </label>
                                                </div>
                                                
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3">
                                            <div class="bz-section-label">Select Packaging Type</div>
                                            @foreach ($package_type_array as $packaging)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="packaging_type" value="{{ $packaging['id'] }}" id="packaging_type_{{ $packaging['id'] }}" wire:model.live="selected_packaging_type">
                                                    <label class="form-check-label" for="packaging_type_{{ $packaging['id'] }}">
                                                        {{ $packaging['name'] }} <span class="text-danger">(₹ {{ formatIndianNumber($packaging['charge']) }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div wire:ignore>
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="user_id" class="form-label">Company (User) <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-6 d-flex align-items-center justify-content-end">
                                                <a href="#" class="btn btn-secondary btn-sm mb-1">Add</a>
                                            </div>
                                        </div>
                                        <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id" wire:model="user_id">
                                            <option value="">Select Company</option>
                                            @foreach ($user_list as $user_data)
                                                <option value="{{ $user_data->id }}">{{ $user_data->name }} ( {{ $user_data->phone }} )</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('user_id') <small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="card card-body p-2 mb-3">
                                    <h6>Billing Address (Bill To)</h6>
                                    @forelse ($user_address_list as $user_address_data) 
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input" id="billing_address_{{ $user_address_data->id }}" wire:model.live="billing_address_id" value="{{ $user_address_data->id }}">
                                                    <label class="form-check-label" for="billing_address_{{ $user_address_data->id }}">
                                                        <span class="bz-chip">Select</span>
                                                    </label>
                                                </div>
                                                <hr>
                                                <dl class="bz-kv-list">
                                                    <div>
                                                        <dt>Company Name</dt>
                                                        <dd>{{ $user_address_data->company_name }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Phone Number</dt>
                                                        <dd>{{ $user_address_data->phone }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Gst Number</dt>
                                                        <dd>{{ $user_address_data->gst }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Address Line1</dt>
                                                        <dd>{{ $user_address_data->address_line_one }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Address Line2</dt>
                                                        <dd>{{ $user_address_data->address_line_two }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>City</dt>
                                                        <dd>{{ $user_address_data->city }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>State</dt>
                                                        <dd>{{ $user_address_data->state }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Pincode</dt>
                                                        <dd>{{ $user_address_data->pincode }}</dd>
                                                    </div>
                                                </dl>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="card">
                                            <div class="card-body p-2 text-center">
                                                <p>No billing address available.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="card card-body p-2 mb-3">
                                    <h6>Consignee Details (Ship To) <br></h6>
                                    <div class="text-end">
                                        <input type="checkbox" class="form-check-input" id="same_buyer_address" wire:model.live="same_buyer_address" wire:change="consigneeAddress()">
                                        <label class="form-check-label" for="same_buyer_address">
                                            <span class="bz-chip">Same Buyer Address</span>
                                        </label>
                                    </div>
                                    @forelse ($user_address_list as $user_address_data) 
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input" id="consignee_address_{{ $user_address_data->id }}" wire:model.live="consignee_address_id" value="{{ $user_address_data->id }}">
                                                    <label class="form-check-label" for="consignee_address_{{ $user_address_data->id }}">
                                                        <span class="bz-chip">Select</span>
                                                    </label>
                                                </div>
                                                <hr>
                                                <dl class="bz-kv-list">
                                                    <div>
                                                        <dt>Company Name</dt>
                                                        <dd>{{ $user_address_data->company_name }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Phone Number</dt>
                                                        <dd>{{ $user_address_data->phone }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Gst Number</dt>
                                                        <dd>{{ $user_address_data->gst }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Address Line1</dt>
                                                        <dd>{{ $user_address_data->address_line_one }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Address Line2</dt>
                                                        <dd>{{ $user_address_data->address_line_two }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>City</dt>
                                                        <dd>{{ $user_address_data->city }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>State</dt>
                                                        <dd>{{ $user_address_data->state }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt>Pincode</dt>
                                                        <dd>{{ $user_address_data->pincode }}</dd>
                                                    </div>
                                                </dl>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="card">
                                            <div class="card-body p-2 text-center">
                                                <p>No billing address available.</p>
                                            </div>
                                        </div>
                                    @endforelse
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

                    if(elementName == 'user_id'){
                        @this.getUserAddress();
                    }
                });
                window.addEventListener('render-select2', event => {
                    $('.select2').select2();
                })
            });
        </script>
    @endpush
</div>
