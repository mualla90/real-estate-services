@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.create_activity_type')" />

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.activity-types.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.english_name') }}</label>
                    <input type="text" name="name[en]" class="form-control" value="{{ old('name.en') }}">
                    @error('name.en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.arabic_name') }}</label>
                    <input type="text" name="name[ar]" class="form-control" value="{{ old('name.ar') }}">
                    @error('name.ar')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.sort_order') }}</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="status" checked>
                    <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                </div>

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    <a href="{{ route('admin.activity-types.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






