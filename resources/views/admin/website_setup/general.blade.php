<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                </div>
                <form wire:submit.prevent="update()">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="phone1">Phone 1</label>
                                    <input type="text" class="form-control" id="phone1" wire:model="value.phone1" placeholder="Enter Phone 1">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="phone2">Phone 2</label>
                                    <input type="text" class="form-control" id="phone2" wire:model="value.phone2" placeholder="Enter Phone 2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="whatsapp">WhatsApp</label>
                                    <input type="text" class="form-control" id="whatsapp" wire:model="value.whatsapp" placeholder="Enter WhatsApp">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" wire:model="value.email" placeholder="Enter Email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="about">Short About</label>
                                    <textarea class="form-control" id="about" wire:model="value.short_about" placeholder="Enter About"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" wire:model="value.address" placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="facebook">Facebook</label>
                                    <input type="text" class="form-control" id="facebook" wire:model="value.facebook" placeholder="Enter Facebook URL">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="twitter">Twitter</label>
                                    <input type="text" class="form-control" id="twitter" wire:model="value.twitter" placeholder="Enter Twitter URL">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="instagram">Instagram</label>
                                    <input type="text" class="form-control" id="instagram" wire:model="value.instagram" placeholder="Enter Instagram URL">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="youtube">YouTube</label>
                                    <input type="text" class="form-control" id="youtube" wire:model="value.youtube" placeholder="Enter YouTube URL">
                                </div>
                            </div>
                        </div>

                        {{-- Statutory identity: footer, About, Terms and Privacy read these (4.2). --}}
                        <h5 class="mt-3 mb-1">Legal identity</h5>
                        <p class="text-muted small mb-3">Shown in the website footer and on the About, Terms and Privacy pages.</p>
                        <div class="row">
                            @foreach ([
                                'legal_name'    => ['Registered name', 'Biznie TradeTech LLP'],
                                'company_llpin' => ['LLPIN', 'AAB-1234'],
                                'company_gstin' => ['GSTIN', '09ABCDE1234F1Z5'],
                                'jurisdiction'  => ['Jurisdiction (courts)', 'Varanasi, Uttar Pradesh, India'],
                            ] as $key => [$label, $placeholder])
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="{{ $key }}">{{ $label }}</label>
                                        <input type="text" class="form-control @error('value.'.$key) is-invalid @enderror" id="{{ $key }}" wire:model="value.{{ $key }}" placeholder="{{ $placeholder }}">
                                        @error('value.'.$key) <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- One booking / cancellation rule for Terms, Return Policy and checkout (4.1). --}}
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="booking_policy">Booking &amp; cancellation rule</label>
                                    <textarea class="form-control @error('value.booking_policy') is-invalid @enderror" id="booking_policy" rows="2" wire:model="value.booking_policy" placeholder="e.g. The booking (token) amount is non-refundable..."></textarea>
                                    <small class="text-muted">Shown to buyers at checkout and in the Return Policy. Use <code>{{ '{' }}{{ '{' }}booking_policy{{ '}' }}{{ '}' }}</code> in Terms to print it there too.</small>
                                    @error('value.booking_policy') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- IT Rules 2021: a named grievance officer with a response time. --}}
                        <h5 class="mt-3 mb-1">Grievance Officer</h5>
                        <p class="text-muted small mb-3">Required for an intermediary under the IT Rules, 2021.</p>
                        <div class="row">
                            @foreach ([
                                'grievance_officer_name'  => ['Name', 'Full name'],
                                'grievance_officer_email' => ['Email', 'grievance@biznie.com'],
                                'grievance_officer_phone' => ['Phone', '10-digit mobile'],
                                'grievance_response_time' => ['Response time', 'within 48 hours'],
                            ] as $key => [$label, $placeholder])
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="{{ $key }}">{{ $label }}</label>
                                        <input type="text" class="form-control @error('value.'.$key) is-invalid @enderror" id="{{ $key }}" wire:model="value.{{ $key }}" placeholder="{{ $placeholder }}">
                                        @error('value.'.$key) <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <x-submit-btn text="Update Settings" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
