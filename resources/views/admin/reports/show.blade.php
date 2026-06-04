@extends('layouts.admin')

@section('content')
@php
    $reportableTypeKey = 'admin.report_type_' . strtolower(class_basename($report->reportable_type));
    $reportableTypeLabel = __($reportableTypeKey);
    if ($reportableTypeLabel === $reportableTypeKey) {
        $reportableTypeLabel = class_basename($report->reportable_type);
    }

    $reportableUrl = null;
    $reportableTitle = __('admin.id') . ': ' . $report->reportable_id;

    if ($report->reportable instanceof \App\Models\Service) {
        $reportableUrl = route('admin.services.review.show', $report->reportable);
        $reportableTitle = $report->reportable->getTranslation('title', app()->getLocale(), false) ?: $reportableTitle;
    } elseif ($report->reportable instanceof \App\Models\BusinessAccount) {
        $reportableUrl = route('admin.business-accounts.show', $report->reportable);
        $reportableTitle = $report->reportable->getTranslation('name', app()->getLocale(), false) ?: $reportableTitle;
    }
@endphp

<div class="container-fluid admin-page">
    <x-admin.page-header
        :title="__('admin.report_number', ['id' => $report->id])"
        :subtitle="$report->reason"
        icon-name="reports">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-with-icon">
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M19 12H5m6-6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span>{{ __('admin.back') }}</span>
        </a>
    </x-admin.page-header>

    <div class="review-hero review-hero-{{ $report->status }} mb-4">
        <div class="review-hero-main">
            <span class="badge status-badge status-{{ $report->status }}">{{ __('admin.' . $report->status) }}</span>
            <h4 class="review-hero-title">{{ $report->reason }}</h4>
            <div class="review-hero-meta">
                <span>{{ __('admin.type') }}: {{ $reportableTypeLabel }}</span>
                <span>{{ __('admin.created_at') }}: {{ optional($report->created_at)->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.report_details') }}</div>
                        <div class="card-body">
                            <div class="review-detail-list">
                                <div class="review-detail-item">
                                    <span>{{ __('admin.reason') }}</span>
                                    <strong>{{ $report->reason }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.type') }}</span>
                                    <strong>{{ $reportableTypeLabel }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.status') }}</span>
                                    <strong><span class="badge status-badge status-{{ $report->status }}">{{ __('admin.' . $report->status) }}</span></strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.created_at') }}</span>
                                    <strong>{{ optional($report->created_at)->format('Y-m-d H:i') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card review-card h-100">
                        <div class="card-header">{{ __('admin.reported_by') }}</div>
                        <div class="card-body">
                            <div class="review-owner">
                                <div class="review-owner-avatar">{{ mb_substr($report->reporterBusinessAccount?->getTranslation('name', app()->getLocale()) ?? 'R', 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold">{{ $report->reporterBusinessAccount?->getTranslation('name', app()->getLocale()) ?: '-' }}</div>
                                    <div class="text-muted small">{{ __('admin.id') }}: {{ $report->reporter_business_account_id }}</div>
                                    @if($report->reporterBusinessAccount)
                                        <a href="{{ route('admin.business-accounts.show', $report->reporterBusinessAccount) }}" class="small">{{ __('admin.open') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.description') }}</div>
                        <div class="card-body">
                            <p class="review-description mb-0">{{ $report->description ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card review-card">
                        <div class="card-header">{{ __('admin.reported_item') }}</div>
                        <div class="card-body">
                            <div class="review-detail-list">
                                <div class="review-detail-item">
                                    <span>{{ __('admin.type') }}</span>
                                    <strong>{{ $reportableTypeLabel }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.title') }}</span>
                                    <strong>{{ $reportableTitle }}</strong>
                                </div>
                                <div class="review-detail-item">
                                    <span>{{ __('admin.reportable_id') }}</span>
                                    <strong>{{ $report->reportable_id }}</strong>
                                </div>
                            </div>

                            @if($reportableUrl)
                                <a href="{{ $reportableUrl }}" class="btn btn-outline-primary mt-3">
                                    {{ __('admin.view_reported_item') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            @can('reports.manage')
                <div class="card review-action-panel">
                    <div class="card-header">{{ __('admin.update_status') }}</div>
                    <div class="card-body">
                        <div class="review-decision-state">
                            <span class="badge status-badge status-{{ $report->status }}">{{ __('admin.' . $report->status) }}</span>
                        </div>

                        <form method="POST" action="{{ route('admin.reports.status', $report) }}" class="review-reject-form">
                            @csrf
                            @method('PATCH')

                            <label class="form-label">{{ __('admin.status') }}</label>
                            <select name="status" class="form-select" required>
                                @foreach(['reviewed', 'resolved', 'rejected'] as $status)
                                    <option value="{{ $status }}" {{ $report->status === $status ? 'selected' : '' }}>
                                        {{ __('admin.' . $status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-primary w-100 review-submit-button">
                                {{ __('admin.update_status') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endcan

            <div class="card review-card mt-4">
                <div class="card-header">{{ __('admin.recent_activity') }}</div>
                <div class="card-body">
                    <div class="review-timeline">
                        <div class="review-timeline-item">
                            <span></span>
                            <div>
                                <strong>{{ __('admin.created_at') }}</strong>
                                <p>{{ optional($report->created_at)->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <div class="review-timeline-item">
                            <span></span>
                            <div>
                                <strong>{{ __('admin.status') }}</strong>
                                <p>{{ __('admin.' . $report->status) }}</p>
                            </div>
                        </div>
                        @if($report->reviewed_at)
                            <div class="review-timeline-item">
                                <span></span>
                                <div>
                                    <strong>{{ __('admin.reviewed_at') }}</strong>
                                    <p>{{ optional($report->reviewed_at)->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($report->reviewedByAdmin)
                            <div class="review-timeline-item">
                                <span></span>
                                <div>
                                    <strong>{{ __('admin.reviewed_by') }}</strong>
                                    <p>{{ $report->reviewedByAdmin->name }}</p>
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
