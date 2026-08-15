<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}

    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4>Add Product Request</h4>
                        <p class="bz-card-sub">
                            On behalf of
                            <b class="text-danger text-uppercase">{{ $seller->name }}</b>
                            @if ($seller->phone)
                                <span class="ms-2"><i class="bi bi-telephone"></i> {{ $seller->phone }}</span>
                            @endif
                            @if ($seller->email)
                                <span class="ms-2"><i class="bi bi-envelope-at"></i> {{ $seller->email }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="bz-toolbar">
                        <a href="{{ route('admin.seller.index') }}" class="btn btn-secondary btn-sm" wire:navigate>
                            <i class="bi bi-x-lg"></i>
                            Cancel
                        </a>
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
                                <label class="form-label" for="product_name">Product Name <span class="text-danger">*</span></label>
                                <input id="product_name" type="text" class="form-control @error('product_name') is-invalid @enderror"
                                    wire:model="product_name" placeholder="Enter product name">
                                @error('product_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                                <select id="category_id" class="form-select @error('category_id') is-invalid @enderror"
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
                                <label class="form-label" for="sub_category_id">Sub Category</label>
                                <select id="sub_category_id" class="form-select @error('sub_category_id') is-invalid @enderror"
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
                                <label class="form-label" for="product_type">Product Type <span class="text-danger">*</span></label>
                                <select id="product_type" class="form-select @error('product_type') is-invalid @enderror"
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
                                <label class="form-label" for="hsn_code">HSN Code <span class="text-danger">*</span></label>
                                <input id="hsn_code" type="text" class="form-control @error('hsn_code') is-invalid @enderror"
                                    wire:model="hsn_code" placeholder="Enter HSN code">
                                @error('hsn_code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tax_rate">Tax Rate (%)</label>
                                <input id="tax_rate" type="number" step="0.01" min="0"
                                    class="form-control @error('tax_rate') is-invalid @enderror" wire:model="tax_rate"
                                    placeholder="e.g. 18">
                                @error('tax_rate')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="short_description">Short Description <span class="text-danger">*</span></label>
                                <input id="short_description" type="text" maxlength="200"
                                    class="form-control @error('short_description') is-invalid @enderror"
                                    wire:model="short_description" placeholder="Max 200 characters">
                                @error('short_description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="detailed_description">Detailed Description</label>
                                <textarea id="detailed_description" rows="4" maxlength="1000" class="form-control @error('detailed_description') is-invalid @enderror"
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
                                <label class="form-label" for="product_images">Product Images <span class="text-danger">*</span></label>
                                <input id="product_images" type="file" multiple accept="image/jpeg,image/jpg,image/png"
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
                                <label class="form-label" for="product_video">Product Video URL</label>
                                <input id="product_video" type="text" class="form-control @error('product_video') is-invalid @enderror"
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
                                <label class="form-label" for="quality">Quality <span class="text-danger">*</span></label>
                                <input id="quality" type="text" class="form-control @error('quality') is-invalid @enderror"
                                    wire:model="quality" placeholder="e.g. Grade A">
                                @error('quality')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="quality_charge">Quality Charge</label>
                                <input id="quality_charge" type="number" step="0.01" min="0"
                                    class="form-control @error('quality_charge') is-invalid @enderror"
                                    wire:model="quality_charge">
                                @error('quality_charge')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="quality_description">Quality Description</label>
                                <input id="quality_description" type="text" maxlength="300"
                                    class="form-control @error('quality_description') is-invalid @enderror"
                                    wire:model="quality_description">
                                @error('quality_description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="brand">Brand</label>
                                <input id="brand" type="text" class="form-control @error('brand') is-invalid @enderror"
                                    wire:model="brand">
                                @error('brand')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="make">Make</label>
                                <input id="make" type="text" class="form-control @error('make') is-invalid @enderror"
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
                                                <label class="form-label" for="packaging_{{ $index }}_type">Packaging Type</label>
                                                <input id="packaging_{{ $index }}_type" type="text" class="form-control"
                                                    wire:model="packaging.{{ $index }}.type">
                                                @error('packaging.' . $index . '.type')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label" for="packaging_{{ $index }}_charge">Charge</label>
                                                <input id="packaging_{{ $index }}_charge" type="number" step="0.01" min="0" class="form-control"
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
                                                <label class="form-label" for="physical_specs_{{ $index }}_parameter">Parameter</label>
                                                <input id="physical_specs_{{ $index }}_parameter" type="text" class="form-control"
                                                    wire:model="physical_specs.{{ $index }}.parameter">
                                                @error('physical_specs.' . $index . '.parameter')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" for="physical_specs_{{ $index }}_value">Value</label>
                                                <input id="physical_specs_{{ $index }}_value" type="text" class="form-control"
                                                    wire:model="physical_specs.{{ $index }}.value">
                                                @error('physical_specs.' . $index . '.value')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="physical_spec_images_{{ $index }}">Image</label>
                                                <input id="physical_spec_images_{{ $index }}" type="file" accept="image/jpeg,image/jpg,image/png"
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
                                                <label class="form-label" for="chemical_specs_{{ $index }}_parameter">Parameter</label>
                                                <input id="chemical_specs_{{ $index }}_parameter" type="text" class="form-control"
                                                    wire:model="chemical_specs.{{ $index }}.parameter">
                                                @error('chemical_specs.' . $index . '.parameter')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" for="chemical_specs_{{ $index }}_value">Value</label>
                                                <input id="chemical_specs_{{ $index }}_value" type="text" class="form-control"
                                                    wire:model="chemical_specs.{{ $index }}.value">
                                                @error('chemical_specs.' . $index . '.value')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="chemical_spec_images_{{ $index }}">Image</label>
                                                <input id="chemical_spec_images_{{ $index }}" type="file" accept="image/jpeg,image/jpg,image/png"
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
                                <label class="form-label" for="weight_unit">Weight Unit <span class="text-danger">*</span></label>
                                <input id="weight_unit" type="text" class="form-control @error('weight_unit') is-invalid @enderror"
                                    wire:model="weight_unit" placeholder="e.g. KG">
                                @error('weight_unit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="net_weight">Net Weight <span class="text-danger">*</span></label>
                                <input id="net_weight" type="text" class="form-control @error('net_weight') is-invalid @enderror"
                                    wire:model="net_weight">
                                @error('net_weight')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tolerance">Tolerance</label>
                                <input id="tolerance" type="text" class="form-control @error('tolerance') is-invalid @enderror"
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
                                                <label class="form-label" for="variants_{{ $index }}_size">Size <span class="text-danger">*</span></label>
                                                <input id="variants_{{ $index }}_size" type="text" class="form-control"
                                                    wire:model="variants.{{ $index }}.size">
                                                @error('variants.' . $index . '.size')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="variants_{{ $index }}_unit">Unit <span class="text-danger">*</span></label>
                                                <input id="variants_{{ $index }}_unit" type="text" class="form-control"
                                                    wire:model="variants.{{ $index }}.unit">
                                                @error('variants.' . $index . '.unit')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label" for="variants_{{ $index }}_charge">Charge</label>
                                                <input id="variants_{{ $index }}_charge" type="number" step="0.01" min="0" class="form-control"
                                                    wire:model="variants.{{ $index }}.charge">
                                                @error('variants.' . $index . '.charge')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="variants_{{ $index }}_stock">Stock</label>
                                                <input id="variants_{{ $index }}_stock" type="text" class="form-control"
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
                                <label class="form-label" for="loading_city">Loading City <span class="text-danger">*</span></label>
                                <input id="loading_city" type="text" class="form-control @error('loading_city') is-invalid @enderror"
                                    wire:model="loading_city">
                                @error('loading_city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="loading_state">Loading State <span class="text-danger">*</span></label>
                                <input id="loading_state" type="text" class="form-control @error('loading_state') is-invalid @enderror"
                                    wire:model="loading_state">
                                @error('loading_state')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="country">Country <span class="text-danger">*</span></label>
                                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror"
                                    wire:model="country">
                                @error('country')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="moq">Minimum Order Quantity <span
                                        class="text-danger">*</span></label>
                                <input id="moq" type="text" class="form-control @error('moq') is-invalid @enderror"
                                    wire:model="moq">
                                @error('moq')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="moq_unit">MOQ Unit <span class="text-danger">*</span></label>
                                <input id="moq_unit" type="text" class="form-control @error('moq_unit') is-invalid @enderror"
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
                                <label class="form-label" for="publish_on">Publish On</label>
                                <input id="publish_on" type="date" class="form-control @error('publish_on') is-invalid @enderror"
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
                                                <label class="form-label" for="charges_{{ $index }}_type">Charge Type</label>
                                                <input id="charges_{{ $index }}_type" type="text" class="form-control"
                                                    wire:model="charges.{{ $index }}.type"
                                                    placeholder="GST / Loading / Insurance">
                                                @error('charges.' . $index . '.type')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="charges_{{ $index }}_percent">Percent</label>
                                                <input id="charges_{{ $index }}_percent" type="number" step="0.01" min="0" class="form-control"
                                                    wire:model="charges.{{ $index }}.percent">
                                                @error('charges.' . $index . '.percent')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="charges_{{ $index }}_amount">Amount</label>
                                                <input id="charges_{{ $index }}_amount" type="number" step="0.01" min="0" class="form-control"
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
                                                <x-table-no-data colspan="4" title="No variants added"
                                                    text="Go back to step 5 and add at least one size variant." />
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
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="previousStep">
                                    <i class="bi bi-arrow-left"></i>
                                    Previous
                                </button>
                            @endif
                        </div>
                        <div>
                            @if ($step <= 7)
                                <button type="button" class="btn btn-danger btn-sm" wire:click="nextStep"
                                    wire:loading.attr="disabled">
                                    Save &amp; Next
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-danger btn-sm" wire:click="submit"
                                    wire:loading.attr="disabled">
                                    <i class="bi bi-send"></i>
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
