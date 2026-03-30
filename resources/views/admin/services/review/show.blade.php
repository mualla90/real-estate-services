@extends('layouts.admin')

@section('title', 'Review Service')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Service Details</strong>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="220">ID</th>
                                <td>{{ $service->id }}</td>
                            </tr>

                            <tr>
                                <th>Business Account</th>
                                <td>
                                    #{{ $service->business_account_id }}
                                    @if($service->businessAccount)
                                        <br>
                                        <strong>EN:</strong> {{ $service->businessAccount->getTranslation('name', 'en', false) ?? '-' }}
                                        <br>
                                        <strong>AR:</strong> {{ $service->businessAccount->getTranslation('name', 'ar', false) ?? '-' }}
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>Category</th>
                                <td>
                                    {{ $service->category?->getTranslation('name', 'en', false) ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Subcategory</th>
                                <td>
                                    {{ $service->subcategory?->getTranslation('name', 'en', false) ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>City</th>
                                <td>
                                    {{ $service->city?->getTranslation('name', 'en', false) ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Title (EN)</th>
                                <td>{{ $service->getTranslation('title', 'en', false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Title (AR)</th>
                                <td>{{ $service->getTranslation('title', 'ar', false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Description (EN)</th>
                                <td>{{ $service->getTranslation('description', 'en', false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Description (AR)</th>
                                <td>{{ $service->getTranslation('description', 'ar', false) ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Service Type</th>
                                <td>{{ ucfirst($service->service_type) }}</td>
                            </tr>

                            <tr>
                                <th>Price</th>
                                <td>{{ $service->price }} {{ $service->currency }}</td>
                            </tr>

                            <tr>
                                <th>Address</th>
                                <td>{{ $service->address ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Latitude</th>
                                <td>{{ $service->latitude ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Longitude</th>
                                <td>{{ $service->longitude ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    @php
                                        $badgeClass = match($service->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>Rejection Reason</th>
                                <td>{{ $service->rejection_reason ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Reviewed By Admin ID</th>
                                <td>{{ $service->reviewed_by_admin_id ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Reviewed At</th>
                                <td>{{ $service->reviewed_at?->format('Y-m-d H:i') ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Published At</th>
                                <td>{{ $service->published_at?->format('Y-m-d H:i') ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Average Rating</th>
                                <td>{{ $service->average_rating }}</td>
                            </tr>

                            <tr>
                                <th>Review Count</th>
                                <td>{{ $service->review_count }}</td>
                            </tr>

                            <tr>
                                <th>Views Count</th>
                                <td>{{ $service->views_count }}</td>
                            </tr>

                            <tr>
                                <th>Is Active</th>
                                <td>{{ $service->is_active ? 'Yes' : 'No' }}</td>
                            </tr>

                            <tr>
                                <th>Sort Order</th>
                                <td>{{ $service->sort_order }}</td>
                            </tr>

                            <tr>
                                <th>Created At</th>
                                <td>{{ $service->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>

                            <tr>
                                <th>Updated At</th>
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
                    <strong>Review Actions</strong>
                </div>

                <div class="card-body">
                    @if($service->status === 'pending')
                        @can('services.approve')
                            <form method="POST" action="{{ route('admin.services.approve', $service) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    Approve Service
                                </button>
                            </form>
                        @endcan

                        @can('services.reject')
                            <form method="POST" action="{{ route('admin.services.reject', $service) }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                    <textarea
                                        name="rejection_reason"
                                        id="rejection_reason"
                                        rows="5"
                                        class="form-control @error('rejection_reason') is-invalid @enderror"
                                    >{{ old('rejection_reason') }}</textarea>

                                    @error('rejection_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-danger w-100">
                                    Reject Service
                                </button>
                            </form>
                        @endcan
                    @else
                        <div class="alert alert-info mb-0">
                            This service has already been reviewed.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong>Navigation</strong>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.services.review.index') }}" class="btn btn-light w-100">
                        Back to Review List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
