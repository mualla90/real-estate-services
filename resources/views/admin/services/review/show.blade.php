@extends('layouts.admin')

@section('title', __('admin.service_review'))

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.service_details')" icon-name="services">
        <a href="{{ route('admin.services.review.index') }}" class="btn btn-outline-secondary">{{ __('admin.back_to_list') }}</a>
    </x-admin.page-header>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="details-grid">
                        <div class="detail-row"><div class="detail-label">ID</div><div class="detail-value">{{ $service->id }}</div></div>
                        <div class="detail-row">
                            <div class="detail-label">{{ __('admin.business_account') }}</div>
                            <div class="detail-value">
                                #{{ $service->business_account_id }}
                                @if($service->businessAccount)
                                    <br>{{ $service->businessAccount->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                                @endif
                            </div>
                        </div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.category') }}</div><div class="detail-value">{{ $service->category?->getTranslation('name', app()->getLocale(), false) ?? '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.subcategory') }}</div><div class="detail-value">{{ $service->subcategory?->getTranslation('name', app()->getLocale(), false) ?? '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.city') }}</div><div class="detail-value">{{ $service->city?->getTranslation('name', app()->getLocale(), false) ?? '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.title') }}</div><div class="detail-value">{{ $service->getTranslation('title', app()->getLocale(), false) ?? '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.description') }}</div><div class="detail-value">{{ $service->getTranslation('description', app()->getLocale(), false) ?? '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.service_type') }}</div><div class="detail-value">{{ ucfirst($service->service_type) }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.price') }}</div><div class="detail-value">{{ $service->price }} {{ $service->currency }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.address') }}</div><div class="detail-value">{{ $service->address ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.latitude') }}</div><div class="detail-value">{{ $service->latitude ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.longitude') }}</div><div class="detail-value">{{ $service->longitude ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.status') }}</div><div class="detail-value"><span class="badge status-badge status-{{ $service->status }}">{{ __('admin.' . $service->status) }}</span></div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.active') }}</div><div class="detail-value">@if($service->is_active)<span class="badge status-badge status-active">{{ __('admin.active') }}</span>@else<span class="badge status-badge status-inactive">{{ __('admin.inactive') }}</span>@endif</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.rejection_reason') }}</div><div class="detail-value">{{ $service->rejection_reason ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.reviewed_by_admin') }}</div><div class="detail-value">{{ $service->reviewed_by_admin_id ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.reviewed_at') }}</div><div class="detail-value">{{ $service->reviewed_at?->format('Y-m-d H:i') ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.published_at') }}</div><div class="detail-value">{{ $service->published_at?->format('Y-m-d H:i') ?: '-' }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.average_rating') }}</div><div class="detail-value">{{ $service->average_rating }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.review_count') }}</div><div class="detail-value">{{ $service->review_count }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.views_count') }}</div><div class="detail-value">{{ $service->views_count }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.sort_order') }}</div><div class="detail-value">{{ $service->sort_order }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.created_at') }}</div><div class="detail-value">{{ $service->created_at?->format('Y-m-d H:i') }}</div></div>
                        <div class="detail-row"><div class="detail-label">{{ __('admin.updated_at') }}</div><div class="detail-value">{{ $service->updated_at?->format('Y-m-d H:i') }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card admin-action-card mb-4">
                <div class="card-header"><strong>{{ __('admin.review_actions') }}</strong></div>
                <div class="card-body">
                    @if($service->status === 'pending')
                        @can('services.approve')
                            <form method="POST"
                                  action="{{ route('admin.services.approve', $service) }}"
                                  data-confirm="{{ __('admin.approve_service_confirmation') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">{{ __('admin.approve_service') }}</button>
                            </form>
                        @endcan

                        @can('services.reject')
                            <form method="POST"
                                  action="{{ route('admin.services.reject', $service) }}"
                                  data-confirm="{{ __('admin.reject_service_confirmation') }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">{{ __('admin.rejection_reason') }}</label>
                                    <textarea name="rejection_reason" rows="4" class="form-control @error('rejection_reason') is-invalid @enderror">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-action btn-action-delete w-100">{{ __('admin.reject_service') }}</button>
                            </form>
                        @endcan
                    @else
                        <div class="alert alert-info mb-0">{{ __('admin.already_reviewed') }}</div>
                    @endif
                </div>
            </div>

            @if($service->status === 'approved')
                <div class="card admin-action-card">
                    <div class="card-header"><strong>{{ __('admin.visibility_actions') }}</strong></div>
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
        </div>
    </div>
</div>
@endsection
