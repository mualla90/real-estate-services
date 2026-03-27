@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Admins</h3>

        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            Add Admin
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.admins.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Name or email"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>Email</th>
                        <th>Role</th>
                        <th>Active</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->roles->first()?->name ?? '-' }}</td>
                            <td>
                                @if($admin->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No admins found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $admins->links() }}
        </div>
    </div>

</div>
@endsection
