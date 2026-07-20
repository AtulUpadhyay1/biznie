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
            flex: 1 1 120px;
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

        .wz-repeater-row {
            border: 1px dashed #dee2e6;
            border-radius: .35rem;
            padding: .75rem;
            margin-bottom: .5rem;
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

        .wz-thumb {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: .35rem;
            border: 1px solid #dee2e6;
        }
    </style>

    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-8 card-title mb-0">
                            <h4 class="mb-1">Add Product Request</h4>
                            <small class="text-muted">
                                On behalf of
                                <b class="text-danger text-uppercase">{{ $seller->name }}</b>
                                @if ($seller->phone)
                                    <span class="ms-2"><i class="bi bi-telephone"></i> {{ $seller->phone }}</span>
                                @endif
                                @if ($seller->email)
                                    <span class="ms-2"><i class="bi bi-envelope-at"></i> {{ $seller->email }}</span>
                                @endif
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.seller.index') }}"
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
                        @for ($i = 1; $i <= 8; $i++)
                            <li class="wz-step {{ $step === $i ? 'is-active' : ($i < $step || $i <= $maxStep ? 'is-done' : '') }}"
                                @if ($i <= $maxStep && $i !== $step) wire:click="goToStep({{ $i }})" @endif>
                                <span class="wz-step-no">{{ $i }}</span>
                                <span>{{ $this->stepLabel($i) }}</span>
                            </li>
                        @endfor
                    </ul>

                    <div class="progress mb-4" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ ($step / 8) * 100 }}%"></div>
                    </div>

                    @error('step')
                        <div class="alert alert-danger border-0 shadow-sm">{{ $message }}</div>
                    @enderror

                    {{-- Step 1: Basic Information --}}
                    @if ($step === 1)
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                    wire:model="product_name" placeholder="Enter product name">
                                @error('product_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                    wire:model.live="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sub Category</label>
                                <select class="form-select @error('sub_category_id') is-invalid @enderror"
                                    wire:model="sub_category_id">
                                    <option value="">Select Sub Category</option>
                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('sub_category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Product Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_type') is-invalid @enderror"
                                    wire:model="product_type">
                                    <option value="raw_material">Raw Material</option>
                                    <option value="finished_good">Finished Good</option>
                                    <option value="service">Service</option>
                                </select>
                                @error('product_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">HSN Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('hsn_code') is-invalid @enderror"
                                    wire:model="hsn_code" placeholder="Enter HSN code">
                                @error('hsn_code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tax Rate (%)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('tax_rate') is-invalid @enderror" wire:model="tax_rate"
                                    placeholder="e.g. 18">
                                @error('tax_rate')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Short Description <span class="text-danger">*</span></label>
                                <input type="text" maxlength="200"
                                    class="form-control @error('short_description') is-invalid @enderror"
                                    wire:model="short_description" placeholder="Max 200 characters">
                                @error('short_description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Detailed Description</label>
                                <textarea rows="4" maxlength="1000" class="form-control @error('detailed_description') is-invalid @enderror"
                                    wire:model="detailed_description" placeholder="Max 1000 characters"></textarea>
                                @error('detailed_description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Media Upload --}}
                    @if ($step === 2)
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Product Images <span class="text-danger">*</span></label>
                                <input type="file" multiple accept="image/jpeg,image/jpg,image/png"
                                    class="form-control @error('product_images') is-invalid @enderror"
                                    wire:model="product_images">
                                <small class="text-muted">Up to 5 images. JPG / JPEG / PNG, max 5 MB each.</small>
                                @error('product_images')
                                    <small class="d-block text-danger">{{ $message }}</small>
                                @enderror
                                @error('product_images.*')
                                    <small class="d-block text-danger">{{ $message }}</small>
                                @enderror

                                <div wire:loading wire:target="product_images" class="text-danger mt-1">
                                    <small>Uploading...</small>
                                </div>

                                @if (!empty($product_images))
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach ($product_images as $image)
                                            @if (method_exists($image, 'temporaryUrl'))
                                                <img src="{{ $image->temporaryUrl() }}" class="wz-thumb">
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @if (!empty($existing_images))
                                    <div class="mt-3">
                                        <span class="wz-kv-label">Already uploaded</span>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @foreach ($existing_images as $url)
                                                <img src="{{ $url }}" class="wz-thumb">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Product Video URL</label>
                                <input type="text" class="form-control @error('product_video') is-invalid @enderror"
                                    wire:model="product_video" placeholder="https://...">
                                @error('product_video')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Quality & Brand --}}
                    @if ($step === 3)
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Quality <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('quality') is-invalid @enderror"
                                    wire:model="quality" placeholder="e.g. Grade A">
                                @error('quality')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Quality Charge</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('quality_charge') is-invalid @enderror"
                                    wire:model="quality_charge">
                                @error('quality_charge')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Quality Description</label>
                                <input type="text" maxlength="300"
                                    class="form-control @error('quality_description') is-invalid @enderror"
                                    wire:model="quality_description">
                                @error('quality_description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                    wire:model="brand">
                                @error('brand')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Make</label>
                                <input type="text" class="form-control @error('make') is-invalid @enderror"
                                    wire:model="make">
                                @error('make')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label d-block">Packaging</label>
                                @foreach ($packaging as $index => $row)
                                    <div class="wz-repeater-row">
                                        <div class="row align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label">Packaging Type</label>
                                                <input type="text" class="form-control"
                                                    wire:model="packaging.{{ $index }}.type">
                                                @error('packaging.' . $index . '.type')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Charge</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    wire:model="packaging.{{ $index }}.charge">
                                                @error('packaging.' . $index . '.charge')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-2 text-end">
                                                @if (count($packaging) > 1)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removePackaging({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <x-add-btn text="Add Packaging" function="addPackaging" />
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 4: Specifications --}}
                    @if ($step === 4)
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">Physical Specifications</label>
                                @foreach ($physical_specs as $index => $row)
                                    <div class="wz-repeater-row">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Parameter</label>
                                                <input type="text" class="form-control"
                                                    wire:model="physical_specs.{{ $index }}.parameter">
                                                @error('physical_specs.' . $index . '.parameter')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Value</label>
                                                <input type="text" class="form-control"
                                                    wire:model="physical_specs.{{ $index }}.value">
                                                @error('physical_specs.' . $index . '.value')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" accept="image/jpeg,image/jpg,image/png"
                                                    class="form-control"
                                                    wire:model="physical_spec_images.{{ $index }}">
                                                @error('physical_spec_images.' . $index)
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-1 text-end">
                                                @if (count($physical_specs) > 1)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removePhysicalSpec({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <x-add-btn text="Add Physical Spec" function="addPhysicalSpec" />
                                <div class="clearfix"></div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">Chemical Specifications</label>
                                @foreach ($chemical_specs as $index => $row)
                                    <div class="wz-repeater-row">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Parameter</label>
                                                <input type="text" class="form-control"
                                                    wire:model="chemical_specs.{{ $index }}.parameter">
                                                @error('chemical_specs.' . $index . '.parameter')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Value</label>
                                                <input type="text" class="form-control"
                                                    wire:model="chemical_specs.{{ $index }}.value">
                                                @error('chemical_specs.' . $index . '.value')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" accept="image/jpeg,image/jpg,image/png"
                                                    class="form-control"
                                                    wire:model="chemical_spec_images.{{ $index }}">
                                                @error('chemical_spec_images.' . $index)
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-1 text-end">
                                                @if (count($chemical_specs) > 1)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removeChemicalSpec({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <x-add-btn text="Add Chemical Spec" function="addChemicalSpec" />
                                <div class="clearfix"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Weight Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('weight_unit') is-invalid @enderror"
                                    wire:model="weight_unit" placeholder="e.g. KG">
                                @error('weight_unit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Net Weight <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('net_weight') is-invalid @enderror"
                                    wire:model="net_weight">
                                @error('net_weight')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tolerance</label>
                                <input type="text" class="form-control @error('tolerance') is-invalid @enderror"
                                    wire:model="tolerance">
                                @error('tolerance')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 5: Size Variants & Pricing --}}
                    @if ($step === 5)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label d-block">Size Variants <span
                                        class="text-danger">*</span></label>
                                @error('variants')
                                    <small class="d-block text-danger mb-2">{{ $message }}</small>
                                @enderror
                                @foreach ($variants as $index => $row)
                                    <div class="wz-repeater-row">
                                        <div class="row align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label">Size <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    wire:model="variants.{{ $index }}.size">
                                                @error('variants.' . $index . '.size')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    wire:model="variants.{{ $index }}.unit">
                                                @error('variants.' . $index . '.unit')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Charge</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    wire:model="variants.{{ $index }}.charge">
                                                @error('variants.' . $index . '.charge')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Stock</label>
                                                <input type="text" class="form-control"
                                                    wire:model="variants.{{ $index }}.stock">
                                                @error('variants.' . $index . '.stock')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-1 text-end">
                                                @if (count($variants) > 1)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removeVariant({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <x-add-btn text="Add Variant" function="addVariant" />
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 6: Loading & MOQ --}}
                    @if ($step === 6)
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loading City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('loading_city') is-invalid @enderror"
                                    wire:model="loading_city">
                                @error('loading_city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loading State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('loading_state') is-invalid @enderror"
                                    wire:model="loading_state">
                                @error('loading_state')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    wire:model="country">
                                @error('country')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Order Quantity <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('moq') is-invalid @enderror"
                                    wire:model="moq">
                                @error('moq')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">MOQ Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('moq_unit') is-invalid @enderror"
                                    wire:model="moq_unit" placeholder="e.g. MT">
                                @error('moq_unit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 7: Charges & Status --}}
                    @if ($step === 7)
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Publish On</label>
                                <input type="date" class="form-control @error('publish_on') is-invalid @enderror"
                                    wire:model="publish_on">
                                @error('publish_on')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="text-muted">Listing status is decided when the request is approved.</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label d-block">Additional Charges</label>
                                @foreach ($charges as $index => $row)
                                    <div class="wz-repeater-row">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Charge Type</label>
                                                <input type="text" class="form-control"
                                                    wire:model="charges.{{ $index }}.type"
                                                    placeholder="GST / Loading / Insurance">
                                                @error('charges.' . $index . '.type')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Percent</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    wire:model="charges.{{ $index }}.percent">
                                                @error('charges.' . $index . '.percent')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Amount</label>
                                                <input type="number" step="0.01" min="0" class="form-control"
                                                    wire:model="charges.{{ $index }}.amount">
                                                @error('charges.' . $index . '.amount')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-2 text-end">
                                                @if (count($charges) > 1)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removeCharge({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <x-add-btn text="Add Charge" function="addCharge" />
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 8: Review & Submit --}}
                    @if ($step === 8)
                        <div class="row">
                            @php
                                $reviewRows = [
                                    'Product Name' => $product_name,
                                    'Category' => optional($categories->firstWhere('id', (int) $category_id))->name,
                                    'Sub Category' => optional($subCategories->firstWhere('id', (int) $sub_category_id))->name,
                                    'Product Type' => ucwords(str_replace('_', ' ', (string) $product_type)),
                                    'HSN Code' => $hsn_code,
                                    'Tax Rate' => $tax_rate !== '' ? $tax_rate . '%' : null,
                                    'Quality' => $quality,
                                    'Brand' => $brand,
                                    'Make' => $make,
                                    'Weight' => trim($net_weight . ' ' . $weight_unit),
                                    'Tolerance' => $tolerance,
                                    'Loading City' => $loading_city,
                                    'Loading State' => $loading_state,
                                    'Country' => $country,
                                    'MOQ' => trim($moq . ' ' . $moq_unit),
                                    'Publish On' => $publish_on,
                                ];
                            @endphp
                            @foreach ($reviewRows as $label => $value)
                                <div class="col-md-3 mb-3">
                                    <div class="wz-kv">
                                        <div class="wz-kv-label">{{ $label }}</div>
                                        <div class="wz-kv-value">{{ $value ?: '--' }}</div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-12 mb-3">
                                <div class="wz-kv">
                                    <div class="wz-kv-label">Short Description</div>
                                    <div class="wz-kv-value">{{ $short_description ?: '--' }}</div>
                                </div>
                            </div>

                            @if (!empty($existing_images))
                                <div class="col-md-12 mb-3">
                                    <span class="wz-kv-label">Product Images</span>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @foreach ($existing_images as $url)
                                            <img src="{{ $url }}" class="wz-thumb">
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Size</th>
                                                <th>Unit</th>
                                                <th>Charge</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($variants as $row)
                                                @if (!empty($row['size']) || !empty($row['unit']))
                                                    <tr>
                                                        <td>{{ $row['size'] ?: '--' }}</td>
                                                        <td>{{ $row['unit'] ?: '--' }}</td>
                                                        <td>{{ $row['charge'] !== '' ? $row['charge'] : '--' }}</td>
                                                        <td>{{ $row['stock'] !== '' ? $row['stock'] : '--' }}</td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-danger">No variants added
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="alert alert-warning border-0 shadow-sm mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    On submit this request moves to <b>Pending Review</b> under
                                    <b>{{ $seller->name }}</b> and stays hidden from the live catalog until approved.
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
                            @if ($step <= 7)
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
