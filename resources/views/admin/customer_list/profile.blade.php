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
                                    <select class="form-select" name="priority" id="priority" wire:model="priority"
                                        wire:change="updatePriority($event.target.value)">
                                        <option value="">Select priority</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <a type="button"
                                    class="btn btn-warning btn-sm btn-icon-text float-end align-items-center ms-2"
                                    title="Edit" href="{{ route('admin.edit-customer-info', $data->id) }}"
                                    wire:navigate>
                                    <i class="bi bi-pencil-square btn-icon-prepend"></i>
                                    Edit
                                </a>
                                
                                @if (isset($data->status) && $data->status === 'active')
                                    <button type="button"
                                        class="btn btn-danger btn-sm btn-icon-text align-items-center ms-2"
                                        wire:click.prevent="openBlockModal">
                                        <i class="bi bi-slash-circle btn-icon-prepend"></i>
                                        Block
                                    </button>
                                @else
                                    <button type="button"
                                        class="btn btn-success btn-sm btn-icon-text align-items-center ms-2"
                                        wire:click.prevent="unblock">
                                        <i class="bi bi-unlock btn-icon-prepend"></i>
                                        Unblock
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Block Reason Modal -->
                    <div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel"
                        aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="blockModalLabel">Block Customer - Reason</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="blockReason" class="form-label">Reason</label>
                                        <textarea id="blockReason" class="form-control" rows="4" wire:model.defer="blockReason" placeholder="Enter reason for blocking the customer"></textarea>
                                        @error('blockReason')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger"
                                        wire:click.prevent="confirmBlock">Confirm Block</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        window.addEventListener('open-block-modal', event => {
                            const modalEl = document.getElementById('blockModal');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                const modal = new bootstrap.Modal(modalEl);
                                modal.show();
                            }
                        });
                        window.addEventListener('close-block-modal', event => {
                            const modalEl = document.getElementById('blockModal');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                const instance = bootstrap.Modal.getInstance(modalEl);
                                if (instance) instance.hide();
                            }
                        });
                    </script>
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
                                        <td><b>Company Name:</b><span
                                                class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->company_name : '' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>GSTIN:</b><span
                                                class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->gst_number : '' }}</span>
                                        </td>
                                        <td><b>PAN:</b><span
                                                class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->pan_number : '' }}</span>
                                        </td>
                                        <td><b>CITY:</b><span
                                                class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->city : '' }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="py-2 bg-orange-light">Address</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><b>Address:</b><span
                                                class="ms-2">{{ $data->getUserDetail ? $data->getUserDetail->company_address : '' }}</span>
                                        </td>
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
