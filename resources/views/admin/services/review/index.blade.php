@extends('layouts.admin')

@section('title', __('admin.service_review'))

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.service_review')" icon-name="services" />

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.services.review.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.search') }}</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.search_service') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('admin.status') }}</label>
                <select name="status" class="form-select">
                    <option value="" {{ request()->has('status') && request('status') === '' ? 'selected' : '' }}>
                        {{ __('admin.all') }}
                    </option>
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>
                        {{ __('admin.pending') }}
                    </option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                        {{ __('admin.approved') }}
                    </option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                        {{ __('admin.rejected') }}
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('admin.visibility') }}</label>
                <select name="is_active" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                        {{ __('admin.active') }}
                    </option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                        {{ __('admin.inactive') }}
                    </option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">{{ __('admin.business_account_id') }}</label>
                <input type="text"
                       name="business_account_id"
                       class="form-control"
                       value="{{ request('business_account_id') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">{{ __('admin.category_id') }}</label>
                <input type="text"
                       name="category_id"
                       class="form-control"
                       value="{{ request('category_id') }}">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ __('admin.filter') }}
                </button>

                <a href="{{ route('admin.services.review.index') }}" class="btn btn-outline-secondary">
                    {{ __('admin.reset') }}
                </a>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <div class="table-responsive">
            <table class="table table-bordered align-middle service-review-table">
                <thead>
                    <tr>
                        <th width="86">{{ __('admin.service_main_image') }}</th>
                        <th>{{ __('admin.service_details') }}</th>
                        <th>{{ __('admin.business_account') }}</th>
                        <th>{{ __('admin.category') }}</th>
                        <th>{{ __('admin.city') }}</th>
                        <th>{{ __('admin.price') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.created_at') }}</th>
                        <th width="150">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        @php
                            $mainImage = $service->getFirstMedia('main_image');
                            $title = $service->getTranslation('title', app()->getLocale(), false) ?: '-';
                        @endphp
                        <tr>
                            <td>
                                @if($mainImage)
                                    <a href="{{ route('admin.services.review.show', $service) }}" class="service-review-thumb">
                                        <img src="{{ $mainImage->getUrl() }}" alt="{{ $mainImage->name }}">
                                    </a>
                                @else
                                    <a href="{{ route('admin.services.review.show', $service) }}" class="service-review-thumb service-review-thumb-empty">
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="service-review-title">{{ $title }}</div>
                                <div class="service-review-meta">
                                    <span>{{ __('admin.id') }}: {{ $service->id }}</span>
                                    <span>{{ __('admin.type') }}: {{ $service->service_type ? ucfirst($service->service_type) : '-' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($service->businessAccount)
                                    <a href="{{ route('admin.business-accounts.show', $service->businessAccount) }}" class="service-review-link">
                                        {{ $service->businessAccount->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                                    </a>
                                    <div class="service-review-meta">{{ __('admin.id') }}: {{ $service->business_account_id }}</div>
                                @else
                                    {{ $service->business_account_id }}
                                @endif
                            </td>
                            <td>
                                {{ $service->category?->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                            </td>
                            <td>
                                {{ $service->city?->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                            </td>
                            <td>
                                <div class="service-review-price">{{ $service->price_usd ?? '-' }} USD</div>
                                <div class="service-review-meta">{{ $service->price_syp ?? '-' }} SYP</div>
                            </td>
                            <td>
                                <span class="badge status-badge status-{{ $service->status }}">
                                    {{ __('admin.' . $service->status) }}
                                </span>
                                <br>
                                @if($service->is_active)
                                    <span class="badge status-badge status-active">{{ __('admin.active') }}</span>
                                @else
                                    <span class="badge status-badge status-inactive">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $service->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="table-actions table-actions-stack">
                                    <a href="{{ route('admin.services.review.show', $service) }}"
                                       class="btn btn-sm btn-action btn-action-view w-100">
                                        {{ __('admin.view') }}
                                    </a>
                                    @if($service->status === 'pending')
                                        @can('services.approve')
                                            <form method="POST"
                                                  action="{{ route('admin.services.approve', $service) }}"
                                                  class="w-100"
                                                  data-confirm="{{ __('admin.approve_service_confirmation') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-action btn-action-open w-100">
                                                    {{ __('admin.approve') }}
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-admin.empty-state :message="__('admin.no_services_found')" :colspan="9" />
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $services->links() }}
        </div>
    </x-admin.table-card>
</div>
@endsection
