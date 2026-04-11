<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.english_title') }}</label>
        <input type="text"
               name="title[en]"
               class="form-control @error('title.en') is-invalid @enderror"
               value="{{ old('title.en', isset($slider) ? $slider->getTranslation('title', 'en') : '') }}"
               required>
        @error('title.en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('admin.arabic_title') }}</label>
        <input type="text"
               name="title[ar]"
               class="form-control @error('title.ar') is-invalid @enderror"
               value="{{ old('title.ar', isset($slider) ? $slider->getTranslation('title', 'ar') : '') }}"
               required>
        @error('title.ar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('admin.english_subtitle') }}</label>
        <input type="text"
               name="subtitle[en]"
               class="form-control @error('subtitle.en') is-invalid @enderror"
               value="{{ old('subtitle.en', isset($slider) ? $slider->getTranslation('subtitle', 'en') : '') }}">
        @error('subtitle.en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('admin.arabic_subtitle') }}</label>
        <input type="text"
               name="subtitle[ar]"
               class="form-control @error('subtitle.ar') is-invalid @enderror"
               value="{{ old('subtitle.ar', isset($slider) ? $slider->getTranslation('subtitle', 'ar') : '') }}">
        @error('subtitle.ar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">{{ __('admin.slider_link') }}</label>
        <input type="url"
               name="link"
               class="form-control @error('link') is-invalid @enderror"
               value="{{ old('link', $slider->link ?? '') }}"
               placeholder="https://example.com">
        @error('link')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('admin.sort_order') }}</label>
        <input type="number"
               name="sort_order"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $slider->sort_order ?? 0) }}"
               min="0">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('admin.starts_at') }}</label>
        <input type="datetime-local"
               name="starts_at"
               class="form-control @error('starts_at') is-invalid @enderror"
               value="{{ old('starts_at', isset($slider) && $slider->starts_at ? $slider->starts_at->format('Y-m-d\TH:i') : '') }}">
        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('admin.ends_at') }}</label>
        <input type="datetime-local"
               name="ends_at"
               class="form-control @error('ends_at') is-invalid @enderror"
               value="{{ old('ends_at', isset($slider) && $slider->ends_at ? $slider->ends_at->format('Y-m-d\TH:i') : '') }}">
        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">{{ __('admin.slider_image') }}</label>
        <input type="file"
               name="image"
               accept=".jpg,.jpeg,.png,.webp"
               class="form-control @error('image') is-invalid @enderror"
               @if(! isset($slider)) required @endif>
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if(isset($slider) && $slider->getFirstMediaUrl('image'))
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-2">
                    <img src="{{ $slider->getFirstMediaUrl('image') }}"
                         alt="slider-image"
                         class="img-fluid rounded"
                         style="max-height: 240px; object-fit: cover;">
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-12">
        <div class="form-check mb-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   class="form-check-input"
                   id="is_active"
                   @checked(old('is_active', $slider->is_active ?? true))>
            <label class="form-check-label" for="is_active">
                {{ __('admin.active') }}
            </label>
        </div>
    </div>
</div>

