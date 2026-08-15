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
                    <h4>All Enquiry</h4>
                    <div class="bz-toolbar">
                        <button type="button" class="btn btn-danger btn-sm">
                            <i class="bi bi-cloud-download"></i>
                            Download Report
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Unique Id</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>{{ $data->unique_id }}</td>
                                        <td>{{ $data->getBrand->name }}</td>
                                        <td>{{ $data->getCommodityProduct->name }}</td>
                                        <td>
                                            <span class="bz-status {{ $data->status == 'ordered' ? 'bz-status--success' : 'bz-status--info' }}">{{ ucfirst($data->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" class="bz-row-action" id="actionBtn{{ $data->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="actionBtn{{ $data->id }}">
                                                <a class="dropdown-item d-flex align-items-center" href=""><i class="bi bi-eye me-2"></i><span>View</span></a>
                                                <a href="javascript:;" class="dropdown-item d-flex align-items-center"><i class="bi bi-arrow-down me-2"></i><span>Download</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-table-no-data colspan="6" />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
