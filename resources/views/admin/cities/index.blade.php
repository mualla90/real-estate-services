@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Cities</h3>

        @if(auth('admin')->user()?->can('cities.create'))
            <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                Add City
            </a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.cities.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search by city name"
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.cities.index') }}"
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
                        <th>Name (EN)</th>
                        <th>Name (AR)</th>
                        <th>Active</th>
                        <th>Sort Order</th>
                        <th>Created At</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cities as $city)
                        <tr>
                            <td>{{ $city->id }}</td>
                            <td>{{ $city->getTranslation('name', 'en') }}</td>
                            <td>{{ $city->getTranslation('name', 'ar') }}</td>
                            <td>
                                @if($city->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $city->sort_order }}</td>
                            <td>{{ $city->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if(auth('admin')->user()?->can('cities.update'))
                                    <a href="{{ route('admin.cities.edit', $city) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                @endif

                                @if(auth('admin')->user()?->can('cities.delete'))
                                    <form method="POST"
                                          action="{{ route('admin.cities.destroy', $city) }}"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this city?')">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No cities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $cities->links() }}
        </div>
    </div>

</div>
@endsection
