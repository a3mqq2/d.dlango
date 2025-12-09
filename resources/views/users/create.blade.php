@extends('layouts.app')

@section('title', __('messages.add_user'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-user-plus me-2"></i>
                    {{ __('messages.add_user') }}
                </h5>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-right me-1"></i>
                    {{ __('messages.back') }}
                </a>
            </div>
            <div class="card-body">
                @include('layouts.messages')

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('messages.enter_name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="{{ __('messages.enter_email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('messages.enter_password') }}" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.confirm_password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('messages.confirm_password') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.role') }} <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">{{ __('messages.select_role') }}</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('messages.role_admin') }}</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>{{ __('messages.role_user') }}</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <div class="form-check mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('messages.active') }}</label>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Section --}}
                    <div id="permissions-section" class="mt-4" style="{{ old('role') == 'user' ? '' : 'display: none;' }}">
                        <h6 class="mb-3">
                            <i class="ti ti-shield-check me-2"></i>
                            {{ __('messages.permissions') }}
                        </h6>
                        <div class="alert alert-info mb-3">
                            <i class="ti ti-info-circle me-1"></i>
                            {{ __('messages.permissions_hint') }}
                        </div>

                        <div class="row g-3">
                            @foreach($permissionGroups as $group => $permissions)
                                <div class="col-md-4">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input group-checkbox" id="group_{{ $group }}" data-group="{{ $group }}">
                                                <label class="form-check-label fw-bold" for="group_{{ $group }}">
                                                    {{ __('messages.permission_group_' . $group) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="card-body py-2">
                                            @foreach($permissions as $permission)
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           class="form-check-input permission-checkbox"
                                                           data-group="{{ $group }}"
                                                           id="perm_{{ $permission->id }}"
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                        {{ $permission->display_name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary" style="background-color: #b65f7a; border-color: #b65f7a;">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const permissionsSection = document.getElementById('permissions-section');

    // Toggle permissions section based on role
    roleSelect.addEventListener('change', function() {
        if (this.value === 'user') {
            permissionsSection.style.display = 'block';
        } else {
            permissionsSection.style.display = 'none';
        }
    });

    // Group checkbox functionality
    document.querySelectorAll('.group-checkbox').forEach(function(groupCheckbox) {
        groupCheckbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll('.permission-checkbox[data-group="' + group + '"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = groupCheckbox.checked;
            });
        });
    });

    // Update group checkbox when individual permissions change
    document.querySelectorAll('.permission-checkbox').forEach(function(permCheckbox) {
        permCheckbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const groupCheckbox = document.getElementById('group_' + group);
            const checkboxes = document.querySelectorAll('.permission-checkbox[data-group="' + group + '"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            groupCheckbox.checked = allChecked;
        });
    });

    // Initialize group checkboxes on page load
    document.querySelectorAll('.group-checkbox').forEach(function(groupCheckbox) {
        const group = groupCheckbox.dataset.group;
        const checkboxes = document.querySelectorAll('.permission-checkbox[data-group="' + group + '"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        groupCheckbox.checked = allChecked;
    });
});
</script>
@endpush
@endsection
