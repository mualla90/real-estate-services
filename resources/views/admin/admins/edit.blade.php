@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Edit Admin</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.admins.update', $admin) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}">
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                @selected(old('role', $admin->roles->first()?->name) === $role->name)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                           id="is_active" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
