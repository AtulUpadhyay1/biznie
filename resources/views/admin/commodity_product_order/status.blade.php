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
                        @include('admin.commodity_product_order.menu', ['is_active' => 'status'])
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <p><b>Current Status : </b>
                                <span
                                    class="bz-status {{ $data->status == 'cancel' ? 'bz-status--danger' : ($data->status == 'delivered' ? 'bz-status--success' : ($data->status == 'pending' ? 'bz-status--warning' : 'bz-status--info')) }}">{{ ucwords($data->status) }}</span>
                            </p>
                        </div>
                        @if ($data->status != 'cancel')
                            <div class="col-4 mb-3">
                                <label class="form-label" for="status">Update Status</label>
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

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Request For Quotation</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Enquiry ID</dt>
                                            <dd>{{ $enquiry_data->unique_id }}</dd>
                                        </div>

                                        <div>
                                            <dt>Category</dt>
                                            <dd>{{ $enquiry_data->getCommodityProduct->getCategory->name }}</dd>
                                        </div>

                                        <div>
                                            <dt>Product</dt>
                                            <dd>{{ $enquiry_data->getCommodityProduct->name }}</dd>
                                        </div>

                                        <div>
                                            <dt>Brand</dt>
                                            <dd>{{ $enquiry_data->getBrand->name }}</dd>
                                        </div>

                                        {{-- Delivery Location (Uncomment if needed)
        <div>
            <dt>Delivery Location</dt>
            <dd>
                {{ $enquiry_data->consignee_detail['address_line_one'] }},
                {{ $enquiry_data->consignee_detail['address_line_two'] }},
                {{ $enquiry_data->consignee_detail['city'] }},
                {{ $enquiry_data->consignee_detail['pin_code']
                    ?? $enquiry_data->consignee_detail['pincode'] }}
            </dd>
        </div>
        --}}

                                        <div>
                                            <dt>Purpose</dt>
                                            <dd>{{ $enquiry_data->purpose }}</dd>
                                        </div>

                                        <div>
                                            <dt>Description</dt>
                                            <dd>{{ $enquiry_data->description }}</dd>
                                        </div>

                                        @if ($enquiry_data->quality)
                                            <div>
                                                <dt>Quality</dt>
                                                <dd>
                                                    {{ $enquiry_data->quality['name'] }} –
                                                    ₹ {{ formatIndianNumber($enquiry_data->quality['price']) }}
                                                </dd>
                                            </div>
                                        @endif

                                        @if ($enquiry_data->packaging_charge)
                                            <div>
                                                <dt>Packaging Charge</dt>
                                                <dd>
                                                    {{ $enquiry_data->packaging_charge['name'] }} –
                                                    ₹
                                                    {{ formatIndianNumber($enquiry_data->packaging_charge['charge']) }}
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Buyer Details</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->company_name }}</dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $enquiry_data->getUser->phone }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->gst_number }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 1</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->address_line_one }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 2</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->address_line_two }}</dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->city }}</dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->state }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ $enquiry_data->getUser->getUserDetail->postal_code }}</dd>
                                        </div>

                                        <div>
                                            <dt>Credit Days</dt>
                                            <dd>{{ $enquiry_data->getUser->credit_days }} Days</dd>
                                        </div>
                                    </dl>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Seller Details</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    @php
                                        $seller = $enquiry_data->getMarkedSellerProductEnquiry->getUser;
                                        $sellerDetail = $seller->getSellerKycDetail;
                                    @endphp
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $seller->getBusiness->name }}</dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $seller->phone }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $sellerDetail->gst_number }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line One</dt>
                                            <dd>{{ $sellerDetail->address_line_one }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line Two</dt>
                                            <dd>{{ $sellerDetail->address_line_two }}</dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $sellerDetail->city }}</dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $sellerDetail->state }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>{{ $sellerDetail->postal_code }}</dd>
                                        </div>

                                        <div>
                                            <dt>Credit Days</dt>
                                            <dd>{{ $seller_enquiry_data->seller_credit_days }} Days</dd>
                                        </div>
                                    </dl>

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
                                <div class="card-body p-3">
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $enquiry_data->billing_address['company_name'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $enquiry_data->billing_address['phone'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $enquiry_data->billing_address['gst'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 1</dt>
                                            <dd>{{ $enquiry_data->billing_address['address_line_one'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 2</dt>
                                            <dd>{{ $enquiry_data->billing_address['address_line_two'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $enquiry_data->billing_address['state'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $enquiry_data->billing_address['city'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>
                                                {{ $enquiry_data->consignee_detail['pin_code'] ?? ($enquiry_data->billing_address['pincode'] ?? '') }}
                                            </dd>
                                        </div>
                                    </dl>

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
                                <div class="card-body p-3">
                                    <dl class="bz-kv-list">
                                        <div>
                                            <dt>Company</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['company_name'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Phone</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['phone'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>GST</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['gst'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 1</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['address_line_one'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Address Line 2</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['address_line_two'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>State</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['state'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>City</dt>
                                            <dd>{{ $enquiry_data->consignee_detail['city'] }}</dd>
                                        </div>

                                        <div>
                                            <dt>Pincode</dt>
                                            <dd>
                                                {{ $enquiry_data->consignee_detail['pin_code'] ?? ($enquiry_data->billing_address['pincode'] ?? '') }}
                                            </dd>
                                        </div>
                                    </dl>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5>Loading Address</h5>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    @if ($loading_address)
                                        <dl class="bz-kv-list">
                                            <div>
                                                <dt>Address Line One</dt>
                                                <dd>{{ $loading_address->address_line_one }}</dd>
                                            </div>

                                            <div>
                                                <dt>Address Line Two</dt>
                                                <dd>{{ $loading_address->address_line_two }}</dd>
                                            </div>

                                            <div>
                                                <dt>City</dt>
                                                <dd>{{ $loading_address->city }}</dd>
                                            </div>

                                            <div>
                                                <dt>State</dt>
                                                <dd>{{ $loading_address->state }}</dd>
                                            </div>

                                            <div>
                                                <dt>Pincode</dt>
                                                <dd>{{ $loading_address->pincode }}</dd>
                                            </div>
                                        </dl>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-4">
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
                        </div> --}}
                    </div>
                    {{-- <div class="row mt-3">
                        <div class="col-6">
                            <p>
                                <b>Billing Address</b> <br>
                                <b>Pincode: </b> {{ isset($data->consignee_detail['pin_code']) ? $data->consignee_detail['pin_code'] : (isset($data->billing_address['pincode']) ? $data->billing_address['pincode'] : '') }} <br>
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
                                <b>Pincode: </b> {{ isset($data->consignee_detail['pin_code']) ? $data->consignee_detail['pin_code'] : (isset($data->consignee_detail['pincode']) ? $data->consignee_detail['pincode'] : '') }} <br>
                                <b>Address 1: </b> {{ $data->consignee_detail['address_line_one'] }} <br>
                                <b>Address 2: </b> {{ $data->consignee_detail['address_line_two'] }} <br>
                                <b>City: </b> {{ $data->consignee_detail['city'] }} <br>
                                <b>State: </b> {{ $data->consignee_detail['state'] }} <br>
                                <b>Gst Number: </b> {{ $data->consignee_detail['gst'] }} <br>
                            </p>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="orderCancel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="orderCancelLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <x-loader />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderCancelLabel">Cancel Reason</h5>
                </div>
                <div class="modal-body">
                    <span class="bz-section-label">Please Select Reason <span class="text-danger">*</span></span>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="price_increased"
                            value="Price Increased" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="price_increased">
                            Price Increased
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="price_descreased"
                            value="Price Decreased" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="price_descreased">
                            Price Decreased
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="sale_closed"
                            value="Sale Closed" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="sale_closed">
                            Sale Closed
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="stock_out"
                            value="Stock Out" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="stock_out">
                            Stock Out
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_reason" id="other"
                            value="Other" wire:model.live="cancel_reason">
                        <label class="form-check-label" for="other">
                            Other
                        </label>
                    </div>
                    @if ($this->cancel_reason == 'Other')
                        <div class="mb-3">
                            <label class="form-label" for="cancel_reason">Reason Details</label>
                            <textarea class="form-control" id="cancel_reason" rows="5" wire:model="cancel_reason_text"
                                placeholder="Please provide a brief description of why you want to cancel this order."></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-secondary btn-sm" wire:navigate>Close</a>
                    @if ($this->cancel_reason)
                        <button type="button" class="btn btn-danger btn-sm" wire:click="updateStatus()"
                            data-bs-dismiss="modal">Update</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="otpVeryfiy" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="otpVeryfiyLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpVeryfiyLabel">OTP Verification</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <label class="form-label" for="otp">Otp Send On {{ $data->getCustomer?->phone }}</label>
                    <input type="number" class="form-control" id="otp" placeholder="Enter OTP"
                        wire:model="otp">
                    <div class="text-end">
                        <small id="resend-otp" class="d-none" wire:click="sendOtp()">
                            <a href="javascript:;" onclick="restartTimer()">Resend OTP</a>
                        </small>
                        <small id="timer"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-secondary btn-sm" wire:navigate>Close</a>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="verifyOtp()"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="verifyOtp">Verify</span>
                        <span wire:loading wire:target="verifyOtp">Verifying...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', function() {
                const statusSelect = document.getElementById('status');
                if (statusSelect) {
                    statusSelect.addEventListener('change', function() {
                        const status = this.value;
                        if (status === 'cancel') {
                            $('#orderCancel').modal('show');
                        } else if (status === 'delivered') {
                            Swal.fire({
                                title: "Are you sure you want to mark this order as delivered?",
                                text: "Select 'Proceed Without OTP' to proceed without OTP or 'Generate OTP' to send OTP.",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Proceed Without OTP",
                                cancelButtonText: "Generate OTP",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    @this.updateStatus();
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    @this.sendOtp();
                                    $('#otpVeryfiy').modal('show');
                                }
                            });
                        } else {
                            @this.updateStatus();
                        }
                    });
                }
            });

            // Also trigger on initial load
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.dispatch('navigated');
            });
        </script>

        <script>
            const modalElement = document.getElementById('otpVeryfiy');
            const resendOtpElement = document.getElementById('resend-otp');
            const timerElement = document.getElementById('timer');
            let timer;

            function startTimer(seconds) {
                let remaining = seconds;
                resendOtpElement.classList.add('d-none');

                clearInterval(timer);
                timer = setInterval(() => {
                    if (remaining > 0) {
                        timerElement.textContent = `Resend OTP in ${remaining--} seconds`;
                    } else {
                        clearInterval(timer);
                        timerElement.textContent = '';
                        resendOtpElement.classList.remove('d-none');
                    }
                }, 1000);
            }

            function restartTimer() {
                startTimer(60);
            }

            modalElement.addEventListener('shown.bs.modal', function() {
                startTimer(60);
            });

            modalElement.addEventListener('hidden.bs.modal', function() {
                clearInterval(timer);
                timerElement.textContent = '';
                resendOtpElement.classList.add('d-none');
            });
        </script>
    @endpush
</div>
