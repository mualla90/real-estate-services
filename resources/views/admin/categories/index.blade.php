@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Categories</h3>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            Add Category
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Name or description"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>Description</th>
                        <th>Active</th>
                        <th>Sort Order</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->getTranslation('name', 'en') }}</td>
                            <td>{{ $category->getTranslation('name', 'ar') }}</td>
                            <td>{{ $category->getTranslation('description', app()->getLocale()) }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline">
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
                            <td colspan="7" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $categories->links() }}
        </div>
    </div>

</div>
@endsection
