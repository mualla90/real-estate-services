<div class="mb-3">
    <label for="name" class="form-label">{{ __('admin.role_name') }}</label>
    <input type="text"
           name="name"
           id="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $role->name ?? '') }}"
           {{ isset($role) && $role->name === 'super_admin' ? 'readonly' : '' }}>

    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">{{ __('admin.permissions') }}</label>

    @php
        $selectedPermissions = old(
            'permissions',
            isset($role) ? $role->permissions->pluck('name')->toArray() : []
        );

        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
    @endphp

    <div class="row">
        @foreach($groupedPermissions as $group => $groupPermissions)
            @php
                $groupKey = str_replace(['.', '-'], '_', $group);
            @endphp

            <div class="col-md-4 mb-3">
                <div class="border rounded p-3 h-100">

                    <h6 class="mb-3 text-capitalize">
                        {{ \Illuminate\Support\Facades\Lang::has('admin.' . $groupKey)
                            ? __('admin.' . $groupKey)
                            : ucfirst(str_replace(['-', '_'], ' ', $group)) }}
                    </h6>

                    @foreach($groupPermissions as $permission)
                        @php
                            $permissionKey = str_replace(['.', '-'], '_', $permission->name);
                        @endphp

                        <div class="form-check mb-2">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->name }}"
                                   id="permission_{{ md5($permission->name) }}"
                                   {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>

                            <label class="form-check-label" for="permission_{{ md5($permission->name) }}">
                                {{ \Illuminate\Support\Facades\Lang::has('admin.' . $permissionKey)
                                    ? __('admin.' . $permissionKey)
                                    : $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @error('permissions')
        <div class="text-danger small">{{ $message }}</div>
    @enderror

    @error('permissions.*')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>
