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
                        <form wire:submit.prevent="{{ isset($hidden_id) ? 'update' : 'store' }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span
                                                class="text-danger">*</span></label>
                                        <input wire:model="name" type="text" class="form-control" id="name"
                                            placeholder="Enter name">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input wire:model="email" type="email" class="form-control" id="email"
                                            placeholder="Enter email">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone <span
                                                class="text-danger">*</span></label>
                                        <input wire:model="phone" type="text" class="form-control" id="phone"
                                            placeholder="Enter phone">
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input wire:model="password" type="password" class="form-control"
                                                id="password" placeholder="Enter password">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="togglePassword">
                                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role <span
                                                class="text-danger">*</span></label>
                                        <select wire:model="roles" id="role" class="form-select">
                                            <option value="">Select Role</option>
                                            @foreach ($roles_list as $role)
                                                <option value="{{ $role }}">{{ $role }}</option>
                                            @endforeach
                                        </select>
                                        @error('roles')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <x-submit-btn text="{{ $hidden_id ? 'Update' : 'Save' }}" />
                                </div>
                            </div>
                        </form>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Toggle the icon
                if (type === 'password') {
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bx-hide');
                } else {
                    toggleIcon.classList.remove('bx-hide');
                    toggleIcon.classList.add('bi-eye');
                }
            });
        });
    </script>
</div>
