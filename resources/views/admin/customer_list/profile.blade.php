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
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>Buyer Profile</h4>
                    <div class="bz-toolbar">
                        <label class="bz-filter-label" for="priority">Priority</label>
                        <select class="form-select" name="priority" id="priority" wire:model="priority"
                            wire:change="updatePriority($event.target.value)">
                            <option value="">Select priority</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <a type="button" class="btn btn-secondary btn-sm" title="Edit"
                            href="{{ route('admin.edit-customer-info', $data->id) }}" wire:navigate>
                            <i class="bi bi-pencil-square"></i>
                            Edit
                        </a>

                        @if (isset($data->status) && $data->status === 'active')
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click.prevent="openBlockModal">
                                <i class="bi bi-slash-circle"></i>
                                Block
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-outline-success"
                                wire:click.prevent="unblock">
                                <i class="bi bi-unlock"></i>
                                Unblock
                            </button>
                        @endif
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
                                    <textarea id="blockReason" class="form-control" rows="4" wire:model.defer="blockReason"
                                        placeholder="Enter reason for blocking the customer"></textarea>
                                    @error('blockReason')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger btn-sm"
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

                <div class="card-body">
                    <div class="row">
                        <h6 class="bz-section-label">Buyer Details</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered mt-3">
                                <tbody>
                                    <tr>
                                        <td><b>Name:</b></td>
                                        <td>{{ $data->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Mobile Number:</b></td>
                                        <td><span class="text-uppercase">{{ $data->phone }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Email:</b></td>
                                        <td>{{ $data->email }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Company Name:</b></td>
                                        <td><span
                                                class="text-uppercase">{{ $data->getUserDetail ? $data->getUserDetail->company_name : '' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>GSTIN:</b></td>
                                        <td>{{ $data->getUserDetail ? $data->getUserDetail->gst_number : '--' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>PAN:</b></td>
                                        <td>{{ $data->getUserDetail ? $data->getUserDetail->pan_number : '--' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>City:</b></td>
                                        <td>{{ $data->getUserDetail ? $data->getUserDetail->city : '--' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h6 class="bz-section-label">Address</h6>
                        <div class="table-responsive mb-4">
                            <table class="custom-table borderless-table">
                                <tbody>
                                    <tr>
                                        <td><span
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
