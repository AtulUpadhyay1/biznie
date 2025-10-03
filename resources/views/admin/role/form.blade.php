<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 card-title">
                            <h4>{{ $page_title }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <a class="btn btn-danger btn-sm btn-icon-text float-end align-items-center" title="Cancel"
                                href="{{ route('admin.role.index') }}" wire:navigate>
                                <i class="bi bi-x-lg btn-icon-prepend"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $hidden_id ? 'update()' : 'save()' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span
                                            class="text-danger">*</span></label>
                                    <input wire:model="name" type="text" class="form-control" id="name"
                                        placeholder="Enter role name">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <div class="mb-3">
                                <h5 class="text-primary"><i class="fas fa-shield-alt me-2"></i>Permissions</h5>
                                <p class="text-muted small mb-3">Select the permissions for this role. You can select
                                    individual permissions or use "Select All" for each section.</p>
                                @error('permission')
                                    <small class="text-danger error d-block mb-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row g-3">
                                @forelse ($permissions as $parentName => $permissionGroup)
                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                            <div
                                                class="card-header bg-gradient-primary text-white py-3 border-0 rounded-top">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-folder2-open me-2"></i>
                                                        <h6 class="mb-0 fw-bold text-white">
                                                            {{ ucwords(str_replace(['_', '-'], ' ', $parentName)) }}
                                                        </h6>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input select-all-toggle"
                                                            type="checkbox" id="selectAll_{{ $parentName }}"
                                                            onchange="toggleSectionPermissions('{{ $parentName }}', this.checked)">
                                                        <label class="form-check-label text-white small"
                                                            for="selectAll_{{ $parentName }}">
                                                            Select All
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="permission-list"
                                                    style="max-height: 250px; overflow-y: auto;">
                                                    @foreach ($permissionGroup as $index => $permission)
                                                        <div
                                                            class="permission-item p-3 border-bottom {{ $index % 2 == 0 ? 'bg-light' : '' }} hover-bg-primary">
                                                            <div class="form-check d-flex align-items-center">
                                                                <input
                                                                    class="form-check-input me-3 permission-checkbox section-{{ $parentName }}"
                                                                    type="checkbox" wire:model.defer="permission"
                                                                    value="{{ $permission->id }}"
                                                                    id="per_{{ $permission->name }}"
                                                                    onchange="updateSelectAllState('{{ $parentName }}')">
                                                                <label
                                                                    class="form-check-label flex-grow-1 cursor-pointer"
                                                                    for="per_{{ $permission->name }}">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bi bi-key me-2 text-muted small"></i>
                                                                        <span class="fw-medium">
                                                                            {{ ucwords(str_replace(['_', '-'], ' ', str_replace($parentName . '_', '', $permission->name))) }}
                                                                        </span>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light border-0 py-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ count($permissionGroup) }}
                                                    permission{{ count($permissionGroup) > 1 ? 's' : '' }} available
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning border-0 shadow-sm text-center py-4">
                                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                                            <h5>No Permissions Found</h5>
                                            <p class="mb-0 text-muted">No permissions are available. Please contact the
                                                administrator or try refreshing the page.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-submit-btn text="{{ $hidden_id ? 'Update' : 'Save' }}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
            transform: translateY(-2px);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8a0707 0%, #e65252 100%);
        }

        .permission-item {
            transition: background-color 0.2s ease;
        }

        .hover-bg-primary:hover {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .permission-list::-webkit-scrollbar {
            width: 6px;
        }

        .permission-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .permission-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .permission-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    <script>
        function toggleSectionPermissions(sectionName, isChecked) {
            const checkboxes = document.querySelectorAll(`.section-${sectionName}`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                // Trigger Livewire update
                checkbox.dispatchEvent(new Event('change'));
            });
        }

        function updateSelectAllState(sectionName) {
            const sectionCheckboxes = document.querySelectorAll(`.section-${sectionName}`);
            const selectAllCheckbox = document.getElementById(`selectAll_${sectionName}`);

            const checkedCount = Array.from(sectionCheckboxes).filter(cb => cb.checked).length;
            const totalCount = sectionCheckboxes.length;

            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === totalCount) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Initialize select all states on page load
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($permissions as $parentName => $permissionGroup)
                updateSelectAllState('{{ $parentName }}');
            @endforeach
        });
    </script>
</div>
