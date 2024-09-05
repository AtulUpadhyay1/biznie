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
                                href="{{ route('admin.transporter.index') }}" wire:navigate>
                                <i class="bi bi-arrow-left btn-icon-prepend"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ isset($hidden_id) ? 'update()' : 'save()' }}">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Enter Name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone" wire:model="phone" placeholder="Enter Phone">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="alternate_phone">Alternat Phone</label>
                                <input type="number" class="form-control @error('alternate_phone') is-invalid @enderror" id="alternate_phone" wire:model="alternate_phone" placeholder="Enter Alternate Phone">
                                @error('alternate_phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" wire:model="company_name" placeholder="Enter Company Name">
                                @error('company_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="gst_number">GST Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('gst_number') is-invalid @enderror" id="gst_number" wire:model="gst_number" placeholder="Enter Gst Number">
                                @error('gst_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="aadhar_number">Aadhar Number</label>
                                <input type="number" class="form-control @error('aadhar_number') is-invalid @enderror" id="aadhar_number" wire:model="aadhar_number" placeholder="Enter Aadhar Number">
                                @error('aadhar_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="address">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model="address" placeholder="Enter Address"></textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{ isset($hidden_id) ? 'Update' : 'Save' }}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
