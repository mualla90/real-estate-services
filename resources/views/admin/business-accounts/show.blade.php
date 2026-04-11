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
