@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.edit_category')" />

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.english_name') }}</label>
                    <input type="text" name="name[en]" class="form-control"
                           value="{{ old('name.en', $category->getTranslation('name', 'en')) }}">
                    @error('name.en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.arabic_name') }}</label>
                    <input type="text" name="name[ar]" class="form-control"
                           value="{{ old('name.ar', $category->getTranslation('name', 'ar')) }}">
                    @error('name.ar')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.english_description') }}</label>
                    <textarea name="description[en]" class="form-control" rows="4">
                        {{ old('description.en', $category->getTranslation('description', 'en')) }}
                    </textarea>
                    @error('description.en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.arabic_description') }}</label>
                    <textarea name="description[ar]" class="form-control" rows="4">
                        {{ old('description.ar', $category->getTranslation('description', 'ar')) }}
                    </textarea>
                    @error('description.ar')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('admin.sort_order') }}</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $category->sort_order) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                           id="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                </div>

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






