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
                                <div class="col-md-6">
                                    <select class="form-select" name="priority" id="priority" wire:model="priority" wire:change="updatePriority($event.target.value)">
                                        <option value="">Select priority</option>
                                        @for ($i=1; $i<=5; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <a type="button"
                                    class="btn btn-warning btn-sm btn-icon-text float-end align-items-center ms-2"
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
                                    <tr>
                                        <td><b>GSTIN:</b><span class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->gst_number : '' }}</span></td>
                                        <td><b>PAN:</b><span class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->pan_number : '' }}</span></td>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="py-2 bg-orange-light">Address</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Address:</b><span class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->company_address : '' }}</span></td>
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
