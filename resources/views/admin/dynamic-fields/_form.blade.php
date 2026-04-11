@php
    $dynamicField = $dynamicField ?? null;
    $isEdit = (bool) $dynamicField;
@endphp

<div class="mb-3">
    <label class="form-label">{{ __('admin.english_name') }}</label>
    <input type="text" name="name[en]" class="form-control"
           value="{{ old('name.en', $dynamicField?->getTranslation('name', 'en')) }}">
    @error('name.en')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">{{ __('admin.arabic_name') }}</label>
    <input type="text" name="name[ar]" class="form-control"
           value="{{ old('name.ar', $dynamicField?->getTranslation('name', 'ar')) }}">
    @error('name.ar')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('admin.field_key') }}</label>
        <input type="text" name="field_key" class="form-control"
               value="{{ old('field_key', $dynamicField?->field_key) }}">
        @error('field_key')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('admin.field_type') }}</label>
        <select name="field_type" class="form-select">
            @php
                $selectedType = old('field_type', $dynamicField?->field_type);
            @endphp
            @foreach(['text', 'number', 'select', 'boolean', 'date'] as $type)
                <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>
                    {{ __('admin.field_type_' . $type) }}
                </option>
            @endforeach
        </select>
        @error('field_type')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('admin.category_optional') }}</label>
        <select name="category_id" class="form-select">
            <option value="">{{ __('admin.none') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (string) old('category_id', $dynamicField?->category_id) === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->getTranslation('name', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('admin.subcategory_optional') }}</label>
        <select name="subcategory_id" class="form-select">
            <option value="">{{ __('admin.none') }}</option>
            @foreach($subcategories as $subcategory)
                <option value="{{ $subcategory->id }}"
                    {{ (string) old('subcategory_id', $dynamicField?->subcategory_id) === (string) $subcategory->id ? 'selected' : '' }}>
                    {{ $subcategory->getTranslation('name', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
        @error('subcategory_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('admin.options_help') }}</label>
    <textarea name="options_text" class="form-control" rows="4">{{ old('options_text', $isEdit && is_array($dynamicField?->options) ? implode(PHP_EOL, $dynamicField->options) : '') }}</textarea>
    @error('options_text')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">{{ __('admin.sort_order') }}</label>
        <input type="number" name="sort_order" class="form-control"
               value="{{ old('sort_order', $dynamicField?->sort_order ?? 0) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">{{ __('admin.status') }}</label>
        <select name="status" class="form-select">
            @php
                $selectedStatus = old('status', $dynamicField?->status ?? 'active');
            @endphp
            <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
            <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
        </select>
    </div>

    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_required" value="0">
            <input type="checkbox" name="is_required" value="1" class="form-check-input" id="is_required"
                {{ old('is_required', $dynamicField?->is_required ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_required">{{ __('admin.required') }}</label>
        </div>
    </div>
</div>

