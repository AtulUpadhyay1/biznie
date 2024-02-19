<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-4">
            <div class="position-sticky customer-profile-card fixed-top">
                @include('admin.seller.seller_nav')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8 card-title">
                            <h5 class="mt-2">Seller Kyc Detail</h5>
                        </div>
                        <div class="col-4 text-end">
                            @if ($data->getSellerKycDetail && $data->getSellerKycDetail->status == 'uploaded' || $data->getSellerKycDetail->status == 'pending')
                                <select class="form-select" wire:model="status" wire:change="updateStatus()">
                                    <option value="pending" disabled>Pending</option>
                                    <option value="uploaded" disabled>Uploaded</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6 class="py-2 bg-orange-light">Seller Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Customer Name:</b><span class="ms-2">{{ $data->name }}</span></td>
                                        <td><b>Mobile Number:</b><span class="ms-2">{{ $data->phone }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Email:</b><span class="ms-2">{{ $data->email }}</span></td>
                                        {{-- <td><b>Gender:</b><span class="ms-2">Male</span></td> --}}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="py-2 bg-orange-light">Address Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Address:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->address : '' }}</span></td>
                                        <td><b>Pincode:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->postal_code : '' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>City:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->city : '' }}</span></td>
                                        <td><b>State:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->state : '' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Country:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->country : '' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="py-2 bg-orange-light">Bank Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Account Number:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->account_number : '' }}</span></td>
                                        <td><b>Account Holder Name:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->account_holder_name : '' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Bank Name:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->bank_name : '' }}</span></td>
                                        <td><b>Ifsc Code:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->ifsc_code : '' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="py-2 bg-orange-light">More Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Identity Type:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->identity_type : '' }}</span></td>
                                        <td><b>Identity Number:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->identity_number : '' }}</span></td>
                                        <td><b>Identity Proof:</b><span class="ms-2"><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->identity_proof) : '' }}" alt="Identity Proof" onerror="this.onerror=null; this.src='{{asset('admin_css/no-photo.png')}}'"> </span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Address Type:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->address_type : '' }}</span></td>
                                        <td><b>Address Proof:</b><span class="ms-2"><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->address_proof) : '' }}" alt="Address Proof"></span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Business Registration Number:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->business_registration_number : '' }}</span></td>
                                        <td><b>Business Registration Certificate:</b><span class="ms-2"><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->business_registration_certificate) : '' }}" alt="Business Registration Certificate" onerror="this.onerror=null; this.src='{{asset('admin_css/no-photo.png')}}'"> </span></td>
                                    </tr>
                                    @if ($data->getSellerKycDetail && $data->getSellerKycDetail->trademark_registration_proof)
                                        <tr>
                                            <td colspan="3"><b>Trademark Registration Proof:</b><span class="ms-2"><img src="{{ $data->getSellerKycDetail ? imageUrl($data->getSellerKycDetail->trademark_registration_proof) : '' }}" alt="Trademark Registration Proof" onerror="this.onerror=null; this.src='{{asset('admin_css/no-photo.png')}}'"> </span></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2"><b>GST Type:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->gst_type : '' }}</span></td>
                                        <td><b>GST Number:</b><span class="ms-2">{{ $data->getSellerKycDetail ? $data->getSellerKycDetail->gst_number : '' }} </span></td>
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
