@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">

    <x-admin.page-header :title="__('admin.business_accounts')" icon-name="business" />

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.business-accounts.index') }}">
            <div class="row g-3">

                <div class="col-md-3">
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

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('admin.filter') }}
                    </button>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('admin.business-accounts.index') }}"
                       class="btn btn-outline-secondary w-100">
                        {{ __('admin.reset') }}
                    </a>
                </div>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table-card>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.user') }}</th>
                    <th>{{ __('admin.city') }}</th>
                    <th>{{ __('admin.activity') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.created_at') }}</th>
                    <th width="120">{{ __('admin.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($businessAccounts as $account)
                    <tr>
                        <td>{{ $account->id }}</td>

                        <td>
                            {{ $account->getTranslation('name', app()->getLocale()) }}
                        </td>

                        <td>{{ $account->user->name }}</td>

                        <td>
                            {{ $account->city?->getTranslation('name', app()->getLocale()) }}
                        </td>

                        <td>
                            {{ $account->activityType?->getTranslation('name', app()->getLocale()) }}
                        </td>

                        <td>
                            <span class="badge status-badge status-{{ $account->status }}">{{ __('admin.' . $account->status) }}</span>
                        </td>

                        <td>{{ $account->created_at->format('Y-m-d') }}</td>

                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.business-accounts.show', $account) }}"
                                   class="btn btn-sm btn-action btn-action-view">
                                    {{ __('admin.view') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state :message="__('admin.no_business_accounts_found')" :colspan="8" />
                @endforelse
            </tbody>
        </table>

        {{ $businessAccounts->links() }}
    </x-admin.table-card>

</div>
@endsection
