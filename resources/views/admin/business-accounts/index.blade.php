@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.business_accounts')" icon-name="business" />

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.business-accounts.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.search') }}</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="{{ __('admin.search_business_account') }}"
                    value="{{ request('search') }}"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">{{ __('admin.status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    <option value="pending" @selected(request('status') === 'pending')>{{ __('admin.pending') }}</option>
                    <option value="approved" @selected(request('status') === 'approved')>{{ __('admin.approved') }}</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>{{ __('admin.rejected') }}</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('admin.city') }}</label>
                <select name="city_id" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected((string) request('city_id') === (string) $city->id)>
                            {{ $city->getTranslation('name', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('admin.activity_type') }}</label>
                <select name="activity_type_id" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($activityTypes as $activityType)
                        <option value="{{ $activityType->id }}" @selected((string) request('activity_type_id') === (string) $activityType->id)>
                            {{ $activityType->getTranslation('name', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ __('admin.filter') }}
                </button>

                <a href="{{ route('admin.business-accounts.index') }}" class="btn btn-outline-secondary">
                    {{ __('admin.reset') }}
                </a>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <div class="table-responsive">
            <table class="table table-bordered align-middle business-review-table">
                <thead>
                    <tr>
                        <th width="86">{{ __('admin.business_account_images') }}</th>
                        <th>{{ __('admin.business_account') }}</th>
                        <th>{{ __('admin.user') }}</th>
                        <th>{{ __('admin.city') }}</th>
                        <th>{{ __('admin.activity_type') }}</th>
                        <th>{{ __('admin.business_account_documents') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.created_at') }}</th>
                        <th width="150">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($businessAccounts as $account)
                        @php
                            $imageMedia = $account->getMedia('images');
                            $documentMedia = $account->getMedia('documents');
                            $firstImage = $imageMedia->first();
                            $displayName = $account->getTranslation('name', app()->getLocale());
                        @endphp
                        <tr>
                            <td>
                                @if($firstImage)
                                    <a href="{{ route('admin.business-accounts.show', $account) }}" class="service-review-thumb">
                                        <img src="{{ $firstImage->getUrl() }}" alt="{{ $firstImage->name }}">
                                    </a>
                                @else
                                    <a href="{{ route('admin.business-accounts.show', $account) }}" class="service-review-thumb service-review-thumb-empty">
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M3 7h18M5 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2M6 11h12v8H6z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                    </a>
                                @endif
                                <div class="service-review-meta mt-1">{{ $imageMedia->count() }} {{ __('admin.business_account_images') }}</div>
                            </td>

                            <td>
                                <div class="service-review-title">{{ $displayName }}</div>
                                <div class="service-review-meta">
                                    <span>{{ __('admin.id') }}: {{ $account->id }}</span>
                                    <span>{{ __('admin.license_number') }}: {{ $account->license_number }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="service-review-title">{{ $account->user?->name ?: '-' }}</div>
                                <div class="service-review-meta">
                                    <span>{{ $account->user?->phone ?: '-' }}</span>
                                    <span>{{ $account->user?->email ?: '-' }}</span>
                                </div>
                            </td>

                            <td>{{ $account->city?->getTranslation('name', app()->getLocale()) ?: '-' }}</td>

                            <td>{{ $account->activityType?->getTranslation('name', app()->getLocale()) ?: '-' }}</td>

                            <td>
                                <span class="badge status-badge status-read">
                                    {{ $documentMedia->count() }}
                                </span>
                            </td>

                            <td>
                                <span class="badge status-badge status-{{ $account->status }}">
                                    {{ __('admin.' . $account->status) }}
                                </span>
                            </td>

                            <td>{{ optional($account->created_at)->format('Y-m-d H:i') }}</td>

                            <td>
                                <div class="table-actions table-actions-stack">
                                    <a href="{{ route('admin.business-accounts.show', $account) }}"
                                       class="btn btn-sm btn-action btn-action-view w-100">
                                        {{ __('admin.view') }}
                                    </a>
                                    @if($account->status === 'pending')
                                        @can('business-accounts.approve')
                                            <form method="POST"
                                                  action="{{ route('admin.business-accounts.approve', $account) }}"
                                                  class="w-100"
                                                  data-confirm="{{ __('admin.approve_business_account_confirmation') }}">
                                                @csrf
                                                @method('PATCH')
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
                        <x-admin.empty-state :message="__('admin.no_business_accounts_found')" :colspan="9" />
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $businessAccounts->links() }}
        </div>
    </x-admin.table-card>
</div>
@endsection
