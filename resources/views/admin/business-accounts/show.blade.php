@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">

    <x-admin.page-header :title="__('admin.business_account_details')" icon-name="business">
        <a href="{{ route('admin.business-accounts.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
    </x-admin.page-header>

    <div class="card">
        <div class="card-body">
            <div class="details-grid">
                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.name') }}</div>
                    <div class="detail-value">{{ $businessAccount->getTranslation('name', app()->getLocale()) }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.license_number') }}</div>
                    <div class="detail-value">{{ $businessAccount->license_number }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.user') }}</div>
                    <div class="detail-value">{{ $businessAccount->user->name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.city') }}</div>
                    <div class="detail-value">{{ $businessAccount->city?->getTranslation('name', app()->getLocale()) }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.activity_type') }}</div>
                    <div class="detail-value">{{ $businessAccount->activityType?->getTranslation('name', app()->getLocale()) }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.description') }}</div>
                    <div class="detail-value">{{ $businessAccount->getTranslation('description', app()->getLocale()) }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.address') }}</div>
                    <div class="detail-value">{{ $businessAccount->address ?: '-' }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.latitude') }}</div>
                    <div class="detail-value">{{ $businessAccount->latitude ?: '-' }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.longitude') }}</div>
                    <div class="detail-value">{{ $businessAccount->longitude ?: '-' }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.status') }}</div>
                    <div class="detail-value">
                        <span class="badge status-badge status-{{ $businessAccount->status }}">{{ __('admin.' . $businessAccount->status) }}</span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">{{ __('admin.created_at') }}</div>
                    <div class="detail-value">{{ $businessAccount->created_at->format('Y-m-d') }}</div>
                </div>

                @if($businessAccount->status === 'rejected' && $businessAccount->rejection_reason)
                    <div class="detail-row">
                        <div class="detail-label">{{ __('admin.rejection_reason') }}</div>
                        <div class="detail-value">{{ $businessAccount->rejection_reason }}</div>
                    </div>
                @endif
            </div>

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

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <h5 class="mb-0">{{ __('admin.business_account_location') }}</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ $osmExternalUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                {{ __('admin.open_in_openstreetmap') }}
                            </a>
                            <a href="{{ $googleMapsUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                {{ __('admin.open_in_google_maps') }}
                            </a>
                        </div>
                    </div>

                    <div style="height: 320px; overflow: hidden; border-radius: 16px; border: 1px solid rgba(0, 0, 0, .1);">
                        <iframe
                            title="{{ __('admin.business_account_location') }}"
                            src="{{ $osmEmbedUrl }}"
                            width="100%"
                            height="100%"
                            style="border: 0;"
                            loading="lazy"></iframe>
                    </div>
                </div>
            @else
                <div class="alert alert-info mt-4 mb-0">
                    {{ __('admin.no_business_account_location') }}
                </div>
            @endif

            @if($businessAccount->getMedia('images')->isNotEmpty())
                <div class="mt-4">
                    <h5>{{ __('admin.business_account_images') }}</h5>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($businessAccount->getMedia('images') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank" rel="noopener" class="d-inline-block">
                                <img src="{{ $media->getUrl() }}"
                                     alt="{{ $media->name }}"
                                     style="width: 140px; height: 100px; object-fit: cover; border-radius: 12px;">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($businessAccount->getMedia('documents')->isNotEmpty())
                <div class="mt-4">
                    <h5>{{ __('admin.business_account_documents') }}</h5>
                    <div class="list-group">
                        @foreach($businessAccount->getMedia('documents') as $media)
                            <a href="{{ $media->getUrl() }}"
                               target="_blank"
                               rel="noopener"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>{{ $media->name }}</span>
                                <span class="badge bg-secondary">{{ $media->mime_type }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($businessAccount->status === 'pending')
                <div class="form-actions-sticky mt-4">
                    <form method="POST"
                          action="{{ route('admin.business-accounts.approve', $businessAccount) }}"
                          class="d-inline"
                          data-confirm="{{ __('admin.approve_business_account_confirmation') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary">{{ __('admin.approve') }}</button>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.business-accounts.reject', $businessAccount) }}"
                          class="d-flex gap-2 align-items-start flex-wrap"
                          data-confirm="{{ __('admin.reject_business_account_confirmation') }}">
                        @csrf
                        @method('PATCH')

                        <input type="text"
                               name="rejection_reason"
                               class="form-control"
                               style="min-width: 260px;"
                               placeholder="{{ __('admin.enter_rejection_reason') }}"
                               required>

                        <button type="submit" class="btn btn-action btn-action-delete">{{ __('admin.reject') }}</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
