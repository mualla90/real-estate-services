@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">Business Accounts</h3>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.business-accounts.index') }}">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Name or license number"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <select name="city_id" class="form-select">
                            <option value="">All</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected((string) request('city_id') === (string) $city->id)>
                                    {{ $city->getTranslation('name', app()->getLocale()) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Activity Type</label>
                        <select name="activity_type_id" class="form-select">
                            <option value="">All</option>
                            @foreach($activityTypes as $activityType)
                                <option value="{{ $activityType->id }}" @selected((string) request('activity_type_id') === (string) $activityType->id)>
                                    {{ $activityType->getTranslation('name', app()->getLocale()) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('admin.business-accounts.index') }}"
                           class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>User</th>
                        <th>City</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th width="120">Actions</th>
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
                                @if($account->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($account->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>

                            <td>{{ $account->created_at->format('Y-m-d') }}</td>

                            <td>
                                <a href="{{ route('admin.business-accounts.show', $account) }}"
                                   class="btn btn-sm btn-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No business accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $businessAccounts->links() }}
        </div>
    </div>

</div>
@endsection
