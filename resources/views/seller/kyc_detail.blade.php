<div>
    @section('title', config('app.name') . ' | '.$page_title)

    <div class="card">
        <div class="row">
            <div class="col-md-12">
                @if ($business_section)
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 card-title">
                                    <h4>Business Detail</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label for="business_name" class="form-label">Business Name</label>
                                        <input type="text" class="form-control" id="business_name" placeholder="Business name" wire:model="business_name" disabled>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="business_about" class="form-label">About Business</label>
                                        <textarea name="business_about" class="form-control" id="business_about" cols="30" rows="2" wire:model="business_about" disabled></textarea>
                                    </div>
                                </div>
                                <div class="row text-end">
                                    <div class="col-md-12">
                                        <a href="{{route('seller.kyc-detail')}}?address_section=true" class="btn btn-success btn-sm btn-icon-text" type="button" title="Next" wire:navigate>Next <i class="bi bi-arrow-right-circle"></i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                @if ($address_section)
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 card-title">
                                    <h4>Address Detail</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-3 mb-3">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code" wire:model="postal_code" disabled>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" wire:model="city" disabled>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" wire:model="state" disabled>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" wire:model="country" disabled>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea name="address" class="form-control" id="address" cols="30" rows="2" wire:model="address" disabled></textarea>
                                    </div>
                                </div>
                                <div class="row text-end">
                                    <div class="col-md-12">
                                        <a href="{{route('seller.kyc-detail')}}?address_section=true" class="btn btn-success btn-sm btn-icon-text" type="button" title="Next" wire:navigate>Next <i class="bi bi-arrow-right-circle"></i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
