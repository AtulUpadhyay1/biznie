<div>
    @section('title', config('app.name') . ' | ' . $page_title)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>

                    <div class="bz-toolbar">
                        <form class="custom-search-bar">
                            <label class="bz-filter-label" for="contact_us_search">Search messages</label>
                            <div class="input-group">
                                <span class="input-group-text"> <i class="bi bi-search"></i></span>
                                <input type="text" id="contact_us_search" class="form-control" placeholder="Search here...">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $key => $data)
                                    <tr>
                                        <td>{{ $list->firstItem() + $loop->index }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->phone }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-success view-message"
                                                data-name="{{ e($data->name) }}">
                                                View
                                            </button>
                                            <div class="d-none message-text">{{ $data->message }}</div>
                                        </td>
                                        <td>{{ dateTimeFormat($data->created_at) }}</td>
                                    </tr>
                                @empty
                                    <x-table-no-data />
                                @endforelse
                            </tbody>
                        </table>
                        <div class="bz-pagination">
                            {{ $list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- filled by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var messageModalEl = document.getElementById('messageModal');
                if (!messageModalEl || typeof bootstrap === 'undefined') return;
                var bsModal = new bootstrap.Modal(messageModalEl);

                document.querySelectorAll('.view-message').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var name = this.getAttribute('data-name') || 'Message';
                        var msgEl = this.closest('tr').querySelector('.message-text');
                        var msg = msgEl ? msgEl.textContent.trim() : '';
                        messageModalEl.querySelector('.modal-title').textContent = 'Message from ' +
                            name;
                        // preserve line breaks
                        messageModalEl.querySelector('.modal-body').innerHTML = msg.replace(/\n/g,
                            '<br>');
                        bsModal.show();
                    });
                });
            });
        </script>
    @endpush
</div>
