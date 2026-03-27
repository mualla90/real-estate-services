@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Subcategories</h3>

        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
            Add Subcategory
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subcategories.index') }}">
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

                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->getTranslation('name', app()->getLocale()) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>Category</th>
                        <th>Active</th>
                        <th>Sort Order</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subcategories as $subcategory)
                        <tr>
                            <td>{{ $subcategory->id }}</td>
                            <td>{{ $subcategory->getTranslation('name', 'en') }}</td>
                            <td>{{ $subcategory->getTranslation('name', 'ar') }}</td>
                            <td>{{ $subcategory->category?->getTranslation('name', app()->getLocale()) }}</td>
                            <td>
                                @if($subcategory->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $subcategory->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}" class="d-inline">
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
                            <td colspan="7" class="text-center">No subcategories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $subcategories->links() }}
        </div>
    </div>

</div>
@endsection
