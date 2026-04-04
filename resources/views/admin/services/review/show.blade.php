@extends('layouts.admin')

@section('title', __('admin.service_review'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ __('admin.service_details') }}</strong>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="220">ID</th>
                                <td>{{ $service->id }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.business_account') }}</th>
                                <td>
                                    #{{ $service->business_account_id }}

                                    @if($service->businessAccount)
                                        <br>
                                        {{ $service->businessAccount->getTranslation('name', app()->getLocale(), false) ?? '-' }}
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.category') }}</th>
                                <td>{{ $service->category?->getTranslation('name', app()->getLocale(), false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.subcategory') }}</th>
                                <td>{{ $service->subcategory?->getTranslation('name', app()->getLocale(), false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.city') }}</th>
                                <td>{{ $service->city?->getTranslation('name', app()->getLocale(), false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.title') }}</th>
                                <td>{{ $service->getTranslation('title', app()->getLocale(), false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.description') }}</th>
                                <td>{{ $service->getTranslation('description', app()->getLocale(), false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.service_type') }}</th>
                                <td>{{ ucfirst($service->service_type) }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.price') }}</th>
                                <td>{{ $service->price }} {{ $service->currency }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.address') }}</th>
                                <td>{{ $service->address ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.latitude') }}</th>
                                <td>{{ $service->latitude ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.longitude') }}</th>
                                <td>{{ $service->longitude ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.status') }}</th>
                                <td>
                                    @php
                                        $statusBadgeClass = match($service->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $statusBadgeClass }}">
                                        {{ __('admin.' . $service->status) }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.active') }}</th>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge bg-success">{{ __('admin.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.rejection_reason') }}</th>
                                <td>{{ $service->rejection_reason ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.reviewed_by_admin') }}</th>
                                <td>{{ $service->reviewed_by_admin_id ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.reviewed_at') }}</th>
                                <td>{{ $service->reviewed_at?->format('Y-m-d H:i') ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.published_at') }}</th>
                                <td>{{ $service->published_at?->format('Y-m-d H:i') ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.average_rating') }}</th>
                                <td>{{ $service->average_rating }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.review_count') }}</th>
                                <td>{{ $service->review_count }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.views_count') }}</th>
                                <td>{{ $service->views_count }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.sort_order') }}</th>
                                <td>{{ $service->sort_order }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.created_at') }}</th>
                                <td>{{ $service->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('admin.updated_at') }}</th>
                                <td>{{ $service->updated_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ __('admin.review_actions') }}</strong>
                </div>

                <div class="card-body">
                    @if($service->status === 'pending')
                        @can('services.approve')
                            <form method="POST" action="{{ route('admin.services.approve', $service) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    {{ __('admin.approve_service') }}
                                </button>
                            </form>
                        @endcan

                        @can('services.reject')
                            <form method="POST" action="{{ route('admin.services.reject', $service) }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.rejection_reason') }}</label>
                                    <textarea name="rejection_reason" rows="5"
                                              class="form-control @error('rejection_reason') is-invalid @enderror">{{ old('rejection_reason') }}</textarea>

                                    @error('rejection_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-danger w-100">
                                    {{ __('admin.reject_service') }}
                                </button>
                            </form>
                        @endcan
                    @else
                        <div class="alert alert-info mb-0">
                            {{ __('admin.already_reviewed') }}
                        </div>
                    @endif
                </div>
            </div>

            @if($service->status === 'approved')
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>{{ __('admin.visibility_actions') }}</strong>
                    </div>

                    <div class="card-body">
                        @if($service->is_active)
                            @can('services.deactivate')
                                <form method="POST" action="{{ route('admin.services.deactivate', $service) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="btn btn-danger w-100">
                                        {{ __('admin.deactivate_service') }}
                                    </button>
                                </form>
                            @endcan
                        @else
                            @can('services.activate')
                                <form method="POST" action="{{ route('admin.services.activate', $service) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="btn btn-success w-100">
                                        {{ __('admin.activate_service') }}
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <strong>{{ __('admin.navigation') }}</strong>
                </div>

                <div class="card-body">
                    <a href="{{ route('admin.services.review.index') }}" class="btn btn-light w-100">
                        {{ __('admin.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
