@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">{{ __('admin.edit_activity_type') }}</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.activity-types.update', $activityType) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.english_name') }}</label>
                    <input type="text" name="name[en]" class="form-control"
                           value="{{ old('name.en', $activityType->getTranslation('name', 'en')) }}">
                    @error('name.en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.arabic_name') }}</label>
                    <input type="text" name="name[ar]" class="form-control"
                           value="{{ old('name.ar', $activityType->getTranslation('name', 'ar')) }}">
                    @error('name.ar')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.sort_order') }}</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $activityType->sort_order) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                        id="is_active" {{ old('is_active', $activityType->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
                <a href="{{ route('admin.activity-types.index') }}" class="btn btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
