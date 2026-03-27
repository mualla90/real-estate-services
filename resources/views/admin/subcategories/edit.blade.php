@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Edit Subcategory</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('category_id', $subcategory->category_id) == $category->id)>
                                {{ $category->getTranslation('name', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">English Name</label>
                    <input type="text" name="name[en]" class="form-control"
                           value="{{ old('name.en', $subcategory->getTranslation('name', 'en')) }}">
                    @error('name.en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Arabic Name</label>
                    <input type="text" name="name[ar]" class="form-control"
                           value="{{ old('name.ar', $subcategory->getTranslation('name', 'ar')) }}">
                    @error('name.ar')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $subcategory->sort_order) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                           id="is_active" {{ old('is_active', $subcategory->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
