@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">{{ __('admin.create_admin') }}</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.admins.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.email') }}</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.role') }}</label>
                    <select name="role" class="form-select">
                        <option value="">{{ __('admin.select_role') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.password') }}</label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           id="is_active"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
