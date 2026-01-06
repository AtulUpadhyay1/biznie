<div>
    <style>
        .table-sm>:not(caption)>*>* {
            padding: 0.25rem .55rem;
        }
    </style>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title d-flex align-items-center">
                            <h4> {{ $page_title }}
                            </h4>
                            <span
                                class="badge {{ $data->status == 'cancel' ? 'bg-danger' : ($data->status == 'pending' ? 'bg-warning' : 'bg-primary') }} ms-3">{{ $data->status }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{ route('admin.general-enquiry.index') }}"
                                class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" wire:navigate><i
                                    class="bi bi-arrow-left btn-icon-prepend"></i>Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $data->name ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Company Name</th>
                                        <td>{{ $data->company_name ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Contact Number</th>
                                        <td>{{ $data->contact_number ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $data->email ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>GST Number</th>
                                        <td>{{ $data->gst_number ?? '--' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Product</th>
                                        <td>{{ $data->getSellerCommodityProduct?->name ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Brand</th>
                                        <td>{{ $data->getBrand?->name ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Delivery Location</th>
                                        <td>{{ $data->delivery_location ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Requirement</th>
                                        <td>{{ $data->requirement ?? '--' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Message</th>
                                        <td>{{ $data->message ?? '--' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
