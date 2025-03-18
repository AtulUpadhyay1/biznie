<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title d-flex align-items-center">
                            <h4> {{ $page_title }}
                            </h4>
                            <span class="badge bg-info ms-3">{{ $data->status }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('admin.general-enquiry.index')}}" class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $data->name }}</p>
                            <p><strong>Company Name:</strong> {{ $data->company_name }}</p>
                            <p><strong>Contact Number:</strong> {{ $data->contact_number }}</p>
                            <p><strong>Email:</strong> {{ $data->email }}</p>
                            <p><strong>GST Number:</strong> {{ $data->gst_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Product:</strong> {{ $data->getSellerCommodityProduct?->name }}</p>
                            <p><strong>Brand:</strong> {{ $data->getBrand?->name }}</p>
                            <p><strong>Delivery Location:</strong> {{ $data->delivery_location }}</p>
                            <p><strong>Requirement:</strong> {{ $data->requirement }}</p>
                            <p><strong>Message:</strong> {{ $data->message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
