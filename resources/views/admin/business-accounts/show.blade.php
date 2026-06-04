@extends('layouts.admin')

@section('content')
@php
    $locale = app()->getLocale();
    $displayName = $businessAccount->getTranslation('name', $locale);
    $description = $businessAccount->getTranslation('description', $locale);
    $statusClass = 'status-' . $businessAccount->status;
    $imageMedia = $businessAccount->getMedia('images');
    $documentMedia = $businessAccount->getMedia('documents');
@endphp

<div class="container-fluid admin-page">
    <x-admin.page-header
        :title="__('admin.business_account_details')"
        :subtitle="$displayName"
        icon-name="business">
        <a href="{{ route('admin.business-accounts.index') }}" class="btn btn-outline-secondary btn-with-icon">
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M19 12H5m6-6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span>{{ __('admin.back') }}</span>
        </a>
    </x-admin.page-header>

    <div class="review-hero review-hero-{{ $businessAccount->status }} mb-4">
        <div class="review-hero-main">
            <span class="badge status-badge {{ $statusClass }}">{{ __('admin.' . $businessAccount->status) }}</span>
            <h4 class="review-hero-title">{{ $displayName }}</h4>
            <div class="review-hero-meta">
                <span>{{ __('admin.license_number') }}: {{ $businessAccount->license_number }}</span>
                <span>{{ __('admin.created_at') }}: {{ optional($businessAccount->created_at)->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="review-hero-stats">
            <div class="review-stat">
                <span class="review-stat-value">{{ $imageMedia->count() }}</span>
                <span class="review-stat-label">{{ __('admin.business_account_images') }}</span>
            </div>
            <div class="review-stat">
                <span class="review-stat-value">{{ $documentMedia->count() }}</span>
                <span class="review-stat-label">{{ __('admin.business_account_documents') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.business_account') }}</div>
                        <div class="card-body">
                            <div class="review-detail-list">
                                <div class="review-detail-item">
                                    <span>{{ __('admin.name') }}</span>
                                    <strong>{{ $displayName }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.activity_type') }}</span>
                                    <strong>{{ $businessAccount->activityType?->getTranslation('name', $locale) ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.city') }}</span>
                                    <strong>{{ $businessAccount->city?->getTranslation('name', $locale) ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.email') }}</span>
                                    <strong>{{ $businessAccount->email ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.phone') }}</span>
                                    <strong>{{ $businessAccount->phone ?: '-' }}</strong>
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
                                <div class="review-owner-avatar">{{ mb_substr($businessAccount->user?->name ?? 'U', 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold">{{ $businessAccount->user?->name ?: '-' }}</div>
                                    <div class="text-muted small">{{ $businessAccount->user?->phone ?: '-' }}</div>
                                    <div class="text-muted small">{{ $businessAccount->user?->email ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="review-detail-list mt-3">
                                <div class="review-detail-item">
                                    <span>{{ __('admin.reviewed_by_admin') }}</span>
                                    <strong>{{ $businessAccount->reviewedByAdmin?->name ?: '-' }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.reviewed_at') }}</span>
                                    <strong>{{ optional($businessAccount->reviewed_at)->format('Y-m-d H:i') ?: '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.description') }}</div>
                        <div class="card-body">
                            <p class="review-description mb-0">{{ $description ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header d-flex justify-content-between align-items-center gap-2">
                            <span>{{ __('admin.business_account_location') }}</span>
                            @if(! is_null($businessAccount->latitude) && ! is_null($businessAccount->longitude))
                                @php
                                    $latitude = (float) $businessAccount->latitude;
                                    $longitude = (float) $businessAccount->longitude;
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
                            @if(! is_null($businessAccount->latitude) && ! is_null($businessAccount->longitude))
                                <div class="review-map">
                                    <iframe
                                        title="{{ __('admin.business_account_location') }}"
                                        src="{{ $osmEmbedUrl }}"
                                        width="100%"
                                        height="100%"
                                        style="border: 0;"
                                        loading="lazy"></iframe>
                                </div>
                                <div class="review-location-meta mt-3">
                                    <span>{{ __('admin.address') }}: {{ $businessAccount->address ?: '-' }}</span>
                                    <span>{{ __('admin.latitude') }}: {{ $businessAccount->latitude }}</span>
                                    <span>{{ __('admin.longitude') }}: {{ $businessAccount->longitude }}</span>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">{{ __('admin.no_business_account_location') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.business_account_images') }}</div>
                        <div class="card-body">
                            @if($imageMedia->isNotEmpty())
                                <div class="review-media-grid">
                                    @foreach($imageMedia as $media)
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
                        <div class="card-header">{{ __('admin.business_account_documents') }}</div>
                        <div class="card-body">
                            @if($documentMedia->isNotEmpty())
                                <div class="review-document-list">
                                    @foreach($documentMedia as $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" rel="noopener" class="review-document-item">
                                            <span class="review-document-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6zM14 4v4h4M9 13h6M9 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                            <span class="review-document-body">
                                                <strong>{{ $media->name }}</strong>
                                                <small>{{ $media->mime_type }}</small>
                                            </span>
                                        </a>
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
                        <span class="badge status-badge {{ $statusClass }}">{{ __('admin.' . $businessAccount->status) }}</span>
                        @if($businessAccount->status === 'rejected' && $businessAccount->rejection_reason)
                            <p class="mb-0 mt-2 text-danger">{{ $businessAccount->rejection_reason }}</p>
                        @endif
                    </div>

                    @if($businessAccount->status === 'pending')
                        <form method="POST"
                              action="{{ route('admin.business-accounts.approve', $businessAccount) }}"
                              data-confirm="{{ __('admin.approve_business_account_confirmation') }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary w-100 btn-with-icon">
                                <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m20 6-11 11-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <span>{{ __('admin.approve') }}</span>
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.business-accounts.reject', $businessAccount) }}"
                              class="mt-3 review-reject-form"
                              data-confirm="{{ __('admin.reject_business_account_confirmation') }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit" class="btn btn-danger w-100 review-submit-button">
                                {{ __('admin.reject') }}
                            </button>

                            <label class="form-label">{{ __('admin.rejection_reason') }}</label>
                            <textarea name="rejection_reason"
                                      class="form-control"
                                      rows="4"
                                      placeholder="{{ __('admin.enter_rejection_reason') }}"
                                      required></textarea>
                        </form>
                    @else
                        <div class="alert alert-info mb-0">{{ __('admin.already_reviewed') }}</div>
                    @endif
                </div>
            </div>

            <div class="card review-card mt-4">
                <div class="card-header">{{ __('admin.recent_activity') }}</div>
                <div class="card-body">
                    <div class="review-timeline">
                        <div class="review-timeline-item">
                            <span></span>
                            <div>
                                <strong>{{ __('admin.created_at') }}</strong>
                                <p>{{ optional($businessAccount->created_at)->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <div class="review-timeline-item">
                            <span></span>
                            <div>
                                <strong>{{ __('admin.status') }}</strong>
                                <p>{{ __('admin.' . $businessAccount->status) }}</p>
                            </div>
                        </div>
                        @if($businessAccount->reviewed_at)
                            <div class="review-timeline-item">
                                <span></span>
                                <div>
                                    <strong>{{ __('admin.reviewed_at') }}</strong>
                                    <p>{{ optional($businessAccount->reviewed_at)->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
