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
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.seller.index') }}" wire:navigate>
                                <i class="bi bi-arrow-left btn-icon-prepend"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" wire:model="company_name" placeholder="Company name">
                                @error('company_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="gst_number">GST Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('gst_number') is-invalid @enderror" id="gst_number" wire:model="gst_number" placeholder="Enter Gst Number">
                                @error('gst_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="user_name">Seller Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('user_name') is-invalid @enderror" id="user_name" wire:model="user_name" placeholder="Seller name">
                                @error('user_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="Seller email">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model="password" placeholder="Min 8 characters (optional)">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone" wire:model="phone" placeholder="Enter Phone">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="about">About</label>
                                <textarea class="form-control @error('about') is-invalid @enderror" id="about" wire:model="about" rows="1" placeholder="About business"></textarea>
                                @error('about')<small class="text-danger d-block">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">Business Category</label>
                                <div class="row">
                                    @foreach ($business_categories ?? [] as $item)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" id="category_{{ $item->id }}"
                                                    wire:model="category" value="{{ $item->id }}">
                                                <label class="form-check-label" for="category_{{ $item->id }}">{{ $item->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('category')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">Business Type</label>
                                <div class="row">
                                    @foreach ($business_types ?? [] as $item)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" id="type_{{ $item->id }}"
                                                    wire:model="type" value="{{ $item->id }}">
                                                <label class="form-check-label" for="type_{{ $item->id }}">{{ $item->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">Seller Type</label>
                                <div class="row">
                                    @foreach ($seller_types ?? [] as $item)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input" id="seller_type_{{ $item->id }}"
                                                    wire:model="seller_type" value="{{ $item->id }}">
                                                <label class="form-check-label" for="seller_type_{{ $item->id }}">{{ $item->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('seller_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pan_number">PAN Number</label>
                                <input type="text" class="form-control" id="pan_number" wire:model="pan_number" placeholder="PAN number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="gst_type">GST Type</label>
                                <select class="form-select" id="gst_type" wire:model="gst_type">
                                    <option value="">Select GST type</option>
                                    @foreach ($gst_types ?? [] as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="address">Address</label>
                                <textarea class="form-control" id="address" wire:model="address" rows="1" placeholder="Full address"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="address_line_one">Address Line 1</label>
                                <input type="text" class="form-control" id="address_line_one" wire:model="address_line_one">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="address_line_two">Address Line 2</label>
                                <input type="text" class="form-control" id="address_line_two" wire:model="address_line_two">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pin_code">Pincode</label>
                                <input type="text" class="form-control" id="pin_code" wire:model="pin_code">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control" id="city" wire:model="city">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="state">State</label>
                                <input type="text" class="form-control" id="state" wire:model="state">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="country">Country</label>
                                <input type="text" class="form-control" id="country" wire:model="country">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="Save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
