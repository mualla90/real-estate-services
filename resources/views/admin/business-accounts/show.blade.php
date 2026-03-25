@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">Business Account Details</h3>

    <div class="card">
        <div class="card-body">

            <p>
                <strong>Name:</strong>
                {{ $businessAccount->getTranslation('name', app()->getLocale()) }}
            </p>

            <p>
                <strong>License Number:</strong>
                {{ $businessAccount->license_number }}
            </p>

            <p>
                <strong>User:</strong>
                {{ $businessAccount->user->name }}
            </p>

            <p>
                <strong>City:</strong>
                {{ $businessAccount->city?->getTranslation('name', app()->getLocale()) }}
            </p>

            <p>
                <strong>Activity Type:</strong>
                {{ $businessAccount->activityType?->getTranslation('name', app()->getLocale()) }}
            </p>

            <p>
                <strong>Description:</strong>
                {{ $businessAccount->getTranslation('description', app()->getLocale()) }}
            </p>

            <p>
                <strong>Status:</strong>
                @if($businessAccount->status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                @elseif($businessAccount->status === 'approved')
                    <span class="badge bg-success">Approved</span>
                @else
                    <span class="badge bg-danger">Rejected</span>
                @endif
            </p>

            <p>
                <strong>Created At:</strong>
                {{ $businessAccount->created_at->format('Y-m-d') }}
            </p>

            @if($businessAccount->status === 'rejected' && $businessAccount->rejection_reason)
                <p>
                    <strong>Rejection Reason:</strong>
                    {{ $businessAccount->rejection_reason }}
                </p>
            @endif

            <hr>

            @if($businessAccount->status === 'pending')
                <form method="POST"
                      action="{{ route('admin.business-accounts.approve', $businessAccount) }}"
                      class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        Approve
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('admin.business-accounts.reject', $businessAccount) }}"
                      class="d-inline ms-2">
                    @csrf
                    @method('PATCH')

                    <div class="mb-2 mt-2">
                        <input type="text"
                               name="rejection_reason"
                               class="form-control"
                               placeholder="Enter rejection reason"
                               required>
                    </div>

                    <button type="submit" class="btn btn-danger">
                        Reject
                    </button>
                </form>
            @endif

        </div>
    </div>

</div>
@endsection
