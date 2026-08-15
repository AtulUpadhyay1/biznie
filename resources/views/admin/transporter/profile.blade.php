<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.transporter.transporter_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Profile</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6 class="bz-section-label">Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Name:</b><span class="ms-2">{{ $data->name }}</span></td>
                                        <td><b>Mobile Number:</b><span class="ms-2">{{ $data->phone }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Email:</b><span class="ms-2">{{ $data->email }}</span></td>
                                        <td><b>Company Name:</b><span class="ms-2">{{ $data->getTransporterDetail ? $data->getTransporterDetail->company_name : '--' }} (<b>GST No.:</b> {{ $data->getTransporterDetail ? $data->getTransporterDetail->gst_number : '--' }})</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Alternate Phone:</b><span class="ms-2">{{ $data->getTransporterDetail ? $data->getTransporterDetail->alternate_phone : '--' }}</span></td>
                                        <td><b>Aadhar Number:</b><span class="ms-2">{{ $data->getTransporterDetail ? $data->getTransporterDetail->aadhar_number : '--' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="bz-section-label">Primary Address</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Address:</b><span class="ms-2">{{ $data->getTransporterDetail ? $data->getTransporterDetail->address : '--' }}</span></td>
                                    </tr>
                                    {{-- <tr>
                                        <td><b>Address Line 1:</b><span class="ms-2">Shri sai baba mandir, behind vinayaka hospital, bhelupur road</span></td>
                                        <td><b>Address Line 2:</b><span class="ms-2">gurudham colony, varanasi, uttarpradesh</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Landmark:</b><span class="ms-2">Vinayaka Hospital</span></td>
                                        <td><b>Pincode:</b><span class="ms-2">221010</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>City:</b><span class="ms-2">Varanasi</span></td>
                                        <td><b>State:</b><span class="ms-2">Uttar Pradesh</span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Country:</b><span class="ms-2">India</span></td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
