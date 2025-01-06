<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.customer_list.customer_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 card-title">
                            <h5 class="mt-2">Buyer Profile</h5>
                        </div>
                        <div class="col-8">
                            <div class="d-flex align-items-center justify-content-end flex-wrap text-nowrap">
                                <button type="button" class="btn btn-danger btn-icon-text me-3 mb-2 mb-md-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-download-cloud btn-icon-prepend">
                                        <polyline points="8 17 12 21 16 17"></polyline>
                                        <line x1="12" y1="12" x2="12" y2="21"></line>
                                        <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                                    </svg>
                                    Download Report
                                </button>
                                <a type="button"
                                    class="btn btn-warning btn-sm btn-icon-text float-end align-items-center"
                                    title="Edit" href="{{ route('admin.edit-customer-info', $data->id) }}" wire:navigate>
                                    <i class="bi bi-pencil-square btn-icon-prepend"></i>
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6 class="py-2 bg-orange-light">Buyer Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Name:</b><span class="ms-2">{{ $data->name }}</span></td>
                                        <td><b>Mobile Number:</b><span class="ms-2">{{ $data->phone }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Email:</b><span class="ms-2">{{ $data->email }}</span></td>
                                        <td><b>Company Name:</b><span class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->company_name : '' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="py-2 bg-orange-light">Primary Address</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
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
