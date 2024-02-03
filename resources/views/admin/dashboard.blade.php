<div>
    @section('title', config('app.name') . ' | '.$page_title)
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                <span class="input-group-text input-group-addon bg-transparent border-danger" data-toggle>
                    <i class="bi bi-calendar text-danger"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-danger" placeholder="Select date" data-input>
            </div>
            {{-- <button type="button" class="btn btn-outline-danger btn-icon-text me-2 mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="printer"></i>
                Print
            </button> --}}
            <button type="button" class="btn btn-danger btn-icon-text mb-2 mb-md-0">
                <i class="bi bi-cloud-download btn-icon-prepend"></i>
                Download Report
            </button>
        </div>
    </div>
</div>

