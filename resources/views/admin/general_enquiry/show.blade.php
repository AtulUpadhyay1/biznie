<div>
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <h4>{{ $page_title }}</h4>
                        <span
                            class="bz-status {{ $data->status == 'cancel' ? 'bz-status--danger' : ($data->status == 'pending' ? 'bz-status--warning' : 'bz-status--success') }} ms-3">{{ ucfirst($data->status) }}</span>
                    </div>

                    <div class="bz-toolbar">
                        <a href="{{ route('admin.general-enquiry.index') }}" class="btn btn-secondary btn-sm"
                            wire:navigate><i class="bi bi-arrow-left"></i>Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Name</dt>
                                    <dd>{{ $data->name ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Company Name</dt>
                                    <dd>{{ $data->company_name ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Contact Number</dt>
                                    <dd>{{ $data->contact_number ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Email</dt>
                                    <dd>{{ $data->email ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>GST Number</dt>
                                    <dd>{{ $data->gst_number ?? '--' }}</dd>
                                </div>
                            </dl>

                        </div>
                        <div class="col-md-6">
                            <dl class="bz-kv-list">
                                <div>
                                    <dt>Product</dt>
                                    <dd>{{ $data->getSellerCommodityProduct?->name ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Brand</dt>
                                    <dd>{{ $data->getBrand?->name ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Delivery Location</dt>
                                    <dd>{{ $data->delivery_location ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Requirement</dt>
                                    <dd>{{ $data->requirement ?? '--' }}</dd>
                                </div>

                                <div>
                                    <dt>Message</dt>
                                    <dd>{{ $data->message ?? '--' }}</dd>
                                </div>
                            </dl>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
