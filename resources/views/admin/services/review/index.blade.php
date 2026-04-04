@extends('layouts.admin')

@section('title', __('admin.service_review'))

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <strong>{{ __('admin.service_review') }}</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.services.review.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('admin.all') }}</option>
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

                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.business_account_id') }}</label>
                    <input type="text"
                           name="business_account_id"
                           class="form-control"
                           value="{{ request('business_account_id') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.category_id') }}</label>
                    <input type="text"
                           name="category_id"
                           class="form-control"
                           value="{{ request('category_id') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ __('admin.filter') }}
                    </button>

                    <a href="{{ route('admin.services.review.index') }}" class="btn btn-light">
                        {{ __('admin.reset') }}
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('admin.title') }}</th>
                            <th>{{ __('admin.business_account') }}</th>
                            <th>{{ __('admin.category') }}</th>
                            <th>{{ __('admin.city') }}</th>
                            <th>{{ __('admin.type') }}</th>
                            <th>{{ __('admin.price') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.active') }}</th>
                            <th>{{ __('admin.created_at') }}</th>
                            <th width="120">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>{{ $service->getTranslation('title', app()->getLocale(), false) ?? '-' }}</td>
                                <td>
                                    {{ $service->business_account_id }}
                                    @if($service->businessAccount)
                                        <br>
                                        <small>{{ $service->businessAccount->getTranslation('name', app()->getLocale(), false) ?? '-' }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($service->category)
                                        {{ $service->category->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($service->city)
                                        {{ $service->city->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ ucfirst($service->service_type) }}</td>
                                <td>{{ $service->price }} {{ $service->currency }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($service->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ __('admin.' . $service->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge bg-success">{{ __('admin.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $service->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.services.review.show', $service) }}"
                                       class="btn btn-sm btn-info">
                                        {{ __('admin.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">{{ __('admin.no_services_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
