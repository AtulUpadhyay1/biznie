<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{ $data }}
                    <h4>{{ $data->name }}</h4>
                    <hr>
                    <p><b>Category :</b> {{ $data->getCategory ? $data->getCategory->name : '--' }}</p>

                    <p><b>Sub Category :</b> {{ $data->getSubCategory ? $data->getSubCategory->name : '--' }}</p>

                    <p><b>Sub sub Category :</b> {{ $data->getSubSubCategory ? $data->getSubSubCategory->name : '--' }}</p>

                    <p>
                        <b>Brand :</b>
                        @foreach ($data->brand_id as $brand_id)
                            {{ getBrand($brand_id)->name }}@if(!$loop->last), @endif
                        @endforeach
                    </p>

                    <p><b>Unit :</b> {{ $data->getUnit ? $data->getUnit->name : '--' }}</p>

                    <p><b>Description :</b> {{ $data->description }}</p>

                    <p><b>PackagingType :</b>
                        @foreach ($data->packaging_type as $packaging_type)
                            {{ getPackagingType($packaging_type)->name}} - ₹ {{ $data->packaging_type_price[$loop->iteration] }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                    <hr>
                    <h4>Pricing & Others</h4>
                    <hr>
                    <p><b>Base Price :</b> ₹ {{ $data->base_price }}</p>
                    <p><b>Loading charge :</b> ₹ {{ $data->loading_charge }}</p>
                    <p><b>Insurance Charge :</b> ₹ {{ $data->insurance_charge }}</p>
                    <p><b>Quality Inspection Charge :</b> ₹ {{ $data->quality_charge }}</p>
                    <p><b>GST (%) :</b> ₹ {{ $data->gst }}</p>
                    <p><b>TCS (%) :</b> ₹ {{ $data->tcs }}</p>
                    <p><b>Other Charges :</b>
                        @foreach ($data->charge_name as $charge_name)
                            {{ $charge_name }} - ₹ {{ $data->operator[$loop->iteration] }}{{ $data->charge_price[$loop->iteration] }},
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
