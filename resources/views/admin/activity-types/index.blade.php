@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Activity Types</h3>

        <a href="{{ route('admin.activity-types.create') }}" class="btn btn-primary">
            Add Activity Type
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-types.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="English or Arabic name"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.activity-types.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>English Name</th>
                        <th>Arabic Name</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($activityTypes as $activityType)
                        <tr>
                            <td>{{ $activityType->id }}</td>
                            <td>{{ $activityType->getTranslation('name', 'en') }}</td>
                            <td>{{ $activityType->getTranslation('name', 'ar') }}</td>
                            <td>
                                @if($activityType->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $activityType->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.activity-types.edit', $activityType) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.activity-types.destroy', $activityType) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No activity types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $activityTypes->links() }}
        </div>
    </div>

</div>
@endsection
