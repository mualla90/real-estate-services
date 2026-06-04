@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.reports')" icon-name="reports" />

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">{{ __('admin.search') }}</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.search_reason_or_description') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('admin.status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach(['pending', 'reviewed', 'resolved', 'rejected'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ __('admin.' . $status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('admin.filter') }}</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset') }}</a>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <div class="table-responsive">
            <table class="table table-bordered align-middle report-review-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.report_details') }}</th>
                        <th>{{ __('admin.reported_item') }}</th>
                        <th>{{ __('admin.reported_by') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.created_at') }}</th>
                        <th width="150">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        @php
                            $reportableTypeKey = 'admin.report_type_' . strtolower(class_basename($report->reportable_type));
                            $reportableTypeLabel = __($reportableTypeKey);
                            if ($reportableTypeLabel === $reportableTypeKey) {
                                $reportableTypeLabel = class_basename($report->reportable_type);
                            }

                            $reportableUrl = null;
                            if ($report->reportable instanceof \App\Models\Service) {
                                $reportableUrl = route('admin.services.review.show', $report->reportable);
                            } elseif ($report->reportable instanceof \App\Models\BusinessAccount) {
                                $reportableUrl = route('admin.business-accounts.show', $report->reportable);
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="service-review-title">{{ $report->reason }}</div>
                                <div class="service-review-meta">
                                    <span>{{ __('admin.id') }}: {{ $report->id }}</span>
                                    @if($report->description)
                                        <span>{{ Str::limit($report->description, 72) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="service-review-title">{{ $reportableTypeLabel }}</div>
                                <div class="service-review-meta">{{ __('admin.id') }}: {{ $report->reportable_id }}</div>
                                @if($reportableUrl)
                                    <a href="{{ $reportableUrl }}" class="service-review-link small">{{ __('admin.view_reported_item') }}</a>
                                @endif
                            </td>
                            <td>
                                <div class="service-review-title">{{ $report->reporterBusinessAccount?->getTranslation('name', app()->getLocale()) ?: '-' }}</div>
                                <div class="service-review-meta">{{ __('admin.id') }}: {{ $report->reporter_business_account_id }}</div>
                            </td>
                            <td>
                                <span class="badge status-badge status-{{ $report->status }}">
                                    {{ __('admin.' . $report->status) }}
                                </span>
                            </td>
                            <td>{{ optional($report->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="table-actions table-actions-stack">
                                    <a href="{{ route('admin.reports.show', $report) }}"
                                       class="btn btn-sm btn-action btn-action-view w-100">
                                        {{ __('admin.view') }}
                                    </a>
                                    @if($report->status === 'pending')
                                        @can('reports.manage')
                                            <form method="POST"
                                                  action="{{ route('admin.reports.status', $report) }}"
                                                  class="w-100"
                                                  data-confirm="{{ __('admin.mark_report_reviewed_confirmation') }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="reviewed">
                                                <button type="submit" class="btn btn-sm btn-action btn-action-open w-100">
                                                    {{ __('admin.reviewed') }}
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-admin.empty-state :message="__('admin.no_reports_found')" :colspan="6" />
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    </x-admin.table-card>
</div>
@endsection
