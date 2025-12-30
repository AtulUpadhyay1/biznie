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
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">New Order Enquiry</h6>
                                    <small class="text-muted">Notify admin and seller when a new order enquiry is created.</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify_new_order_enquiry" wire:model.defer="notify_new_order_enquiry">
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Seller Reply</h6>
                                    <small class="text-muted">Notify buyer and admin when the seller replies to an enquiry.</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify_seller_reply" wire:model.defer="notify_seller_reply">
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Booking Confirmed & Paid</h6>
                                    <small class="text-muted">Notify seller and admin when buyer confirms and pays the booking amount.</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify_booking_confirmed" wire:model.defer="notify_booking_confirmed">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
