@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    @php
        $reportableTypeKey = 'admin.report_type_' . strtolower(class_basename($report->reportable_type));
        $reportableTypeLabel = __($reportableTypeKey);
        if ($reportableTypeLabel === $reportableTypeKey) {
            $reportableTypeLabel = class_basename($report->reportable_type);
        }
    @endphp

    <x-admin.page-header :title="__('admin.report_number', ['id' => $report->id])" icon-name="reports">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
    </x-admin.page-header>

    <div class="card mb-3">
        <div class="card-body">
            <div class="details-grid">
                <div class="detail-row"><div class="detail-label">{{ __('admin.type') }}</div><div class="detail-value">{{ $reportableTypeLabel }}</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.status') }}</div><div class="detail-value"><span class="badge status-badge status-{{ $report->status }}">{{ __('admin.' . $report->status) }}</span></div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.created_at') }}</div><div class="detail-value">{{ $report->created_at }}</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.reason') }}</div><div class="detail-value">{{ $report->reason }}</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.description') }}</div><div class="detail-value">{{ $report->description ?: '-' }}</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.reported_by') }}</div><div class="detail-value">{{ $report->reporterBusinessAccount?->getTranslation('name', app()->getLocale()) }} ({{ __('admin.id') }}: {{ $report->reporter_business_account_id }})</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.reportable_id') }}</div><div class="detail-value">{{ $report->reportable_id }}</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.reviewed_by') }}</div><div class="detail-value">{{ $report->reviewedByAdmin?->name ?: '-' }}</div></div>
                <div class="detail-row"><div class="detail-label">{{ __('admin.reviewed_at') }}</div><div class="detail-value">{{ $report->reviewed_at ?: '-' }}</div></div>
            </div>
        </div>
    </div>

    @can('reports.manage')
        <div class="card admin-form-card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.reports.status', $report) }}">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">{{ __('admin.update_status') }}</label>
                            <select name="status" class="form-select" required>
                                @foreach(['reviewed', 'resolved', 'rejected'] as $status)
                                    <option value="{{ $status }}" {{ $report->status === $status ? 'selected' : '' }}>
                                        {{ __('admin.' . $status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">{{ __('admin.update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
</div>
@endsection
