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
                            <small> ( {{ $data->order_id }} ) </small>
                            <span class="badge rounded-pill border {{$data->status == 'cancel' ? 'border-danger text-danger' : 'border-primary text-primary' }} rounded-pill ms-1">{{ $data->status }} </span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.commodity-product-order.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center ms-2" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                        <div class="col-12 text-center">
                            @include('admin.commodity_product_order.menu', ['is_active' => 'status'])
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

    <div class="modal fade" id="otpVeryfiy" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="otpVeryfiyLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpVeryfiyLabel">OTP Verification</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <label for="otp">Otp Send On {{ $data->getCustomer?->phone }}</label>
                    <input type="number" class="form-control" id="otp" placeholder="Enter OTP" wire:model="otp">
                    <div class="text-end">
                        <small id="resend-otp" class="d-none" wire:click="sendOtp()">
                            <a href="javascript:;" onclick="restartTimer()">Resend OTP</a>
                        </small>
                        <small id="timer"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-danger btn-sm" wire:navigate>Close</a>
                    <button type="button" class="btn btn-success btn-sm" wire:click="verifyOtp()" wire:loading.attr="disabled">
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

            modalElement.addEventListener('shown.bs.modal', function () {
                startTimer(60);
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                clearInterval(timer);
                timerElement.textContent = '';
                resendOtpElement.classList.add('d-none');
            });
        </script>
    @endpush
</div>
