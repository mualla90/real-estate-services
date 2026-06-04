@extends('layouts.admin')

@section('title', __('admin.service_review'))

@section('content')
@php
    $locale = app()->getLocale();
    $title = $service->getTranslation('title', $locale, false) ?: '-';
    $description = $service->getTranslation('description', $locale, false) ?: '-';
    $businessAccountName = $service->businessAccount?->getTranslation('name', $locale, false) ?: '-';
    $statusClass = 'status-' . $service->status;
    $mainImage = $service->getFirstMedia('main_image');
    $galleryMedia = $service->getMedia('gallery');
    $dynamicValues = $service->dynamicFieldValues;

    $formatDynamicValue = function ($item) {
        if (! is_null($item->value_text)) {
            return $item->value_text;
        }

        if (! is_null($item->value_number)) {
            return $item->value_number;
        }

        if (is_array($item->value_json)) {
            return implode(', ', array_filter(array_map(
                fn ($value) => is_scalar($value) ? (string) $value : json_encode($value),
                $item->value_json
            )));
        }

        return '-';
    };
@endphp

<div class="container-fluid admin-page">
    <x-admin.page-header
        :title="__('admin.service_details')"
        :subtitle="$title"
        icon-name="services">
        <a href="{{ route('admin.services.review.index') }}" class="btn btn-outline-secondary btn-with-icon">
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M19 12H5m6-6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span>{{ __('admin.back_to_list') }}</span>
        </a>
    </x-admin.page-header>

    <div class="review-hero review-hero-{{ $service->status }} mb-4">
        <div class="review-hero-main">
            <span class="badge status-badge {{ $statusClass }}">{{ __('admin.' . $service->status) }}</span>
            <h4 class="review-hero-title">{{ $title }}</h4>
            <div class="review-hero-meta">
                <span>{{ __('admin.business_account') }}: {{ $businessAccountName }}</span>
                <span>{{ __('admin.created_at') }}: {{ optional($service->created_at)->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="review-hero-stats">
            <div class="review-stat">
                <span class="review-stat-value">{{ $mainImage ? 1 : 0 }}</span>
                <span class="review-stat-label">{{ __('admin.service_main_image') }}</span>
            </div>
            <div class="review-stat">
                <span class="review-stat-value">{{ $galleryMedia->count() }}</span>
                <span class="review-stat-label">{{ __('admin.service_gallery') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.service_details') }}</div>
                        <div class="card-body">
                            <div class="review-detail-list">
                                <div class="review-detail-item">
                                    <span>{{ __('admin.title') }}</span>
                                    <strong>{{ $title }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.category') }}</span>
                                    <strong>{{ $service->category?->getTranslation('name', $locale, false) ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.subcategory') }}</span>
                                    <strong>{{ $service->subcategory?->getTranslation('name', $locale, false) ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.city') }}</span>
                                    <strong>{{ $service->city?->getTranslation('name', $locale, false) ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.service_type') }}</span>
                                    <strong>{{ $service->service_type ? ucfirst($service->service_type) : '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.service_pricing') }}</div>
                        <div class="card-body">
                            <div class="review-detail-list">
                                <div class="review-detail-item">
                                    <span>{{ __('admin.price_usd') }}</span>
                                    <strong>{{ $service->price_usd ?? '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.price_syp') }}</span>
                                    <strong>{{ $service->price_syp ?? '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.active') }}</span>
                                    <strong>
                                        @if($service->is_active)
                                            <span class="badge status-badge status-active">{{ __('admin.active') }}</span>
                                        @else
                                            <span class="badge status-badge status-inactive">{{ __('admin.inactive') }}</span>
                                        @endif
                                    </strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.published_at') }}</span>
                                    <strong>{{ optional($service->published_at)->format('Y-m-d H:i') ?: '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.description') }}</div>
                        <div class="card-body">
                            <p class="review-description mb-0">{{ $description }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.business_account') }}</div>
                        <div class="card-body">
                            <div class="review-owner">
                                <div class="review-owner-avatar">{{ mb_substr($businessAccountName, 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold">{{ $businessAccountName }}</div>
                                    <div class="text-muted small">{{ __('admin.id') }}: {{ $service->business_account_id }}</div>
                                    @if($service->businessAccount)
                                        <a href="{{ route('admin.business-accounts.show', $service->businessAccount) }}" class="small">
                                            {{ __('admin.open') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.user') }}</div>
                        <div class="card-body">
                            <div class="review-owner">
                                <div class="review-owner-avatar">{{ mb_substr($service->businessAccount?->user?->name ?? 'U', 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold">{{ $service->businessAccount?->user?->name ?: '-' }}</div>
                                    <div class="text-muted small">{{ $service->businessAccount?->user?->phone ?: '-' }}</div>
                                    <div class="text-muted small">{{ $service->businessAccount?->user?->email ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header d-flex justify-content-between align-items-center gap-2">
                            <span>{{ __('admin.service_location') }}</span>
                            @if(! is_null($service->latitude) && ! is_null($service->longitude))
                                @php
                                    $latitude = (float) $service->latitude;
                                    $longitude = (float) $service->longitude;
                                    $mapDelta = 0.01;
                                    $bbox = implode(',', [
                                        $longitude - $mapDelta,
                                        $latitude - $mapDelta,
                                        $longitude + $mapDelta,
                                        $latitude + $mapDelta,
                                    ]);
                                    $marker = $latitude . ',' . $longitude;
                                    $osmEmbedUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' . $bbox . '&layer=mapnik&marker=' . $marker;
                                    $osmExternalUrl = 'https://www.openstreetmap.org/?mlat=' . $latitude . '&mlon=' . $longitude . '#map=16/' . $latitude . '/' . $longitude;
                                    $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $latitude . ',' . $longitude;
                                @endphp
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ $osmExternalUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                        {{ __('admin.open_in_openstreetmap') }}
                                    </a>
                                    <a href="{{ $googleMapsUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                        {{ __('admin.open_in_google_maps') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(! is_null($service->latitude) && ! is_null($service->longitude))
                                <div class="review-map">
                                    <iframe
                                        title="{{ __('admin.service_location') }}"
                                        src="{{ $osmEmbedUrl }}"
                                        width="100%"
                                        height="100%"
                                        style="border: 0;"
                                        loading="lazy"></iframe>
                                </div>
                                <div class="review-location-meta mt-3">
                                    <span>{{ __('admin.address') }}: {{ $service->address ?: '-' }}</span>
                                    <span>{{ __('admin.latitude') }}: {{ $service->latitude }}</span>
                                    <span>{{ __('admin.longitude') }}: {{ $service->longitude }}</span>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">{{ __('admin.no_service_location') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.service_main_image') }}</div>
                        <div class="card-body">
                            @if($mainImage)
                                <div class="review-media-grid">
                                    <a href="{{ $mainImage->getUrl() }}" target="_blank" rel="noopener" class="review-media-tile">
                                        <img src="{{ $mainImage->getUrl() }}" alt="{{ $mainImage->name }}">
                                        <span>{{ $mainImage->name }}</span>
                                    </a>
                                </div>
                            @else
                                <div class="empty-state-text">{{ __('admin.none') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.service_gallery') }}</div>
                        <div class="card-body">
                            @if($galleryMedia->isNotEmpty())
                                <div class="review-media-grid">
                                    @foreach($galleryMedia as $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" rel="noopener" class="review-media-tile">
                                            <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}">
                                            <span>{{ $media->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state-text">{{ __('admin.none') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.dynamic_fields') }}</div>
                        <div class="card-body">
                            @if($dynamicValues->isNotEmpty())
                                <div class="review-detail-list">
                                    @foreach($dynamicValues as $item)
                                        <div class="review-detail-item">
                                            <span>{{ $item->dynamicField?->getTranslation('name', $locale, false) ?: $item->dynamicField?->field_key ?: __('admin.field_key') }}</span>
                                            <strong>{{ $formatDynamicValue($item) ?: '-' }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state-text">{{ __('admin.none') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card review-action-panel">
                <div class="card-header">{{ __('admin.review_actions') }}</div>
                <div class="card-body">
                    <div class="review-decision-state">
                        <span class="badge status-badge {{ $statusClass }}">{{ __('admin.' . $service->status) }}</span>
                        @if($service->status === 'rejected' && $service->rejection_reason)
                            <p class="mb-0 mt-2 text-danger">{{ $service->rejection_reason }}</p>
                        @endif
                    </div>

                    @if($service->status === 'pending')
                        @can('services.approve')
                            <form method="POST"
                                  action="{{ route('admin.services.approve', $service) }}"
                                  data-confirm="{{ __('admin.approve_service_confirmation') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 btn-with-icon">
                                    <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m20 6-11 11-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                    <span>{{ __('admin.approve_service') }}</span>
                                </button>
                            </form>
                        @endcan

                        @can('services.reject')
                            <form method="POST"
                                  action="{{ route('admin.services.reject', $service) }}"
                                  class="mt-3 review-reject-form"
                                  data-confirm="{{ __('admin.reject_service_confirmation') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 review-submit-button">
                                    {{ __('admin.reject_service') }}
                                </button>

                                <label class="form-label">{{ __('admin.rejection_reason') }}</label>
                                <textarea name="rejection_reason"
                                          rows="4"
                                          class="form-control @error('rejection_reason') is-invalid @enderror"
                                          placeholder="{{ __('admin.enter_rejection_reason') }}"
                                          required>{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </form>
                        @endcan
                    @else
                        <div class="alert alert-info mb-0">{{ __('admin.already_reviewed') }}</div>
                    @endif
                </div>
            </div>

            @if($service->status === 'approved')
                <div class="card review-card mt-4">
                    <div class="card-header">{{ __('admin.visibility_actions') }}</div>
                    <div class="card-body">
                        @if($service->is_active)
                            @can('services.deactivate')
                                <form method="POST"
                                      action="{{ route('admin.services.deactivate', $service) }}"
                                      data-confirm="{{ __('admin.deactivate_service_confirmation') }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-action btn-action-delete w-100">{{ __('admin.deactivate_service') }}</button>
                                </form>
                            @endcan
                        @else
                            @can('services.activate')
                                <form method="POST"
                                      action="{{ route('admin.services.activate', $service) }}"
                                      data-confirm="{{ __('admin.activate_service_confirmation') }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary w-100">{{ __('admin.activate_service') }}</button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            @endif

            <div class="card review-card mt-4">
                <div class="card-header">{{ __('admin.recent_activity') }}</div>
                <div class="card-body">
                    <div class="review-timeline">
                        <div class="review-timeline-item">
                            <span></span>
                            <div>
                                <strong>{{ __('admin.created_at') }}</strong>
                                <p>{{ optional($service->created_at)->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <div class="review-timeline-item">
                            <span></span>
                            <div>
                                <strong>{{ __('admin.status') }}</strong>
                                <p>{{ __('admin.' . $service->status) }}</p>
                            </div>
                        </div>
                        @if($service->reviewed_at)
                            <div class="review-timeline-item">
                                <span></span>
                                <div>
                                    <strong>{{ __('admin.reviewed_at') }}</strong>
                                    <p>{{ optional($service->reviewed_at)->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($service->published_at)
                            <div class="review-timeline-item">
                                <span></span>
                                <div>
                                    <strong>{{ __('admin.published_at') }}</strong>
                                    <p>{{ optional($service->published_at)->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card review-card mt-4">
                <div class="card-header">{{ __('admin.service_metrics') }}</div>
                <div class="card-body">
                    <div class="review-detail-list">
                        <div class="review-detail-item">
                            <span>{{ __('admin.average_rating') }}</span>
                            <strong>{{ $service->average_rating }}</strong>
                        </div>
                        <div class="review-detail-item">
                            <span>{{ __('admin.review_count') }}</span>
                            <strong>{{ $service->review_count }}</strong>
                        </div>
                        <div class="review-detail-item">
                            <span>{{ __('admin.views_count') }}</span>
                            <strong>{{ $service->views_count }}</strong>
                        </div>
                        <div class="review-detail-item">
                            <span>{{ __('admin.sort_order') }}</span>
                            <strong>{{ $service->sort_order }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
