<div>
    <style>
        .wz-steps {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .wz-step {
            flex: 1 1 140px;
            border: 1px solid #e9ecef;
            border-radius: .35rem;
            padding: .5rem .65rem;
            background: #fff;
            cursor: default;
            font-size: .78rem;
            line-height: 1.2;
        }

        .wz-step.is-done {
            border-color: #1bcfb4;
            background: #f2fbf9;
            cursor: pointer;
        }

        .wz-step.is-active {
            border-color: #fd7070;
            background: #fff5f5;
            box-shadow: 0 0 0 1px #fd7070 inset;
        }

        .wz-step-no {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            border-radius: 50%;
            background: #e9ecef;
            font-weight: 600;
            margin-right: .35rem;
        }

        .wz-step.is-done .wz-step-no {
            background: #1bcfb4;
            color: #fff;
        }

        .wz-step.is-active .wz-step-no {
            background: #fd7070;
            color: #fff;
        }

        .wz-kv {
            border: 1px solid #dee2e6;
            border-radius: .35rem;
            padding: .5rem .65rem;
            height: 100%;
        }

        .wz-kv-label {
            font-size: .72rem;
            text-transform: uppercase;
            color: #7987a1;
            letter-spacing: .3px;
        }

        .wz-kv-value {
            font-weight: 600;
            word-break: break-word;
        }
    </style>

    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-8 card-title mb-0">
                            <h4 class="mb-1">Add Seller Request</h4>
                            <small class="text-muted">
                                On behalf of
                                <b class="text-danger text-uppercase">{{ $buyer->name }}</b>
                                @if ($buyer->phone)
                                    <span class="ms-2"><i class="bi bi-telephone"></i> {{ $buyer->phone }}</span>
                                @endif
                                @if ($buyer->email)
                                    <span class="ms-2"><i class="bi bi-envelope-at"></i> {{ $buyer->email }}</span>
                                @endif
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.customer-list') }}"
                                class="btn btn-danger btn-sm btn-icon-text" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Stepper --}}
                    <ul class="wz-steps mb-4">
                        @for ($i = 1; $i <= 6; $i++)
                            <li class="wz-step {{ $step === $i ? 'is-active' : ($i <= $maxStep ? 'is-done' : '') }}"
                                @if ($i <= $maxStep && $i !== $step) wire:click="goToStep({{ $i }})" @endif>
                                <span class="wz-step-no">{{ $i }}</span>
                                <span>{{ $this->stepLabel($i) }}</span>
                            </li>
                        @endfor
                    </ul>

                    <div class="progress mb-4" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ ($step / 6) * 100 }}%"></div>
                    </div>

                    @error('step')
                        <div class="alert alert-danger border-0 shadow-sm">{{ $message }}</div>
                    @enderror

                    {{-- Step 1: Business Information --}}
                    @if ($step === 1)
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Business Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                    wire:model="company_name">
                                @error('company_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">GST Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('gst_number') is-invalid @enderror"
                                    wire:model="gst_number">
                                @error('gst_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase @error('pan_number') is-invalid @enderror"
                                    wire:model="pan_number" placeholder="ABCDE1234F">
                                @error('pan_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('seller_type_id') is-invalid @enderror"
                                    wire:model="seller_type_id">
                                    <option value="">Select business type</option>
                                    @foreach ($sellerTypes as $sellerType)
                                        <option value="{{ $sellerType->id }}">{{ $sellerType->name }}</option>
                                    @endforeach
                                </select>
                                @error('seller_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Constitution Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('constitution_type') is-invalid @enderror"
                                    wire:model="constitution_type">
                                    <option value="">Select constitution type</option>
                                    <option value="proprietorship">Proprietorship</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="llp">LLP</option>
                                    <option value="private_limited">Private Limited</option>
                                    <option value="public_limited">Public Limited</option>
                                </select>
                                @error('constitution_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Years in Business <span class="text-danger">*</span></label>
                                <input type="number" min="0" max="100"
                                    class="form-control @error('years_in_business') is-invalid @enderror"
                                    wire:model="years_in_business">
                                @error('years_in_business')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Company Address <span class="text-danger">*</span></label>
                                <textarea rows="3" maxlength="500" class="form-control @error('company_address') is-invalid @enderror"
                                    wire:model="company_address"></textarea>
                                @error('company_address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    wire:model="city">
                                @error('city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                    wire:model="state">
                                @error('state')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    wire:model="country">
                                @error('country')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                <input type="text" maxlength="6"
                                    class="form-control @error('pincode') is-invalid @enderror" wire:model="pincode">
                                @error('pincode')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Contact Person --}}
                    @if ($step === 2)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                    wire:model="contact_person">
                                @error('contact_person')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                    wire:model="designation">
                                @error('designation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" maxlength="10"
                                    class="form-control @error('mobile') is-invalid @enderror" wire:model="mobile">
                                @error('mobile')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Alternate Mobile</label>
                                <input type="text" maxlength="10"
                                    class="form-control @error('alternate_mobile') is-invalid @enderror"
                                    wire:model="alternate_mobile">
                                @error('alternate_mobile')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Business Documents --}}
                    @if ($step === 3)
                        <div class="row">
                            @php
                                $documentFields = [
                                    'gst_certificate' => 'GST Certificate',
                                    'pan_document' => 'PAN Document',
                                    'registration_certificate' => 'Registration Certificate',
                                    'address_proof' => 'Address Proof',
                                    'cancelled_cheque' => 'Cancelled Cheque',
                                    'other_documents' => 'Other Documents',
                                ];
                            @endphp
                            @foreach ($documentFields as $field => $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <input type="file" accept=".pdf,.jpg,.jpeg,.png"
                                        class="form-control @error($field) is-invalid @enderror"
                                        wire:model="{{ $field }}">
                                    @error($field)
                                        <small class="d-block text-danger">{{ $message }}</small>
                                    @enderror
                                    <div wire:loading wire:target="{{ $field }}" class="text-danger">
                                        <small>Uploading...</small>
                                    </div>
                                    @if (!empty($storedDocuments[$field]))
                                        <a href="{{ $storedDocuments[$field] }}" target="_blank"
                                            class="btn btn-outline-success btn-sm mt-1">
                                            <i class="bi bi-file-earmark-check me-1"></i> View uploaded
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                            <div class="col-md-12">
                                <small class="text-muted">PDF / JPG / JPEG / PNG, max 5 MB each. Documents are
                                    optional at this stage and can be added before approval.</small>
                            </div>
                        </div>
                    @endif

                    {{-- Step 4: Bank Details --}}
                    @if ($step === 4)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('account_holder_name') is-invalid @enderror"
                                    wire:model="account_holder_name">
                                @error('account_holder_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    wire:model="bank_name">
                                @error('bank_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                    wire:model="account_number">
                                @error('account_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control text-uppercase @error('ifsc_code') is-invalid @enderror"
                                    wire:model="ifsc_code" placeholder="ABCD0123456">
                                @error('ifsc_code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('account_type') is-invalid @enderror"
                                    wire:model="account_type">
                                    <option value="">Select account type</option>
                                    <option value="savings">Savings</option>
                                    <option value="current">Current</option>
                                </select>
                                @error('account_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('branch') is-invalid @enderror"
                                    wire:model="branch">
                                @error('branch')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 5: Store & Products --}}
                    @if ($step === 5)
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                    wire:model="category" placeholder="e.g. Metals, Polymers">
                                @error('category')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Annual Turnover <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('turnover') is-invalid @enderror"
                                    wire:model="turnover">
                                @error('turnover')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Product Upload Mode <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_upload_mode') is-invalid @enderror"
                                    wire:model="product_upload_mode">
                                    <option value="existing">Existing Products</option>
                                    <option value="new">New Product</option>
                                    <option value="bulk">Bulk Upload</option>
                                </select>
                                @error('product_upload_mode')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Products <span class="text-danger">*</span></label>
                                <textarea rows="5" maxlength="4000" class="form-control @error('products') is-invalid @enderror"
                                    wire:model="products" placeholder="List the products this seller wants to sell"></textarea>
                                @error('products')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 6: Review & Submit --}}
                    @if ($step === 6)
                        <div class="row">
                            @php
                                $reviewRows = [
                                    'Company Name' => $company_name,
                                    'GST Number' => $gst_number,
                                    'PAN Number' => $pan_number,
                                    'Business Type' => optional($sellerTypes->firstWhere('id', (int) $seller_type_id))->name,
                                    'Constitution Type' => $constitution_type,
                                    'Years In Business' => $years_in_business,
                                    'City' => $city,
                                    'State' => $state,
                                    'Country' => $country,
                                    'Pincode' => $pincode,
                                    'Contact Person' => $contact_person,
                                    'Designation' => $designation,
                                    'Mobile' => $mobile,
                                    'Alternate Mobile' => $alternate_mobile,
                                    'Email' => $email,
                                    'Account Holder' => $account_holder_name,
                                    'Bank Name' => $bank_name,
                                    'Account Number' => $account_number,
                                    'IFSC Code' => $ifsc_code,
                                    'Account Type' => $account_type,
                                    'Branch' => $branch,
                                    'Category' => $category,
                                    'Turnover' => $turnover,
                                    'Upload Mode' => ucfirst((string) $product_upload_mode),
                                ];
                            @endphp
                            @foreach ($reviewRows as $label => $value)
                                <div class="col-md-3 mb-3">
                                    <div class="wz-kv">
                                        <div class="wz-kv-label">{{ $label }}</div>
                                        <div class="wz-kv-value">{{ $value !== '' && $value !== null ? $value : '--' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-12 mb-3">
                                <div class="wz-kv">
                                    <div class="wz-kv-label">Company Address</div>
                                    <div class="wz-kv-value">{{ $company_address ?: '--' }}</div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="wz-kv">
                                    <div class="wz-kv-label">Products</div>
                                    <div class="wz-kv-value">{{ $products ?: '--' }}</div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <span class="wz-kv-label">Documents</span>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @forelse ($storedDocuments as $field => $url)
                                        <a href="{{ $url }}" target="_blank"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-file-earmark-check me-1"></i>
                                            {{ ucwords(str_replace('_', ' ', $field)) }}
                                        </a>
                                    @empty
                                        <span class="text-muted">No documents uploaded</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="alert alert-warning border-0 shadow-sm mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    On submit this request moves to <b>Pending Review</b> under
                                    <b>{{ $buyer->name }}</b>. The buyer becomes a seller only once the request is
                                    approved from the Seller Request queue.
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Wizard footer --}}
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div>
                            @if ($step > 1)
                                <button type="button" class="btn btn-secondary btn-sm btn-icon-text"
                                    wire:click="previousStep">
                                    <i class="bi bi-arrow-left btn-icon-prepend"></i>
                                    Previous
                                </button>
                            @endif
                        </div>
                        <div>
                            @if ($step <= 5)
                                <button type="button" class="btn btn-success btn-sm btn-icon-text"
                                    wire:click="nextStep" wire:loading.attr="disabled">
                                    Save &amp; Next
                                    <i class="bi bi-arrow-right btn-icon-append"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm btn-icon-text" wire:click="submit"
                                    wire:loading.attr="disabled">
                                    <i class="bi bi-send btn-icon-prepend"></i>
                                    Submit Request
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-loader />
</div>
