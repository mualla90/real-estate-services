<div class="mb-3">
    <label class="form-label">Name (English)</label>
    <input type="text"
           name="name[en]"
           class="form-control @error('name.en') is-invalid @enderror"
           value="{{ old('name.en', isset($city) ? $city->getTranslation('name', 'en') : '') }}">
    @error('name.en')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Name (Arabic)</label>
    <input type="text"
           name="name[ar]"
           class="form-control @error('name.ar') is-invalid @enderror"
           value="{{ old('name.ar', isset($city) ? $city->getTranslation('name', 'ar') : '') }}">
    @error('name.ar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input type="number"
           name="sort_order"
           class="form-control @error('sort_order') is-invalid @enderror"
           value="{{ old('sort_order', $city->sort_order ?? 0) }}">
    @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox"
           name="is_active"
           value="1"
           class="form-check-input"
           id="is_active"
           @checked(old('is_active', $city->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>
