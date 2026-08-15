<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                </div>
                <form wire:submit.prevent="updateSetting()">
                    <div class="card-body">
                        <div>
                            <div class="bz-toggle-row">
                                <span class="bz-toggle-row__label">
                                    New Order Enquiry
                                    <span class="bz-toggle-row__hint">Notify admin and seller when a new order enquiry is created.</span>
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify_new_order_enquiry" wire:model.defer="notify_new_order_enquiry">
                                    <label class="form-check-label visually-hidden" for="notify_new_order_enquiry">New Order Enquiry</label>
                                </div>
                            </div>

                            <div class="bz-toggle-row">
                                <span class="bz-toggle-row__label">
                                    Seller Reply
                                    <span class="bz-toggle-row__hint">Notify buyer and admin when the seller replies to an enquiry.</span>
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify_seller_reply" wire:model.defer="notify_seller_reply">
                                    <label class="form-check-label visually-hidden" for="notify_seller_reply">Seller Reply</label>
                                </div>
                            </div>

                            <div class="bz-toggle-row">
                                <span class="bz-toggle-row__label">
                                    Booking Confirmed &amp; Paid
                                    <span class="bz-toggle-row__hint">Notify seller and admin when buyer confirms and pays the booking amount.</span>
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify_booking_confirmed" wire:model.defer="notify_booking_confirmed">
                                    <label class="form-check-label visually-hidden" for="notify_booking_confirmed">Booking Confirmed &amp; Paid</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <x-submit-btn text="Save Settings" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
