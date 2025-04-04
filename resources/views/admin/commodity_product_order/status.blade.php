<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="row">
        <x-loader />
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 card-title">
                            <h4>{{ $page_title }}</h4>
                            <small> ( {{ $data->order_id }} ) </small>
                            <span class="badge rounded-pill border {{$data->status == 'cancel' ? 'border-danger text-danger' : 'border-primary text-primary' }} rounded-pill ms-1">{{ $data->status }} </span>
                        </div>
                        <div class="col-8 text-end">
                            {{-- <a href="javasript:;" class="btn btn-info btn-icon me-1" wire:click="invoicePrint()" title="Print Invoice"><i class="bi bi-printer-fill"></i></a> --}}
                            <a href="{{route('admin.commodity-product-order.ledger', $data->id)}}" class="btn btn-warning btn-sm" title="Receive Paymet" wire:navigate>
                                Ledger
                            </a>

                            <a href="{{route('admin.commodity-product-order.sellerLedger', $data->id)}}" class="btn btn-info btn-sm" title="Receive Paymet" wire:navigate>
                                Seller Ledger
                            </a>

                            <a href="{{route('admin.commodity-product-order.receive-payment', $data->id)}}" class="btn btn-outline-primary btn-sm" title="Receive Paymet" wire:navigate>
                                Receive Paymet
                            </a>

                            <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-outline-info btn-sm" title="Send Paymet" wire:navigate>
                                Send Paymet
                            </a>

                            <a href="{{route('admin.commodity-product-order.show', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="View" wire:navigate>
                                <i class="bi bi-eye icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.status', $data->id)}}" class="btn btn-secondary btn-icon btn-sm" title="Update Status" wire:navigate>
                                <i class="bi bi-device-ssd icon-sm"></i>
                            </a>
                            <a href="{{route('admin.commodity-product-order.history', $data->id)}}" class="btn btn-secondary btn-icon btn-sm me-1" title="History" wire:navigate>
                                <i class="bi bi-clock-history icon-sm"></i>
                            </a>

                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <p><b>Current Status : </b> {{ ucwords($data->status) }}</p>
                        </div>
                        @if ($data->status != 'cancel')
                            <div class="col-4 text-end mb-2">
                                <select class="form-select" wire:model="status" id="status">
                                    <option value="pending" disabled="">Pending</option>
                                    <option value="confirm">Confirm</option>
                                    <option value="vehicle booked">Vehicle Booked</option>
                                    <option value="vehicle waiting to load">Vehicle Waiting To Load</option>
                                    <option value="loading">Loading</option>
                                    <option value="bills generated">Bills Generated</option>
                                    <option value="dispatched">Dispatched</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancel">Cancel</option>
                                </select>
                            </div>
                        @endif
                        <hr>
                        <div class="col-4">
                            <p>
                                <b>Product: </b> {{ $data->getCommodityProduct->name }} <br>
                                <b>Brand: </b> {{ $data->getBrand->name }} <br>
                                <b>Purpose: </b> {{ $data->purpose }} <br>
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <p>
                                <b>Company Name: </b> {{ $data->getCustomer?->getUserDetail?->company_name ?? '--' }} <br>
                                <b>User: </b> {{ $data->getCustomer?->name }} <br>
                                <b>GST: </b> {{ $data->getCustomer?->getUserDetail?->gst_number ?? '--' }} <br>
                                <b>Phone: </b> {{ $data->getCustomer?->phone }} <br>
                            </p>
                        </div>
                        <div class="col-4 text-end">
                            <p>
                                <b>Business Name: </b> {{ $data->getSeller?->getBusiness?->name }} <br>
                                <b>Seller: </b> {{ $data->getSeller->name }} <br>
                                <b>Phone: </b> {{ $data->getSeller->phone }} <br>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <p>
                                <b>Billing Address</b> <br>
                                <b>Pincode: </b> {{ $data->billing_address['pincode'] }} <br>
                                <b>Address: </b> {{ $data->billing_address['address_line_one']??'' }} <br>
                                <b>City: </b> {{ $data->billing_address['city'] }} <br>
                                <b>State: </b> {{ $data->billing_address['state'] }} <br>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p>
                                <b>Consignee Detail</b> <br>
                                <b>Company: </b> {{ $data->consignee_detail['company_name'] }} <br>
                                <b>Phone: </b> {{ $data->consignee_detail['phone'] }} <br>
                                <b>Pincode: </b> {{ $data->consignee_detail['pincode'] }} <br>
                                <b>Address 1: </b> {{ $data->consignee_detail['address_line_one'] }} <br>
                                <b>Address 2: </b> {{ $data->consignee_detail['address_line_two'] }} <br>
                                <b>City: </b> {{ $data->consignee_detail['city'] }} <br>
                                <b>State: </b> {{ $data->consignee_detail['state'] }} <br>
                                <b>Gst Number: </b> {{ $data->consignee_detail['gst'] }} <br>
                            </p>
                        </div>
                    </div>

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
                                    <th>Gauge Diff.</th>
                                    <th>Final Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->value as $variation)

                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        @foreach ($variation['value'] as $value)
                                            <td>{{ $value['value'] }}</td>
                                        @endforeach
                                        <td>{{ $variation['quantity'] }}</td>
                                        <td>{{ $variation['price'] }}</td>
                                        <td>{{ $variation['price'] + $data->base_price }}</td>
                                    </tr>

                                @endforeach

                                <tr>
                                    <td colspan="{{ count($data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="base_price" class="form-label">Base Price</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $data->base_price }}
                                        </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ count($data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="transport_price" class="form-label">Transport Price</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $data->transport_price }}
                                        </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ count($data->value[0]['value'])+1 }}" style="border-left: hidden; border-bottom: hidden;">
                                        <td>
                                            <label for="commission" class="form-label">Commission</label>
                                        </td>
                                        <td colspan="2">
                                            {{ $data->commission }}
                                        </td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="orderCancel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="orderCancelLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <x-loader />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderCancelLabel">Cancel Reason</h5>
                </div>
                <div class="modal-body">
                    <lable for="cancel_reason">Please Select Reason <span class="text-danger">*</span></lable>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="price_increased" value="Price Increased" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="price_increased">
                            Price Increased
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="price_descreased" value="Price Decreased" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="price_descreased">
                            Price Decreased
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="sale_closed" value="Sale Closed" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="sale_closed">
                            Sale Closed
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="stock_out" value="Stock Out" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="stock_out">
                            Stock Out
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="other" value="Other" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="other">
                            Other
                        </label>
                    </div>
                    @if($this->cancel_reason == 'Other')
                        <textarea class="form-control" id="cancel_reason" rows="5" wire:model="cancel_reason_text" placeholder="Please provide a brief description of why you want to cancel this order."></textarea>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-secondary" wire:navigate>Close</a>
                    @if ($this->cancel_reason)
                        <button type="button" class="btn btn-danger" wire:click="updateStatus()" data-bs-dismiss="modal">Update</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#status').change(function() {
                    let status = $(this).val();
                    if(status != 'cancel'){
                        @this.updateStatus();
                    }else{
                        $('#orderCancel').modal('show');
                    }
                });
            });
        </script>
    @endpush
</div>
