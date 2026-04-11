@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page"
     data-dashboard-stats-url="{{ route('admin.dashboard.stats') }}"
     data-dashboard-stats-interval="25000">
    <x-admin.page-header :title="__('admin.dashboard')" :subtitle="__('admin.dashboard_overview')" icon-name="dashboard" />

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="card metric-card h-100 dashboard-animate" style="--delay: 0s;">
                <div class="card-body">
                    <div class="metric-label">{{ __('admin.admins') }}</div>
                    <div class="metric-value" data-live-stat="admins">{{ number_format($stats['admins']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card metric-card h-100 dashboard-animate" style="--delay: .06s;">
                <div class="card-body">
                    <div class="metric-label">{{ __('admin.pending') }} {{ __('admin.business_accounts') }}</div>
                    <div class="metric-value" data-live-stat="pending_business_accounts">{{ number_format($stats['pending_business_accounts']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card metric-card h-100 dashboard-animate" style="--delay: .12s;">
                <div class="card-body">
                    <div class="metric-label">{{ __('admin.pending') }} {{ __('admin.services') }}</div>
                    <div class="metric-value" data-live-stat="pending_services">{{ number_format($stats['pending_services']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4 dashboard-animate" style="--delay: .18s;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ __('admin.needs_attention') }}</strong>
        </div>
        <div class="card-body">
            <div class="attention-grid">
                <a href="{{ route('admin.business-accounts.index', ['status' => 'pending']) }}" class="attention-item">
                    <span class="attention-label">{{ __('admin.pending') }} {{ __('admin.business_accounts') }}</span>
                    <span class="badge status-badge status-pending" data-live-stat="pending_business_accounts">{{ number_format($stats['pending_business_accounts']) }}</span>
                </a>

                <a href="{{ route('admin.services.review.index', ['status' => 'pending']) }}" class="attention-item">
                    <span class="attention-label">{{ __('admin.pending') }} {{ __('admin.services') }}</span>
                    <span class="badge status-badge status-pending" data-live-stat="pending_services">{{ number_format($stats['pending_services']) }}</span>
                </a>

                <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="attention-item">
                    <span class="attention-label">{{ __('admin.pending') }} {{ __('admin.reports') }}</span>
                    <span class="badge status-badge status-pending" data-live-stat="pending_reports">{{ number_format($stats['pending_reports']) }}</span>
                </a>

                <a href="{{ route('admin.notifications.index', ['unread_only' => 1]) }}" class="attention-item">
                    <span class="attention-label">{{ __('admin.unread') }} {{ __('admin.notifications') }}</span>
                    <span class="badge status-badge status-unread" data-live-stat="unread_notifications">{{ number_format($stats['unread_notifications']) }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="card mt-4 dashboard-animate" style="--delay: .2s;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ __('admin.quick_actions') }}</strong>
        </div>
        <div class="card-body">
            <div class="quick-actions-grid">
                @can('business-accounts.approve')
                    <a href="{{ route('admin.business-accounts.index', ['status' => 'pending']) }}" class="btn btn-primary quick-action-btn">
                        <span class="quick-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                        <span>{{ __('admin.review_business_accounts') }}</span>
                    </a>
                @endcan

                @can('services.approve')
                    <a href="{{ route('admin.services.review.index', ['status' => 'pending']) }}" class="btn btn-outline-primary quick-action-btn">
                        <span class="quick-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m20 6-11 11-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span>{{ __('admin.review_services') }}</span>
                    </a>
                @endcan

                @can('reports.manage')
                    <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="btn btn-outline-secondary quick-action-btn">
                        <span class="quick-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6zM9 13h6M9 17h6M9 9h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span>{{ __('admin.review_reports') }}</span>
                    </a>
                @endcan

                @can('notifications.manage')
                    <form method="POST" action="{{ route('admin.notifications.read-all') }}" data-confirm="{{ __('admin.confirm_mark_all_read') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary quick-action-btn w-100">
                            <span class="quick-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5v14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                            <span>{{ __('admin.mark_all_read') }}</span>
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="card mt-4 dashboard-animate" style="--delay: .22s;">
        <div class="card-body">
            <h5 class="mb-2 fw-semibold">{{ __('admin.navigation') }}</h5>
            <p class="text-muted mb-2">{{ __('admin.dashboard_navigation_hint') }}</p>
            <p class="text-muted mb-0">
                {{ __('admin.notifications') }}: <strong data-live-stat="unread_notifications">{{ number_format($stats['unread_notifications']) }}</strong>
            </p>
        </div>
    </div>

    <div class="card mt-4 dashboard-animate" style="--delay: .26s;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ __('admin.recent_activity') }}</strong>
        </div>
        <div class="card-body">
            @if($recentActivities->isEmpty())
                <p class="text-muted mb-0">{{ __('admin.no_recent_activity') }}</p>
            @else
                <div class="activity-timeline">
                    @foreach($recentActivities as $activity)
                        <a href="{{ $activity['url'] }}" class="activity-item">
                            <span class="activity-dot activity-dot-{{ $activity['type'] }}" aria-hidden="true"></span>
                            <span class="activity-text">{{ $activity['title'] }}</span>
                            <span class="activity-time">{{ $activity['time']->diffForHumans() }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
