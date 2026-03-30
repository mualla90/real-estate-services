@extends('layouts.admin')

@section('title', 'Service Review')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <strong>Service Review</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.services.review.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Business Account ID</label>
                    <input type="text" name="business_account_id" class="form-control" value="{{ request('business_account_id') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Category ID</label>
                    <input type="text" name="category_id" class="form-control" value="{{ request('category_id') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.services.review.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title (EN)</th>
                            <th>Business Account</th>
                            <th>Category</th>
                            <th>City</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th>Created At</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>{{ $service->getTranslation('title', 'en', false) ?? '-' }}</td>
                                <td>
                                    #{{ $service->business_account_id }}
                                    @if($service->businessAccount)
                                        <br>
                                        <small>{{ $service->businessAccount->getTranslation('name', 'en', false) ?? '-' }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($service->category)
                                        {{ $service->category->getTranslation('name', 'en', false) ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($service->city)
                                        {{ $service->city->getTranslation('name', 'en', false) ?? '-' }}
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
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $service->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.services.review.show', $service) }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No services found.</td>
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
