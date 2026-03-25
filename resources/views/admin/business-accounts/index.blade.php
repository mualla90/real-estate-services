
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">Business Accounts</h3>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>User</th>
                        <th>City</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($businessAccounts as $account)
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
                    @endforeach
                </tbody>
            </table>

            {{ $businessAccounts->links() }}

        </div>
    </div>

</div>
@endsection
