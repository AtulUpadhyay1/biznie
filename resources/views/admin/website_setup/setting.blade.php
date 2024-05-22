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
                <div class="card-body">
                    <div class="col-xl-4 col-sm-6">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                <span class="fs-5">
                                    Enquiry Send to Seller
                                    <span class="input-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="It enable product enquiry automatically send to seller.">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </span>
                                <div class="form-check form-switch" wire:change="updateSetting('enquiry_send_to_seller')">
                                    <input type="checkbox" class="form-check-input" {{ $enquiry_send_to_seller == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
