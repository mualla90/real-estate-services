@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.reports')" icon-name="reports" />

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.search') }}</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
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
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('admin.filter') }}</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary w-100">{{ __('admin.reset') }}</a>
                </div>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.reason') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.reported_by') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                    <th width="120">{{ __('admin.actions') }}</th>
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
                    @endphp
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>{{ $reportableTypeLabel }}</td>
                        <td>{{ $report->reason }}</td>
                        <td>
                            <span class="badge status-badge status-{{ $report->status }}">
                                {{ __('admin.' . $report->status) }}
                            </span>
                        </td>
                        <td>{{ $report->reporterBusinessAccount?->getTranslation('name', app()->getLocale()) }}</td>
                        <td>{{ $report->created_at }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-action btn-action-view">{{ __('admin.view') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_reports_found')" :colspan="7" />
                @endforelse
            </tbody>
        </table>

        {{ $reports->links() }}
    </x-admin.table-card>
</div>
@endsection

