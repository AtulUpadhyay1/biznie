<div>
    @section('title', config('app.name') . ' | ' . $page_title)

    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4>{{ $page_title }}</h4>
                    <div class="bz-toolbar">
                        <a class="btn btn-secondary btn-sm" title="Cancel"
                            href="{{ route('admin.role.index') }}" wire:navigate>
                            <i class="bi bi-x-lg"></i>Cancel
                        </a>
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
                        <div class="col-12 mb-3">
                            <div class="mb-3">
                                <h5><i class="bi bi-shield-lock me-2 text-muted"></i>Permissions</h5>
                                <p class="text-muted small mb-3">Select the permissions for this role. You can select
                                    individual permissions or use "Select All" for each section.</p>
                                @error('permission')
                                    <small class="text-danger error d-block mb-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row g-3">
                                @forelse ($permissions as $parentName => $permissionGroup)
                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                        <div class="card h-100 hover-shadow transition-all">
                                            <div class="card-header">
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-folder2-open me-2 text-muted"></i>
                                                        <h6 class="mb-0">
                                                            {{ ucwords(str_replace(['_', '-'], ' ', $parentName)) }}
                                                        </h6>
                                                    </div>
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input select-all-toggle"
                                                            type="checkbox" id="selectAll_{{ $parentName }}"
                                                            onchange="toggleSectionPermissions('{{ $parentName }}', this.checked)">
                                                        <label class="form-check-label small text-muted"
                                                            for="selectAll_{{ $parentName }}">
                                                            Select all
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="permission-list">
                                                    @foreach ($permissionGroup as $index => $permission)
                                                        <div class="permission-item px-3 py-2 hover-bg-primary">
                                                            <div class="form-check d-flex align-items-center mb-0">
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
                                            <div class="card-footer py-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    {{ count($permissionGroup) }}
                                                    permission{{ count($permissionGroup) > 1 ? 's' : '' }} available
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="bz-empty">
                                            <span class="bz-empty__icon"><i class="bi bi-shield-exclamation"></i></span>
                                            <span class="bz-empty__title">No permissions found</span>
                                            <p class="bz-empty__text">No permissions are available. Please contact the
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
    {{-- page styles moved to admin_css/assets/css/biznie-admin.css --}}

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
