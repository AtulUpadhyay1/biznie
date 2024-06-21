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
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="minimum_balance_for_enquiry" class="form-label">Minimum Balance For Enquiry
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="right" title="Minimum Balance For Enquiry">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </label>
                                <input type="number" class="form-control" placeholder="Minimum Balance For Enquiry" id="minimum_balance_for_enquiry" wire:model="value.minimum_balance_for_enquiry">

                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="order_token_amount" class="form-label">Order Token Amount (%)
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="right" title="Order Token Amount (%)">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </label>
                                <input type="number" class="form-control" placeholder="Order Token Amount (%)" id="order_token_amount" wire:model="value.order_token_amount">

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
