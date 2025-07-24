<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>

                    </div>
                </div>
                <form wire:submit.prevent="updateSetting()">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                        <span class="fs-5">
                                            Enquiry Send to Seller
                                            <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="It enable product enquiry automatically send to seller.">
                                                <i class="bi bi-info-circle"></i>
                                            </span>
                                        </span>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" wire:model="value.enquiry_send_to_seller" {{$value['enquiry_send_to_seller'] == true ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                        <span class="fs-5">
                                            Enquiry Send to Transporter
                                            <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="It enable product enquiry automatically send to transporter.">
                                                <i class="bi bi-info-circle"></i>
                                            </span>
                                        </span>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" wire:model="value.enquiry_send_to_transporter" {{$value['enquiry_send_to_transporter'] == true ? 'checked' : ''}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-xl-4 col-sm-6">
                                <label for="seller_enquiry_reply_time" class="form-label">Seller Enquiry Reply Time (Min)
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="right" title="Time in minutes for the seller to reply to an enquiry.">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </label>
                                <input type="number" class="form-control" placeholder="Seller Enquiry Reply Time (Min)" id="seller_enquiry_reply_time" wire:model="value.seller_enquiry_reply_time">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="minimum_balance_for_enquiry" class="form-label">Minimum Balance For Enquiry
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="right" title="The minimum balance required to make an enquiry.">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </label>
                                <input type="number" class="form-control" placeholder="Minimum Balance For Enquiry" id="minimum_balance_for_enquiry" wire:model="value.minimum_balance_for_enquiry">

                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="order_token_amount" class="form-label">Order Token Amount (%)
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="right" title="The percentage of the order amount required as a token.">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </label>
                                <input type="number" class="form-control" placeholder="Order Token Amount (%)" id="order_token_amount" wire:model="value.order_token_amount">

                            </div>
                        </div>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                        <span class="fs-5">
                                            Customer Quality Check Visibility
                                            <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="It enable product order quality check visible to customer.">
                                                <i class="bi bi-info-circle"></i>
                                            </span>
                                        </span>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" wire:model="value.customer_quality_check_visibility" {{$value['customer_quality_check_visibility'] == true ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="master_otp" class="form-label">Master OTP
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="right" title="The OTP required for master access.">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </label>
                                <input type="number" class="form-control" placeholder="Master OTP" id="master_otp" wire:model="value.master_otp">

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-6">
                            <x-submit-btn text="Update" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
