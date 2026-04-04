@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">{{ __('admin.business_account_details') }}</h3>

    <div class="card">
        <div class="card-body">

            <p>
                <strong>{{ __('admin.name') }}:</strong>
                {{ $businessAccount->getTranslation('name', app()->getLocale()) }}
            </p>

            <p>
                <strong>{{ __('admin.license_number') }}:</strong>
                {{ $businessAccount->license_number }}
            </p>

            <p>
                <strong>{{ __('admin.user') }}:</strong>
                {{ $businessAccount->user->name }}
            </p>

            <p>
                <strong>{{ __('admin.city') }}:</strong>
                {{ $businessAccount->city?->getTranslation('name', app()->getLocale()) }}
            </p>

            <p>
                <strong>{{ __('admin.activity_type') }}:</strong>
                {{ $businessAccount->activityType?->getTranslation('name', app()->getLocale()) }}
            </p>

            <p>
                <strong>{{ __('admin.description') }}:</strong>
                {{ $businessAccount->getTranslation('description', app()->getLocale()) }}
            </p>

            <p>
                <strong>{{ __('admin.status') }}:</strong>
                @if($businessAccount->status === 'pending')
                    <span class="badge bg-warning">{{ __('admin.pending') }}</span>
                @elseif($businessAccount->status === 'approved')
                    <span class="badge bg-success">{{ __('admin.approved') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('admin.rejected') }}</span>
                @endif
            </p>

            <p>
                <strong>{{ __('admin.created_at') }}:</strong>
                {{ $businessAccount->created_at->format('Y-m-d') }}
            </p>

            @if($businessAccount->status === 'rejected' && $businessAccount->rejection_reason)
                <p>
                    <strong>{{ __('admin.rejection_reason') }}:</strong>
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
                        {{ __('admin.approve') }}
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
                               placeholder="{{ __('admin.enter_rejection_reason') }}"
                               required>
                    </div>

                    <button type="submit" class="btn btn-danger">
                        {{ __('admin.reject') }}
                    </button>
                </form>
            @endif

        </div>
    </div>

</div>
@endsection
